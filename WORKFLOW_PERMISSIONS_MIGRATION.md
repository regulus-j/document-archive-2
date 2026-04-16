# Workflow Permissions Migration - Implementation Summary

## Date: April 17, 2026

## What Changed

The document permissions have been restructured to align with the actual document workflow lifecycle, replacing granular action permissions with lifecycle-stage permissions.

## Old vs New Permissions

### Before (Action-Based)
```
documents.view      - Access documents module
documents.create    - Upload documents
documents.edit      - Edit documents
documents.delete    - Delete documents
documents.release   - Release in workflow
documents.receive   - Receive in workflow
```

**Problems:**
- ❌ `documents.release` and `documents.receive` didn't map to actual workflow
- ❌ Workflow purpose (appropriate_action, for_comment, dissemination) already controls what actions users can take
- ❌ Confusing which permissions users need for workflow participation

### After (Lifecycle-Based)
```
documents.manage                    - Create, edit, and delete your own documents
documents.workflow.initiate         - Forward documents to start workflows  
documents.workflow.participate      - Receive and respond to workflow assignments
documents.workflow.admin            - Full oversight and management of all workflows
```

**Benefits:**
- ✅ Maps to actual document lifecycle stages
- ✅ Aligns with how users interact with the system
- ✅ Workflow purpose restricts available actions automatically
- ✅ Clear separation between creating vs participating in workflows

## Migration Map

The system automatically migrated existing permissions:

```
OLD                                 → NEW
─────────────────────────────────────────────────────────────
documents.create + edit + delete    → documents.manage
documents.release                   → documents.workflow.initiate
documents.receive                   → documents.workflow.participate
(new)                              → documents.workflow.admin
```

All existing role assignments were automatically migrated during the database migration.

## Updated Default Roles

### company-admin
```php
'company-admin' => [
    // Access Management
    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
    'users.view', 'users.create', 'users.edit', 'users.delete',
    
    // Organization Management
    'offices.view', 'offices.create', 'offices.edit', 'offices.delete',
    
    // Document Lifecycle ← NEW
    'documents.manage',
    'documents.workflow.initiate',
    'documents.workflow.participate',
    'documents.workflow.admin',
    
    // Audit
    'audit.view',
]
```

### user (Basic User)
```php
'user' => [
    // Document Lifecycle ← NEW
    'documents.manage',
    'documents.workflow.initiate',
    'documents.workflow.participate',
]
```

## How Workflow Actions Are Controlled

### Two-Layer System
1. **Permission Layer**: What stage of lifecycle user can participate in
2. **Purpose Layer**: What specific actions are available

```
User has documents.workflow.participate
    ↓
Assigned to workflow with purpose "appropriate_action"
    ↓
Can: approve, reject, return, forward
Cannot: comment (that's for "for_comment" purpose)
```

### Workflow Purposes
- **appropriate_action**: approve, reject, return, forward
- **for_comment**: comment only
- **dissemination**: acknowledge, forward

**The permission grants access to participate, but the purpose restricts which buttons/actions appear.**

## User Journeys

### Journey 1: Document Creator
```
User uploads document → Needs: documents.manage
User forwards to approver → Needs: documents.workflow.initiate
```

### Journey 2: Workflow Approver
```
User receives assignment → Needs: documents.workflow.participate
Purpose is "appropriate_action" → Can approve/reject/return/forward
```

### Journey 3: Reviewer  
```
User receives assignment → Needs: documents.workflow.participate
Purpose is "for_comment" → Can comment only
```

### Journey 4: Admin Oversight
```
User needs to audit workflows → Needs: documents.workflow.admin
User needs to reroute stuck workflow → Needs: documents.workflow.admin
```

## What Stayed The Same

✅ **Document visibility** still controlled by classification (Public/Office/Custom)  
✅ **Workflow purpose** still restricts available actions  
✅ **DocumentAccessService** unchanged  
✅ **DocumentPolicy** unchanged  
✅ **All other modules** (Roles, Users, Offices, Audit) unchanged

## Backward Compatibility

### During Transition
- ✅ Old permission names still work
- ✅ Both old and new names checked automatically
- ✅ Existing roles automatically migrated
- ✅ No breaking changes to functionality

### Migration Path
1. **Phase 1 (Current)**: Both old and new permissions work
2. **Phase 2 (Future)**: Add deprecation warnings for old names
3. **Phase 3 (Later)**: Remove old permissions

## Files Modified

### Created (2 files)
1. `database/migrations/2026_04_16_182147_migrate_to_workflow_lifecycle_permissions.php`
2. `WORKFLOW_PERMISSIONS_MIGRATION.md` (this file)

### Modified (6 files)
1. `app/Models/PermissionMetadata.php` - Updated document permissions structure
2. `app/Models/Role.php` - Updated default role permissions
3. `database/seeders/PermissionTableSeeder.php` - Added new permissions to seed
4. `docs/AUTHORIZATION.md` - Updated documentation
5. `RBAC_QUICK_START.md` - Updated quick reference
6. Views (role create/edit) - Already use PermissionMetadata, automatically updated

## Testing Performed

✅ Seeder execution successful  
✅ Migration successful (permissions created and role relationships migrated)  
✅ Permission metadata validated  
✅ Role creation UI displays new grouped permissions  
✅ Backward compatibility verified (old names still work)

## For Developers

### Use New Permission Names

```php
// ✅ Use these going forward
$this->middleware('permission:documents.manage');
$this->middleware('permission:documents.workflow.initiate');
$this->middleware('permission:documents.workflow.participate');

// ⚠️ Old names deprecated (but still work)
$this->middleware('permission:documents.create');
$this->middleware('permission:documents.release');
$this->middleware('permission:documents.receive');
```

### Check Permissions

```php
// Check if user can manage documents
if (auth()->user()->can('documents.manage')) {
    // User can create/edit/delete own documents
}

// Check if user can initiate workflows
if (auth()->user()->can('documents.workflow.initiate')) {
    // User can forward documents
}

// Check if user can participate in workflows
if (auth()->user()->can('documents.workflow.participate')) {
    // User can receive and respond to assignments
    // Specific actions depend on workflow purpose
}

// Check if user can admin workflows
if (auth()->user()->can('documents.workflow.admin')) {
    // User can view all workflows, reroute, etc.
}
```

### In Blade Templates

```blade
@can('documents.manage')
    <a href="{{ route('documents.create') }}">Upload Document</a>
@endcan

@can('documents.workflow.initiate')
    <a href="{{ route('documents.forward', $document) }}">Forward</a>
@endcan

@can('documents.workflow.participate')
    {{-- User can participate in workflows they're assigned to --}}
    {{-- Available actions controlled by workflow purpose --}}
@endcan
```

## Benefits Achieved

### For Users
✅ Clearer understanding of what permissions do  
✅ Easier role assignment based on job function  
✅ Permissions match how they actually work

### For Administrators
✅ Intuitive role creation with lifecycle stages  
✅ Less confusion about workflow permissions  
✅ Easier to assign appropriate permissions

### For Developers
✅ Permissions align with code structure  
✅ Less redundancy (purpose handles action restrictions)  
✅ Easier to maintain and extend

## Support

For questions or issues:
- See [docs/AUTHORIZATION.md](docs/AUTHORIZATION.md) for full documentation
- See [docs/DEVELOPER_GUIDE_AUTHORIZATION.md](docs/DEVELOPER_GUIDE_AUTHORIZATION.md) for code examples
- Check [RBAC_QUICK_START.md](RBAC_QUICK_START.md) for quick reference

## Next Steps

1. **✅ COMPLETE**: Permissions restructured
2. **✅ COMPLETE**: Migration executed  
3. **✅ COMPLETE**: Documentation updated
4. **TODO**: Monitor for any issues with new permission structure
5. **FUTURE**: Add deprecation warnings for old permission names
6. **FUTURE**: Remove old permissions after transition period

---

**Implementation Complete**: April 17, 2026  
**Migration Status**: ✅ Successful  
**Backward Compatible**: ✅ Yes
