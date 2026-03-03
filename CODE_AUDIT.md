# Code Review & Audit Report

**Application:** Document Archive System  
**Stack:** Laravel 11, PHP, MySQL/MariaDB, Blade/Tailwind CSS  
**Date:** 2026-03-03  
**Reviewer:** Senior Fullstack Engineer

---

## Table of Contents

- [Critical Issues](#critical-issues)
- [High Severity Issues](#high-severity-issues)
- [Medium Severity Issues](#medium-severity-issues)
- [Low Severity Issues](#low-severity-issues)

---

## Critical Issues

### Backend

---

#### B-CRIT-1 · Roles Middleware Is a Pass-Through (No-Op)

**File:** `app/Http/Middleware/Roles.php` — Lines 16–18

```php
public function handle(Request $request, Closure $next): Response
{
    return $next($request);  // Does nothing — every request passes through
}
```

Routes registered with `middleware(['auth', 'role:super-admin'])` (e.g., the entire `/admin` prefix group in `routes/web.php` lines 73–88) do **not** enforce role checks. Any authenticated user can reach admin-only endpoints such as subscription management, plan management, and the admin dashboard.

**Fix:** Implement the role check inside `handle()`, e.g., using `auth()->user()->hasRole($role)` or delegate to Spatie's built-in `role` middleware alias.

---

#### B-CRIT-2 · No Authorization on Document CRUD Operations

**File:** `app/Http/Controllers/DocumentController.php`

The constructor permission middleware is commented out (lines 27–33) and no `$this->authorize()` or `Gate::` calls exist in any of the document methods (`show`, `edit`, `update`, `destroy`, `downloadFile`). Any authenticated user can view, edit, delete, or download any document in the entire system by guessing or enumerating document IDs.

```php
// constructor middleware is commented out — never enforced
// public function __construct()
// {
//     $this->middleware('permission:document-list|...', [...]);
// }
```

**Fix:** Uncomment or re-implement the constructor middleware, or add `$this->authorize('action', $document)` in each method using Laravel's policy system.

---

#### B-CRIT-3 · Horizontal Privilege Escalation in User Management

**File:** `app/Http/Controllers/UserController.php` — Lines 171–191, 196–240

`show()`, `edit()`, and `update()` accept a raw `$id` and call `User::find($id)` or `User::findOrFail($id)` without checking whether the authenticated user has any authority over that user. An attacker can change any user's name, email, roles, offices, and company by submitting a PUT request to `/users/{any_id}`.

```php
public function update(Request $request, $id) {
    $user = User::findOrFail($id);  // No ownership or company-scope check
    $input = $request->all();
    ...
}
```

**Fix:** Verify that the target user belongs to the same company as the currently authenticated user before allowing edits.

---

#### B-CRIT-4 · No Ownership Verification in Company Update/Delete

**File:** `app/Http/Controllers/CompanyController.php` — Lines 91–111

`update()` and `destroy()` use Laravel's route-model binding to load any `CompanyAccount` by ID, then immediately update or delete it with no check that the authenticated user owns or manages it.

```php
public function update(Request $request, CompanyAccount $company)
{
    $company->update($validated);  // No authorization check
    ...
}

public function destroy(CompanyAccount $company)
{
    $company->delete();  // No authorization check
    ...
}
```

**Fix:** Add `$this->authorize('update', $company)` / `$this->authorize('delete', $company)` or manually verify `$company->user_id === auth()->id()`.

---

#### B-CRIT-5 · `shell_exec` with Unsanitized File Path (Command Injection)

**File:** `app/Http/Controllers/DocumentController.php` — Lines 508–514

```php
$command = env('POPPLER_PATH') . ' '
    . str_replace('/', '\\', $path)
    . ' '
    . str_replace('/', '\\', $outputFile);
$output = shell_exec($command);
```

`$path` is derived from user-uploaded content. A filename containing shell metacharacters (e.g., `; rm -rf /`) could execute arbitrary commands on the server. `str_replace('/', '\\')` provides no meaningful sanitization against shell injection.

**Fix:** Use `escapeshellarg()` on each argument, or better, replace `shell_exec` with the Symfony Process component which does not invoke a shell.

---

#### B-CRIT-6 · Payment Gateway Callback Method Is Not Implemented

**File:** `app/Http/Controllers/PaymentController.php` (method absent) / `routes/web.php` — Line 235

```php
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
```

`handleCallback()` does not exist in `PaymentController`. Hitting this route causes a fatal `BadMethodCallException`. More critically, payment webhooks from PayMongo are routed to a dead endpoint, meaning payment confirmations are never processed automatically, and any attacker aware of this can probe the application for errors.

**Fix:** Implement `handleCallback()` to verify the PayMongo webhook signature and process the payment event; until then, remove the route to avoid exposing the unhandled error.

---

#### B-CRIT-7 · Payment Confirmation Is Simulated — No Real Gateway Verification

**File:** `app/Http/Controllers/PaymentController.php` — Lines 283–291

```php
// Here you would integrate with your payment gateway
// For this example, we'll simulate a successful payment
$payment->update(['status' => 'successful']);
$subscription->update(['status' => 'active']);
```

`PaymentController::store()` marks every payment as successful without contacting the payment gateway. Any authenticated user can activate a subscription for any plan at no cost by submitting a POST to `/payments/{plan}` with any payment method string.

**Fix:** Remove the simulation; integrate with the actual gateway and only mark payment successful after receiving a confirmed payment event.

---

#### B-CRIT-8 · Company-Admin Sees All Documents Across All Companies

**File:** `app/Http/Controllers/DocumentController.php` — Lines 40–43, 108–109

```php
if (auth()->user()->hasRole('company-admin')) {
    $documents = Document::with([...])->latest()->paginate(5);
    // No company filter — returns ALL documents system-wide
}
```

The same unscoped query exists in `showArchive()`, `showReleased()`, `showPending()`, and `showComplete()`. A company administrator can read every document uploaded by every company in the system.

**Fix:** Scope queries to the authenticated user's company, e.g., by joining through the user's company offices or adding a `company_id` column to the documents table.

---

### Frontend

---

#### F-CRIT-1 · Business Logic and Database Queries Inside Blade Templates

**File:** `resources/views/documents/create.blade.php` — Lines 3–13

```php
@php
use App\Models\Office;
$currentUserCompany = auth()->user()->companies()->first();
$originatingOfficeId = auth()->user()->offices->first()->id ?? null;
$offices = $currentUserCompany
    ? Office::where('company_id', $currentUserCompany->id)
        ->where('id', '!=', $originatingOfficeId)
        ->get()
    : collect();
@endphp
```

Performing database queries in the view layer violates MVC separation of concerns. It also means the logic is invisible to the controller, untested, and bypasses any caching or authorization layer in the controller.

**Fix:** Move all data retrieval into the controller and pass the result to the view via `compact()`.

---

## High Severity Issues

### Backend

---

#### B-HIGH-1 · Subscription Cancel/Activate Has No Ownership Check

**File:** `app/Http/Controllers/SubscriptionController.php` — Lines 58–70

```php
public function cancel(CompanySubscription $subscription)
{
    $subscription->update(['status' => 'canceled']);  // No auth check
    return response()->json(['message' => 'Subscription canceled successfully']);
}

public function activate(CompanySubscription $subscription)
{
    $subscription->update(['status' => 'active']);  // No auth check
    return response()->json(['message' => 'Subscription activated successfully']);
}
```

Any authenticated user can cancel or activate any company's subscription by sending requests to `/subscriptions/{id}/cancel` or `/subscriptions/{id}/activate`.

**Fix:** Verify that the subscription belongs to a company the authenticated user manages before allowing the status change.

---

#### B-HIGH-2 · Main Document Upload Accepts Any File Type

**File:** `app/Http/Controllers/DocumentController.php` — Line 144

```php
'upload' => 'required|file',  // No MIME type or extension restriction
```

Contrast with the attachment rule on line 145 which restricts to `mimes:jpeg,png,jpg,gif,pdf,docx`. The main document file accepts PHP scripts, executables, or any other file type. Combined with storage in the public disk, an attacker could upload a PHP file and access it at a predictable URL to execute server-side code.

**Fix:** Add a MIME-type whitelist, e.g., `'upload' => 'required|file|mimes:pdf,docx,doc,jpeg,png,jpg|max:20480'`.

---

#### B-HIGH-3 · Uploaded Documents Are Publicly Accessible Without Authentication

**File:** `app/Http/Controllers/DocumentController.php` — Line 158, `config/filesystems.php` — Lines 40–46

```php
$filePath = $file->storeAs($companyPath . '/documents', $fileName, 'public');
```

Files stored on the `public` disk are accessible at `{APP_URL}/storage/{path}` by anyone with the URL, regardless of authentication or authorization status. Sensitive documents can be accessed by unauthenticated third parties if they know or guess the path.

**Fix:** Store documents on the `local` (private) disk and serve them through a controller method that performs an authorization check before streaming the file.

---

#### B-HIGH-4 · Predictable Upload File Names

**File:** `app/Http/Controllers/DocumentController.php` — Lines 156, 197, 634, 651

```php
$fileName = time() . '_' . $file->getClientOriginalName();
```

Using a Unix timestamp prefix makes file paths predictable. An attacker who knows a document was uploaded at approximately a known time can enumerate file names by iterating over a small range of timestamps combined with common or known original filenames.

**Fix:** Use `Str::uuid()` or `Str::random(32)` for file names and store the original name separately in the database.

---

#### B-HIGH-5 · Weak Tracking Number Generation Using `rand()`

**File:** `app/Http/Controllers/DocumentController.php` — Lines 757–759

```php
$randomString .= $characters[rand(0, $charactersLength - 1)];
$randomString .= $characters[rand(0, $charactersLength - 1)];
```

PHP's `rand()` function is not cryptographically secure. With a known prefix format (`{OFFICE}-{CATEGORY}-{RANDOM}-{YEAR}`) and predictable year component, tracking numbers could be predicted or enumerated. This also applies to `uniqid()` used in `generateTransactionReference()`.

**Fix:** Replace `rand()` with `random_int()` and `uniqid()` with `bin2hex(random_bytes(8))` or use `Str::random()`.

---

#### B-HIGH-6 · Workflow Approve/Reject/Receive Lack Recipient Verification

**File:** `app/Http/Controllers/DocumentWorkflowController.php` — Lines 134–226

`approveWorkflow()`, `rejectWorkflow()`, and `receiveWorkflow()` load any `DocumentWorkflow` by ID without verifying that the authenticated user is the intended recipient of that workflow step. Any user can approve or reject any workflow step.

**Fix:** Add a check that `$workflow->recipient_id === auth()->id()` before allowing the action.

---

#### B-HIGH-7 · `processAutoRenewals` Is Accessible via HTTP Without Authentication

**File:** `app/Http/Controllers/SubscriptionController.php` — Lines 166–190 (called from `routes/web.php`)

`processAutoRenewals()` is a batch database-update operation that should be called only by an internal scheduler. If it is accessible over HTTP without authentication, any external user could trigger mass subscription renewals.

**Fix:** Move this to a Laravel Artisan command (`php artisan subscriptions:renew`) and schedule it with `Schedule::command(...)` in the console kernel. Remove the HTTP route.

---

#### B-HIGH-8 · Email Verification Not Enforced

**File:** `app/Models/User.php` — Line 5

```php
// use Illuminate\Contracts\Auth\MustVerifyEmail;
```

The `MustVerifyEmail` contract is commented out. Users can register with any email address and immediately access all application features without confirming they own the email. This allows account creation with other people's email addresses and reduces the reliability of email-based notifications.

**Fix:** Uncomment `MustVerifyEmail` and ensure protected routes use the `verified` middleware where appropriate.

---

### Frontend

---

#### F-HIGH-1 · Typo in Attachment Validation Key — MIME Check Never Applied

**File:** `app/Http/Controllers/DocumentController.php` — Line 145

```php
'attachements.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,docx|max:10240',
// ^^ typo: 'attachements' — the request key is 'attachments'
```

Because of the misspelling, the MIME-type and size validation rules for attachments are never evaluated. Any file type and any file size can be uploaded as an attachment.

**Fix:** Correct the key to `'attachments.*'`.

---

#### F-HIGH-2 · Raw Exception Messages Disclosed to Users

**File:** `app/Http/Controllers/DocumentController.php` — Lines 234–238; multiple controllers

```php
return redirect()->back()
    ->with('error', 'Error processing document: ' . $e->getMessage())
    ->withInput();
```

Raw exception messages can leak internal file paths, class names, SQL errors, and other implementation details that aid an attacker in crafting targeted exploits.

**Fix:** Log the full exception with `\Log::error($e)` and return a generic user-facing message such as `'An unexpected error occurred. Please try again.'`.

---

### Networking

---

#### N-HIGH-1 · No Webhook Signature Verification on Payment Callback

**File:** `routes/web.php` — Line 235; `app/Http/Controllers/PaymentController.php`

The `/payment/callback` route accepts any inbound POST/GET request without verifying that it originates from PayMongo. An attacker can forge a payment success notification by sending a crafted request to this endpoint, potentially activating subscriptions without paying.

**Fix:** Validate the `Paymongo-Signature` header against the raw request body using the webhook secret before processing any payment event.

---

#### N-HIGH-2 · Sensitive Payment Reference Exposed in URL Query String

**File:** `app/Http/Controllers/PaymentController.php` — Lines 178–182

```php
$referenceNumber = $request->query('reference');
$payment = SubscriptionPayment::with([...])
    ->where('transaction_reference', $referenceNumber)
    ->firstOrFail();
```

Transaction reference numbers appear in the URL (e.g., `/payment/success?reference=TXN-...`). URLs are stored in browser history, server access logs, proxy logs, and referrer headers — exposing references to third-party services included on the page.

**Fix:** Store the reference number in the session instead of passing it as a query parameter.

---

## Medium Severity Issues

### Backend

---

#### B-MED-1 · N+1 Query Problem in Document Listing

**File:** `app/Http/Controllers/DocumentController.php` — Lines 58–101

The `index()` method paginates documents and then, inside a `foreach` loop, issues two additional queries per document to find the maximum `step_order` and retrieve the corresponding workflow records.

```php
foreach ($documents as $doc) {
    $maxStepOrder = DocumentWorkflow::where('document_id', $doc->id)->max('step_order');
    $topWorkflows = DocumentWorkflow::where('document_id', $doc->id)
                        ->where('step_order', $maxStepOrder)->get();
    ...
}
```

With 5 documents per page this is 10 extra queries minimum; as the page size grows the performance degrades linearly.

**Fix:** Eager-load workflows with a constrained relationship or use a single query with a subquery to retrieve the highest-step workflows for all documents at once.

---

#### B-MED-2 · Duplicate Subscription Route Declarations

**File:** `routes/web.php` — Lines 60–64 and Lines 227–230

Subscription routes are declared twice: once inside an `auth` middleware group and again outside any explicit middleware group (but inside a broader `auth` group from line 90). The duplicated routes may conflict and the later definition silently overrides the earlier one, potentially stripping intended middleware.

**Fix:** Remove the duplicate declarations at lines 227–230 and keep only the definitions inside the appropriate middleware group.

---

#### B-MED-3 · No Pagination on Admin Subscription Listing

**File:** `app/Http/Controllers/SubscriptionController.php` — Lines 19–22

```php
public function indexAdmin()
{
    $subscriptions = CompanySubscription::with(['company', 'plan'])->get();
    // Loads all records into memory
}
```

As the number of companies and subscriptions grows, this query will load the entire table into memory, causing slow responses and potential out-of-memory errors.

**Fix:** Replace `->get()` with `->paginate(20)` and update the view to render pagination links.

---

#### B-MED-4 · Hardcoded Office IDs Override User Input

**File:** `app/Http/Controllers/DocumentController.php` — Lines 134–135

```php
$request->from_office = 1;
$request->to_office = 2;
```

These assignments unconditionally overwrite whatever `from_office` and `to_office` values the user provides. Combined with the `from_office` required-exists validation on line 142, documents are always recorded as originating from office ID 1 regardless of the user's actual office.

**Fix:** Remove the hardcoded assignments and derive the originating office from the authenticated user's office, or remove the validation requirement if the value is always inferred server-side.

---

#### B-MED-5 · `env()` Called Directly in Application Code

**File:** `app/Http/Controllers/DocumentController.php` — Lines 501, 509

```php
$pdfContent = (new Pdf(env('POPPLER_PATH')))->...
$command = env('POPPLER_PATH') . ' ' . ...
```

Calling `env()` directly in application code (outside of `config/` files) returns `null` when the configuration cache is active (`php artisan config:cache`), which is the recommended production setup. This causes silent failures in PDF processing.

**Fix:** Create a config entry (e.g., `config('services.poppler.path')`) and access it via `config()`.

---

#### B-MED-6 · `findOrFail` with Null-Coalesce Logic Bug

**File:** `app/Http/Controllers/DocumentController.php` — Line 728

```php
$document = Document::findOrFail($id) ?? $document = DocumentAttachment::findOrFail($id);
```

`findOrFail()` throws a `ModelNotFoundException` rather than returning `null`, so the `??` (null-coalesce) operator never triggers the fallback to `DocumentAttachment`. If the `$id` does not correspond to a `Document`, the code throws an unhandled exception rather than gracefully checking `DocumentAttachment`.

**Fix:** Use a try/catch block or `find()` followed by a null check.

---

#### B-MED-7 · Base64 Image Upload Without Content Validation

**File:** `app/Http/Controllers/DocumentController.php` — Lines 707–722

```php
$data = $request->input('image');
[$type, $data] = explode(';', $data);
[, $data] = explode(',', $data);
$data = base64_decode($data);
$filename = 'uploads/' . uniqid() . '.png';
Storage::put($filename, $data);
```

The endpoint accepts any base64-encoded data, blindly decodes it, and stores it as `.png`. There is no validation that the decoded content is actually an image, no MIME-type check, and no file size limit. An attacker could use this endpoint to store arbitrary binary content on the server.

**Fix:** Validate the decoded data using PHP's `getimagesizefromstring()` or a library like Intervention Image before saving.

---

#### B-MED-8 · Race Condition When Retrieving Newly Created Company

**File:** `app/Http/Controllers/CompanyController.php` — Line 65

```php
CompanyAccount::create($validated);
$company = CompanyAccount::latest()->first();  // Race condition
$company->addresses()->create($addressValidated);
```

In a concurrent environment, `CompanyAccount::latest()->first()` may return a different company created by another request in the same instant. The address would then be attached to the wrong company.

**Fix:** Use the return value of `create()` directly: `$company = CompanyAccount::create($validated);`.

---

#### B-MED-9 · Plan Management Routes Not Restricted to Super Admin

**File:** `routes/web.php` — Lines 217–225

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
});
```

Plan create/update/delete operations are protected only by `auth`, not `role:super-admin`. Any authenticated user can create, modify, or delete subscription plans.

**Fix:** Move these routes inside the `role:super-admin` middleware group or add role checks inside the controller methods.

---

### Frontend

---

#### F-MED-1 · Missing Accessible Labels on Interactive UI Elements

Several action buttons in document views (approve, reject, receive) rely solely on icon SVGs without screen-reader-accessible text or `aria-label` attributes, making the application unusable for visually impaired users.

**Fix:** Add `aria-label` attributes or visually hidden `<span>` text to icon-only buttons.

---

#### F-MED-2 · Inconsistent Validation Error Feedback

Some forms redirect back with generic flash messages while others display per-field validation errors. Users experience inconsistent feedback when input is invalid, leading to confusion about which fields need correction.

**Fix:** Standardize on using `$errors->get('field_name')` per field across all form templates.

---

### Networking

---

#### N-MED-1 · HTTP Client Instantiated Inline Without Timeout Configuration

**File:** `app/Http/Controllers/PaymentController.php` — Lines 44, 76, 212

```php
$client = new \GuzzleHttp\Client();
```

A new `GuzzleHttp\Client` instance is created on every request with no timeout, connect timeout, or retry configuration. If the PayMongo API is slow or unresponsive, the request will hang indefinitely, blocking the PHP-FPM worker and potentially exhausting the server's request pool.

**Fix:** Configure the client with `timeout` and `connect_timeout` options, or inject a pre-configured client via the service container.

---

#### N-MED-2 · No Rate Limiting on API Endpoints

**File:** `routes/web.php` — Line 94 (`/users/api/users`)

The internal API endpoint for fetching users by office (`GET /users/api/users`) is not rate-limited. It is accessible to any authenticated user and can be used to enumerate all users in a given office through repeated requests.

**Fix:** Apply Laravel's `throttle` middleware to API-style endpoints, e.g., `->middleware('throttle:60,1')`.

---

## Low Severity Issues

### Backend

---

#### B-LOW-1 · `Notifiable` Trait Imported Twice in User Model

**File:** `app/Models/User.php` — Lines 16, 18

```php
use Notifiable;
use HasFactory, HasRoles, Notifiable, SoftDeletes;
```

`Notifiable` is listed on a standalone line and again in the compound `use` statement. While PHP silently deduplicates trait uses, this is dead code that signals a maintenance oversight.

**Fix:** Remove the standalone `use Notifiable;` line.

---

#### B-LOW-2 · `BackupController` References a Non-Existent View

**File:** `app/Http/Controllers/BackupController.php` — Line 15

```php
return view("backup.index");
```

There is no `resources/views/backup/` directory or `index.blade.php` file. Accessing the backup route will throw a `View [backup.index] not found` exception.

**Fix:** Create the missing view or remove the controller and its route until the backup feature is implemented.

---

#### B-LOW-3 · `storeFile` Helper Method Has No Implementation

**File:** `app/Http/Controllers/DocumentWorkflowController.php` — Lines 196–199

```php
private function storeFile($file, $company_id)
{
    Storage::disk('local')->put(date('mYd'), 'Contents');  // Does nothing useful
}
```

This method is never called and contains no real logic. It is dead code.

**Fix:** Remove the method or implement it properly.

---

#### B-LOW-4 · Commented-Out Permission Middleware in DocumentController

**File:** `app/Http/Controllers/DocumentController.php` — Lines 27–33

```php
// public function __construct()
// {
//     $this->middleware('permission:document-list|...', [...]);
// }
```

Leaving commented-out security code in production source suggests the authorization layer is partially incomplete. This increases the risk of accidentally deploying without protections.

**Fix:** Either implement and enable the middleware or remove the commented block and use Laravel Policies instead.

---

#### B-LOW-5 · Inconsistent `DocumentAudit::logDocumentAction` Signature

**File:** `app/Http/Controllers/DocumentWorkflowController.php` — Lines 57–64 vs. Lines 151–157

The method is called with different argument types: sometimes passing a `Document` object (line 34 in `createWorkflow()`), sometimes passing a document ID integer. This inconsistency will cause errors if the method signature expects one type but receives the other.

**Fix:** Normalize the signature to always accept a document ID and update all call sites accordingly.

---

#### B-LOW-6 · `CompanyAccount::addresses()` Relationship Name Inconsistency

**File:** `app/Models/CompanyAccount.php` — Lines 36–39

The model defines two relationship methods: `address()` (returns `HasMany`) and no `addresses()` method. However, `CompanyController::store()` calls `$company->addresses()->create(...)`. This is likely a naming inconsistency that will produce a `BadMethodCallException` at runtime.

**Fix:** Rename `address()` to `addresses()` and update all references.

---

### Frontend

---

#### F-LOW-1 · Database Queries Inside View `@php` Blocks (Multiple Views)

Beyond `create.blade.php`, other Blade views contain `@php` blocks that call Eloquent models directly. This pattern scatters data-access logic throughout the presentation layer, making it hard to test, cache, or audit.

**Fix:** Consistently move all data retrieval to the controller and pass results to views as variables.

---

#### F-LOW-2 · Commented-Out Route Definitions Left in Route File

**File:** `routes/web.php` — Lines 181–184

```php
// Route::get('/{document}/status', [DocumentController::class, 'confirmReleased'])...
// Route::get('/{document}/{status}', [DocumentController::class, 'changeStatus'])...
// Route::put('/{document}/{status}', [DocumentController::class, 'changeStatus'])...
```

Commented-out routes suggest unfinished or removed features. Keeping them in the codebase creates confusion about which endpoints exist and whether associated controller methods are still expected to be present.

**Fix:** Remove commented-out routes. If the feature is planned, track it in an issue rather than in source code comments.

---

### Networking

---

#### N-LOW-1 · PayMongo Payment Amount Multiplied by 100 Twice

**File:** `app/Http/Controllers/PaymentController.php` — Lines 49–54

```php
$price = $plan->price * 100;   // Converts to cents
$body = [
    'data' => [
        'attributes' => [
            'amount' => $price * 100,  // Multiplies by 100 again — 10,000x original price
```

The plan price is converted to the smallest currency unit (cents/centavos) and then multiplied by 100 a second time. A ₱100.00 plan would result in a charge request of ₱1,000,000.00.

**Fix:** Calculate the final amount once: `$amount = $plan->price * 100;` and use `$amount` directly in the request body.

---

#### N-LOW-2 · No HTTPS Enforcement at Application Level

The application does not enforce HTTPS via middleware or HTTP Strict Transport Security (HSTS) headers. If deployed without web-server-level redirect rules, sessions, authentication tokens, and document content can be transmitted in plaintext.

**Fix:** Add `\Illuminate\Http\Middleware\SetCacheHeaders` or a custom middleware to set HSTS headers, and ensure `APP_URL` uses `https://` so Laravel generates secure URLs and sets the session cookie's `secure` flag.

---

#### N-LOW-3 · External Payment API URL Hardcoded Rather Than Configured

**File:** `app/Http/Controllers/PaymentController.php` — Lines 60, 78, 213

```php
$client->request('POST', 'https://api.paymongo.com/v1/links', [...]);
```

The PayMongo API base URL is hardcoded in three places. Switching to a sandbox environment, a different payment provider, or a different API version requires modifying multiple controller files.

**Fix:** Extract the base URL to `config/services.php` as `services.paymongo.base_url` and reference it via `config()`.

---

*End of audit report.*
