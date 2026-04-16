# Developer Guide: Authorization System

Quick reference for developers working with the authorization system in DocTrack.

## Quick Reference

### Check Permission in Controller

```php
// Using middleware (recommended for feature access)
public function __construct()
{
    $this->middleware('permission:users.view|users.create');
    $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
}

// Using direct check
if (!auth()->user()->can('users.create')) {
    abort(403);
}

// Using AuthorizationService
if (!$this->authService->can('users.create')) {
    abort(403);
}
```

### Check Permission in Blade

```blade
@can('users.create')
    <a href="{{ route('users.create') }}">Create User</a>
@endcan

{{-- Multiple permissions --}}
@canany(['users.create', 'users.edit'])
    <button>Manage Users</button>
@endcanany
```

### Check Document Access

```php
// Method 1: Using DocumentAccessService
if (!$this->documentAccessService->canViewDocument($document)) {
    abort(403);
}

// Method 2: Using Policy
$this->authorize('view', $document);

// Method 3: Using AuthorizationService (recommended - combines both layers)
if (!$this->authService->canAccessDocument($document, 'view')) {
    abort(403);
}
```

## Common Patterns

### Controller Setup

```php
use App\Services\AuthorizationService;

class MyController extends Controller
{
    protected $authService;

    public function __construct(AuthorizationService $authService)
    {
        // Feature-level protection
        $this->middleware('permission:documents.view');
        
        $this->authService = $authService;
    }

    public function index()
    {
        // Get only accessible documents
        $documents = $this->documentAccessService->getAccessibleDocuments();
        
        return view('documents.index', compact('documents'));
    }

    public function show(Document $document)
    {
        // Resource-level check (combines both layers)
        if (!$this->authService->canAccessDocument($document, 'view')) {
            abort(403, 'You do not have permission to view this document.');
        }
        
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        // Check edit permission + ownership
        if (!$this->authService->canAccessDocument($document, 'edit')) {
            abort(403);
        }
        
        return view('documents.edit', compact('document'));
    }
}
```

### Policy Example

```php
namespace App\Policies;

use App\Models\User;
use App\Models\Office;

class OfficePolicy
{
    public function view(User $user, Office $office): bool
    {
        // User must have permission AND be in the same company
        if (!$user->hasPermissionTo('offices.view')) {
            return false;
        }
        
        return $user->companies->contains($office->company_id);
    }

    public function update(User $user, Office $office): bool
    {
        if (!$user->hasPermissionTo('offices.edit')) {
            return false;
        }
        
        // Company admin can edit offices in their company
        if ($user->hasRole('company-admin')) {
            return $user->companies->contains($office->company_id);
        }
        
        return false;
    }
}
```

## Adding New Permissions

### Step 1: Define in PermissionMetadata

Edit `app/Models/PermissionMetadata.php`:

```php
'new_feature' => [
    'label' => 'New Feature',
    'description' => 'Manage the new feature',
    'icon' => 'star',  // Choose from: shield-check, users, building, file-text, clipboard-list
    'color' => 'purple',  // Choose from: blue, green, purple, indigo, yellow
    'permissions' => [
        'new_feature.view' => [
            'label' => 'View Feature',
            'description' => 'Can view and access the new feature',
            'legacy' => null,  // or legacy name if migrating
        ],
        'new_feature.create' => [
            'label' => 'Create Items',
            'description' => 'Can create new items in the feature',
            'legacy' => null,
        ],
        // ... more permissions
    ]
],
```

### Step 2: Seed the Permissions

```bash
php artisan db:seed --class=PermissionTableSeeder
```

Or create a migration:

```php
use Spatie\Permission\Models\Permission;

Permission::firstOrCreate([
    'name' => 'new_feature.view',
    'guard_name' => 'web',
]);
```

### Step 3: Assign to Roles

Update `app/Models/Role.php` in `createDefaultRolesForCompany()`:

```php
$defaultRoles = [
    'company-admin' => [
        // ... existing permissions
        'new_feature.view',
        'new_feature.create',
    ],
    'user' => [
        // ... existing permissions  
        'new_feature.view',  // if users should have it
    ],
];
```

### Step 4: Use in Code

```php
// Controller
$this->middleware('permission:new_feature.view');

// Blade
@can('new_feature.create')
    <button>Create New Item</button>
@endcan
```

## Permission Modules

### Available Modules

| Module | Permissions | Purpose |
|--------|-------------|---------|
| `roles` | view, create, edit, delete | Role management |
| `users` | view, create, edit, delete | User management |
| `offices` | view, create, edit, delete | Office/team management |
| `documents` | view, create, edit, delete, release, receive | Document operations |
| `audit` | view | Audit log access |

### Module Naming Rules

1. **Module name**: Plural, lowercase (e.g., `users`, `documents`)
2. **Action name**: Singular verb (e.g., `view`, `create`, `edit`, `delete`)
3. **Format**: Always `{module}.{action}`

## Document Visibility Rules

### Classification Types

```php
// Public - Visible to all users in same company
$document->classification = 'Public';

// Office Only - Visible to users in same office as uploader
$document->classification = 'Office Only';

// Custom Offices - Visible to users in selected offices
$document->classification = 'Custom Offices';
$document->allowedOffices()->attach([1, 2, 3]); // office IDs

// Private (Legacy) - Visible to selected users only
$document->classification = 'Private';
$document->allowedViewers()->attach([5, 6, 7]); // user IDs
```

### Checking Visibility

```php
// Get all documents user can access
$accessibleDocs = app(DocumentAccessService::class)
    ->getAccessibleDocuments($user);

// Check specific document
$canView = app(DocumentAccessService::class)
    ->canViewDocument($document, $user);

// Get visibility description
$description = app(DocumentAccessService::class)
    ->getAccessDescription($document);
// Returns: "Visible to all company users" for Public classification
```

## Testing Authorization

### Unit Test Example

```php
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationTest extends TestCase
{
    public function test_user_can_create_documents()
    {
        // Create user with permission
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'documents.create']);
        $role = Role::create(['name' => 'test-role']);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->can('documents.create'));
    }

    public function test_document_visibility_by_classification()
    {
        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        
        $document = Document::factory()->create([
            'uploader' => $uploader->id,
            'classification' => 'Public',
        ]);

        $service = app(DocumentAccessService::class);
        $this->assertTrue($service->canViewDocument($document, $viewer));
    }
}
```

### Feature Test Example

```php
public function test_user_cannot_access_documents_without_permission()
{
    $user = User::factory()->create();
    // Don't assign documents.view permission

    $this->actingAs($user)
        ->get(route('documents.index'))
        ->assertStatus(403);
}

public function test_user_can_only_see_their_accessible_documents()
{
    $user = User::factory()->create();
    $permission = Permission::firstOrCreate(['name' => 'documents.view']);
    $user->givePermissionTo($permission);

    // Create various documents
    $publicDoc = Document::factory()->create(['classification' => 'Public']);
    $privateDoc = Document::factory()->create(['classification' => 'Private']);

    $this->actingAs($user)
        ->get(route('documents.index'))
        ->assertSee($publicDoc->title)
        ->assertDontSee($privateDoc->title);
}
```

## Troubleshooting

### Permission not working

```bash
# 1. Clear permission cache
php artisan permission:cache-reset

# 2. Verify permission exists
php artisan tinker
>>> \Spatie\Permission\Models\Permission::where('name', 'documents.create')->first()

# 3. Check user's permissions
>>> User::find(1)->getAllPermissions()

# 4. Check user's roles
>>> User::find(1)->getRoleNames()
```

### Document visibility issues

```php
// Debug what documents a user can access
use App\Services\DocumentAccessService;

$service = app(DocumentAccessService::class);
$user = User::find(1);

// Get accessible documents
$docs = $service->getAccessibleDocuments($user)->get();
dd($docs->pluck('id', 'title'));

// Check specific document
$document = Document::find(1);
$canView = $service->canViewDocument($document, $user);
dd([
    'can_view' => $canView,
    'classification' => $document->classification,
    'uploader' => $document->uploader,
    'current_user' => $user->id,
]);
```

## Best Practices

### ✅ DO

- Use new dotted notation for permissions (`users.create`)
- Check both layers for document operations
- Use middleware for feature-level protection
- Use policies for complex resource-level logic
- Group related permissions in the same module
- Document any custom authorization logic

### ❌ DON'T

- Don't use legacy permission names in new code
- Don't check only one layer for document access
- Don't hardcode permission names - use constants if needed
- Don't skip authorization checks "temporarily"
- Don't mix authorization logic across multiple places

### Security Guidelines

1. **Always authorize before showing data**
   ```php
   // Bad
   return view('user.show', ['user' => User::find($id)]);
   
   // Good
   $user = User::findOrFail($id);
   $this->authorize('view', $user);
   return view('user.show', compact('user'));
   ```

2. **Filter queries, don't just hide UI**
   ```php
   // Bad - User can still access via API
   @can('view', $document)
       <a href="{{ route('documents.show', $document) }}">View</a>
   @endcan
   
   // Good - Authorization in controller too
   public function show(Document $document) {
       $this->authorize('view', $document);
       // ...
   }
   ```

3. **Use accessor methods consistently**
   ```php
   // Bad - Direct property access
   if ($user->role === 'admin') { ... }
   
   // Good - Use helper methods
   if ($user->hasRole('admin')) { ... }
   if ($user->isAdmin()) { ... }
   ```

## Quick Command Reference

```bash
# Clear permission cache
php artisan permission:cache-reset

# Seed permissions
php artisan db:seed --class=PermissionTableSeeder

# Create migration for new permissions
php artisan make:migration add_new_feature_permissions

# Test specific permission
php artisan tinker
>>> auth()->user()->can('documents.create')

# List all permissions
php artisan tinker
>>> \Spatie\Permission\Models\Permission::all()->pluck('name')

# Show user's permissions
php artisan tinker
>>> User::find(1)->getAllPermissions()->pluck('name')
```

## Further Reading

- [Full Authorization Documentation](AUTHORIZATION.md)
- [Spatie Laravel Permission Docs](https://spatie.be/docs/laravel-permission)
- [Laravel Authorization Docs](https://laravel.com/docs/11.x/authorization)
