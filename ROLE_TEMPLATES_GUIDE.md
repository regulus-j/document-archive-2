# Role Templates Guide

Quick reference for using role templates to streamline role creation and management.

## Overview

Role templates provide pre-configured permission sets for common user roles, making it easy to create consistent, properly-permissioned roles across your organization.

## Available Templates

### 1. Document Manager
**Best for:** Users who need full control over document workflows

**Permissions included:**
- Create, edit, and delete documents
- Initiate workflows and assign recipients
- Participate in workflows
- Full workflow administration

**Use cases:**
- Department heads
- Project managers
- Document coordinators

---

### 2. Viewer Only
**Best for:** Read-only users who need to view and respond to documents

**Permissions included:**
- Participate in workflows (view and respond)

**Use cases:**
- External consultants
- Read-only stakeholders
- Temporary reviewers

---

### 3. HR Manager
**Best for:** Human resources staff who manage users and documents

**Permissions included:**
- View, create, edit, and delete users
- Create and edit documents
- Participate in workflows

**Use cases:**
- HR managers
- People operations staff
- Employee onboarding coordinators

---

### 4. Office Manager
**Best for:** Users who manage teams/offices and their documents

**Permissions included:**
- View, create, edit, and delete offices/teams
- Full document management
- Initiate and participate in workflows

**Use cases:**
- Branch managers
- Department administrators
- Team leads

---

### 5. Auditor
**Best for:** Compliance and audit personnel

**Permissions included:**
- View audit logs and reports
- Participate in workflows

**Use cases:**
- Compliance officers
- Internal auditors
- Quality assurance staff

## How to Use Templates

### Creating a New Role

1. Navigate to **Roles** → **Create New Role**
2. In the "Quick Start Templates" section, review available templates
3. Click on the template that best matches your needs
4. The template name will be suggested, and permissions will be auto-selected
5. **Customize** as needed:
   - Change the role name
   - Add or remove individual permissions
   - Use permission group "Select All" buttons for quick adjustments
6. Click **Create Role** to save

### Editing an Existing Role

1. Navigate to **Roles** and find the role you want to edit
2. Click the **Edit** button
3. In the "Quick Start Templates" section, click a template to reconfigure
4. **Warning:** Clicking a template will replace all current permissions
5. Review and adjust the auto-selected permissions
6. Click **Update Role** to save changes

## Permission Groups

Templates select from these permission categories:

### Roles & Permissions
- View, create, edit, delete roles
- Assign permissions to users

### User Management
- View, create, edit, delete user accounts
- Invite and manage users

### Office Management
- View, create, edit, delete offices/teams
- Manage team membership

### Document Lifecycle
- **Manage Documents** - Create, edit, delete own documents
- **Initiate Workflows** - Forward documents and assign recipients
- **Participate in Workflows** - Receive and respond to assignments
- **Workflow Admin** - Full oversight of all workflows

### Audit & Logs
- View system audit trails
- Generate compliance reports

## Best Practices

### 1. Start with a Template
Always begin with the template closest to your needs rather than starting from scratch. This ensures:
- Consistent permission patterns
- No missing critical permissions
- Faster role creation

### 2. Customize for Your Organization
Templates are starting points. Adjust them to match your:
- Security policies
- Organizational structure
- Compliance requirements

### 3. Use Descriptive Role Names
When creating from a template, rename to match your organization:
- ❌ "Document Manager" (generic)
- ✅ "Marketing Document Manager" (specific)
- ✅ "Finance Workflow Administrator" (clear purpose)

### 4. Review Permissions Regularly
Periodically audit roles to ensure:
- Permissions match current responsibilities
- No privilege creep
- Compliance with security policies

### 5. Test New Roles
Before assigning a new role to multiple users:
1. Assign to a test user account
2. Log in as that user
3. Verify they can access what they need
4. Verify they cannot access what they shouldn't

## Common Customizations

### Adding Single Permissions
After applying a template, you can add individual permissions:
1. Expand the relevant permission group
2. Check additional permission boxes
3. Permission counts update automatically

### Removing Permissions
To restrict a template:
1. Apply the template
2. Expand permission groups
3. Uncheck permissions you want to remove
4. Or use group "Select All" then deselect specific items

### Combining Templates
You can manually combine permissions from multiple templates:
1. Start with one template
2. Note which permissions it selected
3. Manually add permissions from other templates
4. Create a custom role with mixed permissions

## Security Considerations

### Least Privilege Principle
Always grant the **minimum permissions** necessary:
- Start with "Viewer Only" if unsure
- Add permissions as needed
- Better to grant too few than too many

### Sensitive Permissions
Be cautious when granting:
- **User delete** - Can remove user accounts
- **Role delete** - Can remove roles
- **Workflow admin** - Full oversight of all documents

### Company-Admin vs Custom Roles
- **company-admin** - Pre-configured, full access
- **Custom roles** - Use templates to create restricted roles
- Consider creating custom admin roles with limited scope

## Troubleshooting

### Template Not Applying
**Problem:** Clicking template doesn't select permissions

**Solutions:**
- Refresh the page
- Ensure JavaScript is enabled
- Check browser console for errors

### Missing Permissions
**Problem:** Template doesn't include a needed permission

**Solution:**
- Apply the closest template
- Manually add the missing permission from the groups below

### Too Many Permissions
**Problem:** Template grants more access than needed

**Solution:**
- Apply the template
- Manually deselect unnecessary permissions
- Consider using a more restrictive template

## Permission Reference

### Document Workflow Behavior
**Important:** Document visibility is controlled separately by classification:
- **Public** - All users can see
- **Office Only** - Only office members can see
- **Custom Offices** - Only selected offices can see

Workflow permissions control **actions**, not visibility:
- **Manage** - Create/edit/delete
- **Initiate** - Forward/assign
- **Participate** - Receive/respond
- **Admin** - Full oversight

## Support

For questions about role templates or permissions:
1. Review this guide
2. Check `PERMISSION_FIX_SUMMARY.md` for technical details
3. Test with a non-production user account
4. Contact your system administrator

---

**Last Updated:** April 22, 2026  
**Feature Version:** 1.0  
**Compatibility:** All permission types (legacy + new-style)
