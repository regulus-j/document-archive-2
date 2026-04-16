# RBAC System - Quick Start Guide

## 🎉 What's New?

The RBAC system has been upgraded with:
- ✅ **Grouped Permissions UI** - Permissions organized by module with descriptions
- ✅ **New Permission Format** - Cleaner `module.action` naming (e.g., `users.create`)
- ✅ **Better Documentation** - Complete guides for developers and admins
- ✅ **Backward Compatible** - Old permission names still work

## 🚀 For End Users

### Creating a New Role

1. Navigate to **Roles** → **Create Role**
2. Enter a descriptive role name (e.g., "Document Manager")
3. **Select Permissions by Module**:
   - Click on a module header to expand it
   - Use "Select All" to choose all permissions in a module
   - Or select individual permissions with descriptions
4. Watch the counter update as you select (e.g., "5 permissions selected")
5. Click **Create Role**

### Module Breakdown

| Module | What It Controls |
|--------|-----------------|
| 🛡️ **Roles & Permissions** | Who can manage roles in your company |
| 👥 **User Management** | Who can add, edit, or remove users |
| 🏢 **Office Management** | Who can manage offices/teams |
| 📄 **Document Actions** | Who can upload, edit, delete documents |
| 📋 **Audit & Logs** | Who can view system activity logs |

### Understanding Document Visibility

**Important:** Document permissions control **actions** (create, edit, delete), but document **visibility** is controlled separately:

- **Public** documents: Everyone in your company can see them
- **Office Only**: Only people in the same office can see them
- **Custom Offices**: Only people in selected offices can see them
- **Private**: Only specific people you choose can see them

This is independent of permissions! Even if someone has "View Documents" permission, they can only see documents based on these visibility rules.

## 💻 For Developers

### Using New Permission Names

```php
// ✅ Use new format (recommended)
$this->middleware('permission:users.create');
if (auth()->user()->can('documents.edit')) { ... }

// ⚠️ Old format still works but deprecated
$this->middleware('permission:user-create');
if (auth()->user()->can('document-edit')) { ... }
```

### Quick Code Examples

**Check permission in controller:**
```php
use App\Services\AuthorizationService;

public function __construct(AuthorizationService $auth)
{
    if (!$auth->can('users.create')) {
        abort(403);
    }
}
```

**Check permission in Blade:**
```blade
@can('users.create')
    <a href="{{ route('users.create') }}">Create User</a>
@endcan
```

**Check document access:**
```php
use App\Services\AuthorizationService;

if (!$authService->canAccessDocument($document, 'edit')) {
    abort(403);
}
```

### Available Permissions

**Format:** `module.action`

**Roles Module:**
- `roles.view` - View roles list
- `roles.create` - Create new roles
- `roles.edit` - Edit existing roles
- `roles.delete` - Delete roles

**Users Module:**
- `users.view` - View users list
- `users.create` - Create new users
- `users.edit` - Edit user info
- `users.delete` - Delete users

**Offices Module:**
- `offices.view` - View offices list
- `offices.create` - Create new offices
- `offices.edit` - Edit office info
- `offices.delete` - Delete offices

**Documents Module:**
- `documents.view` - Access documents module
- `documents.create` - Upload documents
- `documents.edit` - Edit documents
- `documents.delete` - Delete documents
- `documents.release` - Release in workflow
- `documents.receive` - Receive in workflow

**Audit Module:**
- `audit.view` - View audit logs

### Resources

📖 **Full Documentation:**
- [Authorization System Guide](docs/AUTHORIZATION.md) - Complete system overview
- [Developer Guide](docs/DEVELOPER_GUIDE_AUTHORIZATION.md) - Code examples and patterns
- [Implementation Summary](RBAC_REFACTOR_SUMMARY.md) - What changed and why

## 🔧 Common Tasks

### Add a New Permission

1. Edit `app/Models/PermissionMetadata.php`
2. Add to appropriate module or create new module
3. Run: `php artisan db:seed --class=PermissionTableSeeder`
4. Assign to roles as needed

### Update Default Role Permissions

Edit `app/Models/Role.php` in the `createDefaultRolesForCompany()` method.

### Check User's Permissions

```bash
php artisan tinker
>>> User::find(1)->getAllPermissions()->pluck('name')
```

### Clear Permission Cache

```bash
php artisan permission:cache-reset
```

## ⚠️ Important Notes

1. **Two Layers**: Remember the system has two independent layers:
   - **Layer 1**: What you can DO (permissions)
   - **Layer 2**: What documents you can SEE (classification)

2. **Backward Compatibility**: Old permission names (`role-list`, etc.) still work but use new names (`roles.view`) in new code.

3. **Document Visibility**: Having `documents.view` permission doesn't mean you can see ALL documents - visibility is controlled by classification.

4. **Role Scoping**: Roles are scoped to companies. A `company-admin` can't manage roles in other companies.

## 🆘 Troubleshooting

**Permission not working?**
```bash
php artisan permission:cache-reset
```

**Can't see expected documents?**
- Check both permission (`documents.view`) AND document classification
- Use `DocumentAccessService` to debug visibility

**Role not being applied?**
- Verify user is assigned the role
- Check role has the required permissions
- Clear permission cache

## 📞 Need Help?

- Check the [Full Documentation](docs/AUTHORIZATION.md)
- Review code examples in [Developer Guide](docs/DEVELOPER_GUIDE_AUTHORIZATION.md)
- See [Implementation Summary](RBAC_REFACTOR_SUMMARY.md) for technical details

---

**Version:** 1.0  
**Last Updated:** April 17, 2026
