# Authorization System

## Overview

The Document Archive system uses a **two-layer authorization approach** that separates action permissions from document visibility control.

## Two-Layer Architecture

### Layer 1: Action Permissions (Spatie Permission Package)

**Purpose**: Controls **what actions** users can perform

**Examples**:
- Can create documents?
- Can edit users?
- Can delete roles?
- Can view audit logs?

**Implementation**: Role-based permissions using the Spatie Laravel Permission package
- Permissions like `documents.create`, `users.edit`, `roles.delete`
- Grouped into modules: Roles, Users, Offices, Documents, Audit
- Assigned to users via roles (e.g., company-admin, user)

**When to use**:
- Checking if a user can access a feature/module
- Verifying if a user can perform an action (CRUD operations)
- Route/controller middleware protection

### Layer 2: Document Classification

**Purpose**: Controls **which documents** users can see

**Classifications**:
1. **Public**: Visible to all users in the same company
2. **Office Only**: Visible to users in the same office(s) as the document uploader
3. **Custom Offices**: Visible to users in specifically selected offices
4. **Private**: (Legacy) Visible to specifically selected users

**Implementation**: Managed by `DocumentAccessService`
- Based on document's `classification` field
- Uses relationships (company, office, allowed viewers)
- Independent of action permissions

**When to use**:
- Determining if a user can see a document in lists
- Checking if a user can view document details
- Filtering accessible documents for a user

## How The Layers Work Together

Both layers must pass for a user to successfully perform an action on a document:

```
User wants to EDIT Document X
    ↓
1. Does user have 'documents.edit' permission? (Layer 1: Actions)
    ↓ YES
2. Can user access Document X based on classification? (Layer 2: Visibility)
    ↓ YES
✓ Action allowed
```

### Example Scenarios

#### Scenario 1: Editing a Document
- **Layer 1**: User needs `documents.edit` permission
- **Layer 2**: User must be the document owner (only owners can edit their documents)
- **Result**: Both must pass

#### Scenario 2: Viewing Documents List
- **Layer 1**: User needs `documents.view` permission to access the documents module
- **Layer 2**: Query is filtered to only show documents the user can access based on classification
- **Result**: User sees only their accessible documents

#### Scenario 3: Creating a User
- **Layer 1**: User needs `users.create` permission
- **Layer 2**: Not applicable (users aren't classified like documents)
- **Result**: Only Layer 1 is checked

## Permission Naming Convention

### New Format (Recommended)
Permissions use dotted notation: `{module}.{action}`

**Modules**:
- `roles` - Role management
- `users` - User management  
- `offices` - Office/team management
- `documents` - Document operations
- `audit` - Audit logs

**Actions**:
- `view` - List/read access
- `create` - Create new resources
- `edit` - Modify existing resources
- `delete` - Remove resources
- Special document actions: `release`, `receive`, `manage`, `workflow.initiate`, `workflow.participate`, `workflow.admin`

**Examples**:
- `roles.create` - Can create new roles
- `users.edit` - Can modify user information
- `documents.view` - Can access documents module
- `documents.delete` - Can delete documents
- `audit.view` - Can view audit logs
- `documents.manage` - Create, edit, and delete your own documents
- `documents.workflow.initiate` - Forward documents to start workflows
- `documents.workflow.participate` - Receive and respond to workflow assignments
- `documents.workflow.admin` - Full oversight and management of all workflows

### Legacy Format (Deprecated)
Old permissions use kebab-case: `{noun}-{verb}`

**Examples**:
- `role-create`
- `user-edit`
- `document-list`

**Backward Compatibility**: Both formats are currently supported. The system will automatically check both when verifying permissions.

## Usage Patterns

### In Controllers (Middleware)

Use permission middleware for **feature-level** access:

```php
public function __construct()
{
    // Check if user can access the users module at all
    $this->middleware('permission:users.view|users.create');
    $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
}
```

### In Controllers (Policies)

Use policies for **resource-specific** checks:

```php
public function edit(User $user)
{
    // Check if current user can edit THIS specific user
    $this->authorize('update', $user);
    
    // ... rest of method
}
```

### In Controllers (Document Access)

For documents, always use `DocumentAccessService` for visibility:

```php
public function show(Document $document)
{
    // Check Layer 1: Has permission to view documents
    if (!auth()->user()->can('documents.view')) {
        abort(403);
    }
    
    // Check Layer 2: Can see THIS document
    if (!$this->documentAccessService->canViewDocument($document)) {
        abort(403);
    }
    
    return view('documents.show', compact('document'));
}
```

Or use the unified `AuthorizationService`:

```php
public function show(Document $document)
{
    if (!$this->authorizationService->canAccessDocument($document, 'view')) {
        abort(403);
    }
    
    return view('documents.show', compact('document'));
}
```

### In Blade Views

Check permissions in templates:

```blade
@can('users.create')
    <a href="{{ route('users.create') }}">Add User</a>
@endcan

{{-- Document-specific check --}}
@can('update', $document)
    <a href="{{ route('documents.edit', $document) }}">Edit</a>
@endcan
```

### Using AuthorizationService

The `AuthorizationService` provides a unified interface:

```php
use App\Services\AuthorizationService;

class MyController extends Controller
{
    protected $authService;
    
    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }
    
    public function someMethod()
    {
        // Check single permission
        if ($this->authService->can('users.create')) {
            // ...
        }
        
        // Check any permission
        if ($this->authService->canAny(['users.edit', 'users.delete'])) {
            // ...
        }
        
        // Check document access (combines both layers)
        if ($this->authService->canAccessDocument($document, 'edit')) {
            // ...
        }
        
        // Get user's permissions grouped by module
        $permissions = $this->authService->getUserPermissionsByModule();
    }
}
```

## Default Roles

### company-admin
Full access to their company's resources:
- All role, user, office, document, and audit permissions
- Can see all documents in their company (Layer 2 override for public/office documents)
- Cannot see documents from other companies

### user
Basic document management:
- Can view, create, edit, and delete documents
- Can release and receive documents in workflow
- Document visibility based on classification (Layer 2)
- No access to users, roles, offices, or audit

### super-admin (Global)
System-wide administrative access:
- All permissions across all modules
- Can see all documents in all companies
- Manages companies and global system settings

## Adding New Permissions

1. **Add to PermissionMetadata.php**:
```php
'new_module' => [
    'label' => 'New Feature',
    'description' => 'Manage new feature',
    'icon' => 'icon-name',
    'color' => 'indigo',
    'permissions' => [
        'new_module.view' => [
            'label' => 'View Feature',
            'description' => 'Can view the feature',
            'legacy' => 'old-feature-list', // if migrating
        ],
        // ... more permissions
    ]
],
```

2. **Run the permission seeder** or create a migration:
```bash
php artisan db:seed --class=PermissionTableSeeder
```

3. **Assign to default roles** in `Role::createDefaultRolesForCompany()` if needed

4. **Use in controllers**:
```php
$this->middleware('permission:new_module.view');
```

## Best Practices

1. **Use Layer 1 for Actions**: Check `documents.create`, not document classification
2. **Use Layer 2 for Visibility**: Use `DocumentAccessService` to determine which documents to show
3. **Combine Both**: For document-specific actions (edit, delete), check both layers
4. **Use Policies for Complex Logic**: Policies are great for resource-specific authorization
5. **Use Middleware for Features**: Middleware is perfect for protecting entire modules
6. **Prefer New Permission Names**: Use dotted notation (`users.create`) over legacy (`user-create`)
7. **Document Special Cases**: If you deviate from the standard pattern, document why

## Troubleshooting

### User can't perform an action they should be able to

1. **Check Layer 1**: Does the user have the required permission?
   ```php
   auth()->user()->getAllPermissions(); // See all permissions
   ```

2. **Check Layer 2** (for documents): Can they access the specific resource?
   ```php
   $documentAccessService->canViewDocument($document, $user);
   ```

3. **Check role assignment**: Is the user in the correct role?
   ```php
   auth()->user()->getRoleNames(); // See all roles
   ```

### Permission isn't working

1. Ensure the permission exists in the database
2. Check for typos (case-sensitive!)
3. Clear permission cache: `php artisan permission:cache-reset`
4. Verify the permission is assigned to the user's role

### Document visibility issues

- Remember: Document visibility is independent of action permissions
- Check the document's `classification` field
- Verify office memberships for office-based classifications
- Use `DocumentAccessService::getDocumentViewers($document)` to debug

## Migration from Legacy Permissions

The system currently supports both old and new permission formats for backward compatibility:

- Old: `role-list`, `document-create`
- New: `roles.view`, `documents.create`

**Timeline**:
1. **Phase 1 (Current)**: Both formats work
2. **Phase 2 (Future)**: Deprecation warnings for old format
3. **Phase 3 (TBD)**: Remove support for old format

Update your code to use the new format at your earliest convenience.
