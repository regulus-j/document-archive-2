# Multi-Company Email Support - Quick Start Guide

## What Changed?

Users can now exist in multiple companies with the same email address. When logging in, they select which company to access.

## Quick Test (3 Steps)

### 1. Test User Invitation

```bash
# Via browser:
1. Log in as Company A admin
2. Create user: test@example.com
3. Log in as Company B admin  
4. Invite user: test@example.com (same email)
5. Check logs - should see "Reusing existing user account"
```

### 2. Test Login

```bash
# Via browser:
1. Log out
2. Log in with: test@example.com
3. You should see company selection screen
4. Select Company A or B
5. Should successfully log in to selected company
```

### 3. Verify No Duplicates

```sql
-- Run in database
SELECT email, COUNT(*) as count 
FROM users 
WHERE email = 'test@example.com' 
GROUP BY email;

-- Should return: count = 1 (single user record)

SELECT * FROM company_users 
WHERE user_id = (SELECT id FROM users WHERE email = 'test@example.com');

-- Should return: 2 rows (one for each company)
```

## Key Changes Summary

| Feature | Old Behavior | New Behavior |
|---------|-------------|--------------|
| **Email Uniqueness** | Global (one email = one account) | Per-company (email unique within company) |
| **User Invitation** | Always creates new user | Reuses existing user if email exists |
| **Login Flow** | Direct to dashboard | Company selection if multiple companies |
| **Registration** | Creates user + company | Still requires unique email (unchanged) |

## Files Modified

✅ **7 Files Modified**
- `app/Models/User.php` - Added company context methods
- `app/Http/Requests/Auth/LoginRequest.php` - Multi-company detection
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Redirect handling
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Validation update
- `app/Http/Controllers/UserController.php` - Reuse existing users
- `routes/auth.php` - Added company selection routes

✅ **3 Files Created**
- `app/Rules/UniqueEmailInCompany.php` - Custom validation
- `app/Http/Controllers/Auth/CompanySelectionController.php` - Company selection logic
- `resources/views/auth/select-company.blade.php` - Selection UI

## No Database Changes Needed

The email uniqueness constraint was already removed in a previous migration:
- `2025_05_12_103018_remove_email_uniqueness_from_users_table.php`

Just run cache clear:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## User Scenarios

### Scenario 1: Existing User Joins Second Company
1. User exists in Company A: john@example.com
2. Company B admin invites: john@example.com
3. System reuses john's account, adds to Company B
4. John logs in → sees company selection → picks A or B

### Scenario 2: New User Registration
1. New user registers: sarah@example.com
2. Creates new account + new company
3. Sarah logs in → goes directly to dashboard (single company)

### Scenario 3: User Updates Email
1. User in Company A changes email to: newemail@example.com
2. System validates: email must be unique within Company A
3. Email can exist in Company B (different company)

## Common Questions

**Q: Can same email have different passwords in different companies?**  
A: No. Same email = same user account = same password for all companies.

**Q: What happens if I invite existing user?**  
A: System reuses the account and adds company association. User gets access to both companies.

**Q: How do users switch between companies?**  
A: Currently: log out and log in again, select different company. Future: company switcher UI.

**Q: Can I still register with existing email?**  
A: No. Registration requires unique email (creates new company).

**Q: Where is company context stored?**  
A: In server session: `session('current_company_id')`

## Validation Rule Usage

```php
use App\Rules\UniqueEmailInCompany;

// For creating user
$companyId = auth()->user()->getCurrentCompanyId();
$request->validate([
    'email' => ['required', 'email', new UniqueEmailInCompany($companyId)]
]);

// For updating user
$request->validate([
    'email' => ['required', 'email', new UniqueEmailInCompany($companyId, $userId)]
]);
```

## Logs to Watch

```bash
# Successful scenarios
[info] Reusing existing user account for company invitation
[info] Created new user for company invitation  
[info] User logged in with company context

# Error scenarios
[warning] This user is already part of your company
[error] You do not have access to this company
```

## Rollback (If Needed)

If you need to revert:
1. Restore old validation rules (global email uniqueness)
2. Remove company selection routes
3. Revert LoginRequest authenticate() method
4. Remove UniqueEmailInCompany rule

Keep in mind: Users already shared across companies will have issues.

## Next Steps

1. **Test thoroughly** with real scenarios
2. **Add company switcher** to UI (future enhancement)
3. **Update user documentation** for end users
4. **Monitor logs** for any issues

---

**Ready to Use!** 🚀

All changes are backward compatible. Existing single-company users will log in normally with no changes to their experience.
