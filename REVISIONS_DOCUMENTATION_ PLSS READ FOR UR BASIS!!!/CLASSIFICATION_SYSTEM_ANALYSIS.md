// Suggested Classification System Replacement

## Simplified Document Classification

### 1. Replace current classification dropdown with:
```html
<select name="classification" id="classification">
    <option value="public">Public - All company users can view</option>
    <option value="office">Office Only - Users in my office only</option>
    <option value="department">Department - Specific department only</option>
    <option value="restricted">Restricted - Selected users only</option>
</select>
```

### 2. Dynamic sections based on selection:

**For "Department":**
- Show department selector
- Much cleaner than office filtering

**For "Restricted":**
- Simple user multi-select (no office filtering)
- Search/autocomplete for users
- Tag-based display of selected users

### 3. Database changes:
```sql
-- Replace document_allowed_viewers table with:
CREATE TABLE document_permissions (
    id BIGINT PRIMARY KEY,
    document_id BIGINT,
    permission_type ENUM('public', 'office', 'department', 'restricted'),
    department_id BIGINT NULL,
    allowed_user_ids JSON NULL, -- Store array of user IDs for restricted
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 4. Benefits:
- ✅ Much simpler user interface
- ✅ Easier to understand permissions
- ✅ Automatic office-based filtering
- ✅ Scalable permission system
- ✅ Easy to audit and maintain
- ✅ Consistent with business workflows

### 5. Implementation:
- Remove office filtering complexity
- Use department-based access (aligns with org structure)
- For restricted: simple user search/select
- Automatic cleanup when users change roles
