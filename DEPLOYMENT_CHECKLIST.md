# Deployment Checklist

This checklist ensures critical database seeding and verification steps are completed during deployment.

## Pre-Deployment Verification

### 1. Check Database Connection
```bash
php artisan db:show
```

### 2. Verify Migrations Are Ready
```bash
php artisan migrate:status
```

## Deployment Steps

### 1. Run Database Migrations
```bash
php artisan migrate --force
```

**Critical**: The `2026_03_16_132138_ensure_permissions_exist` migration automatically creates all required permissions. This prevents registration 500 errors.

### 2. Verify Permissions Exist (Optional)
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::count()
# Should return 19
>>> exit
```

### 3. Clear Application Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 4. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Post-Deployment Verification

### 1. Test Registration Flow
- Navigate to `/register`
- Complete the registration form
- Verify successful account creation
- Check that default roles are assigned

### 2. Check Logs for Errors
```bash
tail -n 50 storage/logs/laravel.log
```

### 3. Verify Role Creation
```bash
php artisan tinker
>>> \App\Models\Role::where('company_id', '!=', null)->count()
# Should show company-specific roles
>>> exit
```

## Troubleshooting

### Registration Still Fails with 500 Error

**Symptom**: Users cannot register, getting 500 errors

**Solution**:
```bash
# Check if permissions exist
php artisan tinker
>>> \Spatie\Permission\Models\Permission::count()
>>> exit

# If count is 0 or less than 19, run:
php artisan migrate:fresh --seed
# WARNING: This will wipe the database! Use only in development.

# For production, manually run:
php artisan db:seed --class=PermissionTableSeeder
```

### Missing Roles After Registration

**Symptom**: Users are created but don't have roles assigned

**Check logs**: 
```bash
grep "Failed to create default role" storage/logs/laravel.log
```

**Solution**: The system now auto-creates permissions and logs errors without failing registration. Users can be assigned roles manually through the admin panel.

## Critical Files Modified

- `app/Models/Role.php` - Added error handling and auto-permission creation
- `database/migrations/2026_03_16_132138_ensure_permissions_exist.php` - Auto-seeds permissions
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Registration controller
- `app/Models/CompanyAccount.php` - Auto-creates roles on company creation

## Environment-Specific Notes

### Production
- Always use `--force` flag with migrations
- Never use `migrate:fresh` (data loss!)
- Monitor logs after deployment

### Staging
- Test registration flow thoroughly
- Verify role permissions are correct
- Check email delivery for verification codes

### Development/Local
- Can use `migrate:fresh --seed` freely
- Keep local environment synced with latest migrations
