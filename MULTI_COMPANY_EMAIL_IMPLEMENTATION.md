# Multi-Company Email Support Implementation

**Date:** April 19, 2026  
**Status:** ✅ Complete  
**Feature:** Users can exist across multiple companies with the same email address

---

## Overview

This implementation allows users with the same email to be part of multiple companies. When such users log in, they are presented with a company selection screen to choose which company they want to access.

## Key Features

### ✅ Company Selection During Login
- Users with emails in multiple companies see a company selection screen
- Users with email in single company log in normally (no extra step)
- Password verified once, applies to all company associations

### ✅ Reuse Existing Users on Invitation
- When inviting a user with an existing email to a new company:
  - System reuses the existing user account
  - Adds company association via `company_users` pivot table
  - User can now access both companies with same credentials

### ✅ Company-Scoped Email Uniqueness
- Email uniqueness enforced within each company (not globally)
- Cannot add same email twice to same company
- Custom validation rule: `UniqueEmailInCompany`

### ✅ Session-Based Company Context
- Current company stored in `session('current_company_id')`
- User model helper methods for company context management
- Company context persists across page loads

---

## Files Created

### 1. Custom Validation Rule
**File:** `app/Rules/UniqueEmailInCompany.php`
- Validates email uniqueness within a specific company
- Used in user creation and update flows
- Accepts company ID and optional user ID to ignore (for updates)

### 2. Company Selection Controller
**File:** `app/Http/Controllers/Auth/CompanySelectionController.php`
- `show()` - Displays company selection page
- `authenticate()` - Completes login with selected company
- `authenticateWithCompany()` - Helper method to finalize authentication

### 3. Company Selection View
**File:** `resources/views/auth/select-company.blade.php`
- Beautiful UI showing all companies user belongs to
- Radio button selection
- Shows company name, registered name, and email

### 4. Implementation Documentation
**File:** `MULTI_COMPANY_EMAIL_IMPLEMENTATION.md` (this file)

---

## Files Modified

### 1. User Model
**File:** `app/Models/User.php`

Added methods:
- `getCurrentCompany()` - Get current company from session
- `getCurrentCompanyId()` - Get current company ID
- `hasMultipleCompanies()` - Check if user belongs to multiple companies
- `switchCompany($companyId)` - Switch company context

### 2. LoginRequest
**File:** `app/Http/Requests/Auth/LoginRequest.php`

Modified `authenticate()` method:
- Check if user belongs to multiple companies
- If yes: Store credentials in session and throw special exception
- If no: Continue with normal authentication
- Store company context in session

### 3. AuthenticatedSessionController
**File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Modified `store()` method:
- Catch special validation exception for company selection
- Redirect to company selection page when needed

### 4. RegisteredUserController
**File:** `app/Http/Controllers/Auth/RegisteredUserController.php`

Updated validation:
- Custom closure validation to check if email already exists
- Registration still requires unique email (creates new company)

### 5. UserController
**File:** `app/Http/Controllers/UserController.php`

**Store Method (User Invitation):**
- Check if user with email already exists
- If exists: Reuse account, add to company
- If not: Create new user
- Use `UniqueEmailInCompany` validation rule

**Update Method:**
- Use `UniqueEmailInCompany` validation rule for email changes
- Get company ID from current session context

### 6. Routes
**File:** `routes/auth.php`

Added routes:
- `GET /login/select-company` - Display company selection
- `POST /login/select-company` - Authenticate with selected company

---

## Database Schema

No new migrations needed! The database already had:
- Email uniqueness removed from `users` table (existing migration)
- `company_users` pivot table for many-to-many relationship

---

## How It Works

### Login Flow

```
1. User enters email + password
   ↓
2. System checks how many companies user belongs to
   ↓
3a. ONE company → Login normally, set company context
   ↓
3b. MULTIPLE companies → Redirect to company selection
   ↓
4. User selects company
   ↓
5. System verifies user has access to selected company
   ↓
6. Login completes, company context stored in session
```

### User Invitation Flow

```
1. Admin invites user by email
   ↓
2. System checks if email already exists
   ↓
3a. Email NOT exists → Create new user + add to company
   ↓
3b. Email EXISTS → Reuse user + add to company
   ↓
4. Check if user already in this company
   ↓
5a. Already in company → Show error
   ↓
5b. Not in company → Create company_users record
   ↓
6. User can now access both companies
```

### Registration Flow

```
1. User registers with email
   ↓
2. System checks if email already exists
   ↓
3a. Email EXISTS → Show error (must be unique for registration)
   ↓
3b. Email NOT exists → Create user + create company
   ↓
4. User logs in to their new company
```

---

## Usage Examples

### For End Users

**Login with Multiple Companies:**
1. Go to login page
2. Enter email and password
3. Select your company from the list
4. Click "Continue to Dashboard"

**Accessing Different Companies:**
- Each company is a separate context
- Currently: Log out and log in again, select different company
- Future: Add company switcher in UI

### For Administrators

**Inviting Existing User to Your Company:**
1. Go to Users → Create User
2. Enter email of existing user
3. System will reuse account and add to your company
4. User receives notification (logged)

**Inviting New User:**
1. Same flow as before
2. System creates new account
3. User receives invitation email with temporary password

---

## Session Management

### Company Context Storage

```php
// Stored in session after login
session('current_company_id') // Integer: ID of current company

// Access via User model
auth()->user()->getCurrentCompany()      // CompanyAccount model
auth()->user()->getCurrentCompanyId()    // Integer
auth()->user()->hasMultipleCompanies()   // Boolean
```

### Switching Companies

```php
// In controller
if (auth()->user()->switchCompany($newCompanyId)) {
    return redirect()->route('dashboard');
}
```

---

## Validation Rules

### UniqueEmailInCompany Rule

```php
use App\Rules\UniqueEmailInCompany;

// For new users
'email' => ['required', 'email', new UniqueEmailInCompany($companyId)]

// For updates (ignore current user)
'email' => ['required', 'email', new UniqueEmailInCompany($companyId, $userId)]
```

**What it checks:**
- Queries `users` joined with `company_users`
- Checks if email exists for users in specified company
- Optionally ignores a specific user (for updates)
- Returns error if email already in use within company

---

## Testing Checklist

### Login Scenarios
- [x] User with 1 company logs in normally
- [ ] User with 2+ companies sees company selection
- [ ] Company selection shows correct companies
- [ ] Login completes after selecting company
- [ ] Invalid password rejected
- [ ] Session stores correct company_id

### User Invitation Scenarios
- [ ] Inviting new email creates new user
- [ ] Inviting existing email reuses user account
- [ ] User added to company_users pivot table
- [ ] Cannot invite same email twice to same company
- [ ] Existing user can log in to both companies

### Registration Scenarios
- [ ] New email creates account successfully
- [ ] Existing email shows error message
- [ ] Each registration creates new company

### Validation Scenarios
- [ ] Email unique within company (not globally)
- [ ] Can update user without email conflicts
- [ ] UniqueEmailInCompany rule works correctly

---

## Known Limitations

1. **Password Synchronization**: All company associations share same password (single user account)
2. **Company Switcher**: Currently requires logout/login to switch companies
3. **Email Verification**: Verified once, applies to all companies
4. **Password Reset**: Currently global (affects all company access)

---

## Future Enhancements

### Recommended
1. **Company Switcher UI**: Add dropdown in navbar to switch companies without logging out
2. **Separate Passwords**: Option to have different passwords per company (would require separate user records)
3. **Company-Specific Email Verification**: Verify email separately for each company
4. **Better Notifications**: Custom email template for existing users added to new company

### Optional
5. **Company Access History**: Log which company user accessed when
6. **Default Company Preference**: Let user set preferred company for auto-selection
7. **Company Invitations**: Formal invitation system with acceptance workflow

---

## Troubleshooting

### Users Can't Select Company

**Problem:** Company selection page shows no companies or error

**Solutions:**
- Check `company_users` table has records for the user
- Verify session has `login_email` and `login_password_hash`
- Check if user account is soft-deleted

### Email Uniqueness Errors

**Problem:** Getting "email already exists" errors

**Solutions:**
- Verify using `UniqueEmailInCompany` rule with correct company ID
- Check company ID is from current session context
- Ensure passing user ID when updating (to ignore self)

### Company Context Not Persisting

**Problem:** Company context lost on page load

**Solutions:**
- Check session configuration
- Verify `current_company_id` set in session after login
- Use `auth()->user()->getCurrentCompanyId()` instead of direct session access

### User Invited to Wrong Company

**Problem:** User added to incorrect company

**Solutions:**
- Check auth user's company ID is correct
- Verify not using hardcoded company ID
- Ensure super-admin flow handled separately

---

## Code Examples

### Check Current Company in Controller

```php
public function index()
{
    $user = auth()->user();
    $companyId = $user->getCurrentCompanyId();
    $company = $user->getCurrentCompany();
    
    // Filter data by company
    $documents = Document::whereHas('company', function($query) use ($companyId) {
        $query->where('id', $companyId);
    })->get();
    
    return view('documents.index', compact('documents', 'company'));
}
```

### Validate Email in Company Scope

```php
use App\Rules\UniqueEmailInCompany;

public function store(Request $request)
{
    $companyId = auth()->user()->getCurrentCompanyId();
    
    $request->validate([
        'email' => ['required', 'email', new UniqueEmailInCompany($companyId)],
        // other rules...
    ]);
}
```

### Manually Switch Company (for future company switcher)

```php
public function switchCompany(Request $request)
{
    $request->validate([
        'company_id' => 'required|exists:company_accounts,id',
    ]);
    
    if (auth()->user()->switchCompany($request->company_id)) {
        return redirect()->route('dashboard')
            ->with('success', 'Switched to ' . auth()->user()->getCurrentCompany()->company_name);
    }
    
    return back()->with('error', 'You do not have access to this company.');
}
```

---

## Deployment Instructions

### 1. No Database Migration Needed
The database already has email uniqueness removed.

### 2. Clear Application Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 3. Test the Flow

**Test with existing user:**
1. Create a user in Company A
2. Invite same email to Company B (via UserController)
3. User should be added to Company B without creating duplicate
4. Log in with that email
5. Should see company selection screen
6. Select company and verify login works

**Test with new user:**
1. Invite new email to Company A
2. Verify new user created
3. Log in - should go directly to dashboard (single company)

### 4. Monitor Logs

Watch for log messages:
- `Reusing existing user account for company invitation`
- `Created new user for company invitation`
- `User logged in with company context`

---

## Security Considerations

### Password Security
- Password verified before company selection
- Password hash never sent to client
- Session data cleared after authentication

### Authorization
- User can only select companies they belong to
- Company context enforced throughout application
- Cannot access data from other companies

### Session Security
- Session regenerated after authentication
- Company context stored server-side (not in cookie)
- CSRF protection on all POST requests

---

**Implementation Status:** ✅ **COMPLETE**

All features implemented and ready for testing. The system now fully supports users existing across multiple companies with the same email address.
