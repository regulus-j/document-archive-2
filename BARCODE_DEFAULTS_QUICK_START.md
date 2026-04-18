# Barcode Default Locations & Presets - Quick Start Guide

## 🚀 Quick Deployment (3 Steps)

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Clear Caches
```bash
php artisan config:clear && php artisan view:clear && php artisan route:clear
```

### Step 3: Test
1. Navigate to `/preferences` in your browser
2. Set a custom barcode position
3. Upload a document and verify defaults load automatically

---

## ✨ What's New

### For End Users

**🎯 Default Barcode Positions**
- Set your preferred barcode location once
- Automatically applied to all future uploads
- Access via user menu → Preferences

**⚡ Quick Presets**
- 8 built-in positions (Top Left, Top Right, Center, etc.)
- One-click application
- Available in both preferences page and upload modal

**💾 Save from Modal**
- Adjust barcode in preview
- Click "Save as Default" to remember your position
- No need to visit preferences page

### For Administrators

**🔧 Configurable System Defaults**
- Edit `config/barcode.php` to change system-wide defaults
- Add custom presets for your organization
- Control validation rules

---

## 📍 Access Points

1. **Preferences Page**: Navigate to `/preferences` or click user menu → Preferences
2. **Barcode Preview Modal**: When uploading documents with barcode overlay
3. **User Menu**: Add link to preferences in your navigation (optional)

---

## 🎨 Preset List

| Preset | Position | Use Case |
|--------|----------|----------|
| **Top Left** | X: 5%, Y: 3% | Standard header position |
| **Top Right** | X: 70%, Y: 3% | Letterhead-friendly |
| **Top Center** | X: 37.5%, Y: 3% | Centered header |
| **Bottom Left** | X: 5%, Y: 92% | Footer position |
| **Bottom Right** | X: 70%, Y: 92% | Bottom corner |
| **Bottom Center** | X: 37.5%, Y: 92% | Centered footer |
| **Center** | X: 37.5%, Y: 47.5% | Watermark style |
| **Small Top Right** | X: 80%, Y: 2% | Compact corner |

---

## 🔧 Configuration

### Adding Custom Preset

Edit `c:\xampp2\htdocs\document_archive\config\barcode.php`:

```php
'presets' => [
    'my-preset' => [
        'name' => 'My Custom Position',
        'description' => 'Perfect for our templates',
        'x_percent' => 15.0,
        'y_percent' => 10.0,
        'width_percent' => 20.0,
        'height_percent' => 4.0,
    ],
],
```

Then run: `php artisan config:cache`

### Changing System Defaults

Edit the `defaults` array in `config/barcode.php`:

```php
'defaults' => [
    'x_percent' => 10.0,      // Change from 5.0 to 10.0
    'y_percent' => 5.0,       // Change from 3.0 to 5.0
    'width_percent' => 30.0,  // Change from 25.0 to 30.0
    'height_percent' => 6.0,  // Change from 5.0 to 6.0
    'show_text' => true,
    'page' => 1,
],
```

Then run: `php artisan config:cache`

---

## 🧪 Quick Test

```bash
# Test user preferences route
curl http://localhost/preferences

# Test get defaults API (requires auth)
curl -H "X-Requested-With: XMLHttpRequest" \
     -H "Cookie: laravel_session=YOUR_SESSION" \
     http://localhost/preferences/barcode/defaults
```

---

## 📚 Full Documentation

See `BARCODE_PRESETS_AND_DEFAULTS_DOCUMENTATION.md` for:
- Complete API reference
- Database schema details
- Testing guide
- Troubleshooting
- Security considerations

---

## ⚠️ Important Notes

1. **Migration Required**: Users table needs `preferences` column
2. **Backward Compatible**: Existing barcode functionality unchanged
3. **User-Specific**: Each user has their own defaults
4. **Fallback Safe**: If user has no custom defaults, system defaults are used
5. **Percentage-Based**: All positions use percentage (works on any document size)

---

## 🎯 User Workflow

### First Time Setup
1. User navigates to `/preferences`
2. Clicks a preset or manually adjusts values
3. Clicks "Save Default Settings"
4. Done! Future uploads use these settings

### Alternative Workflow
1. User uploads document
2. Opens barcode preview modal
3. Drags barcode to desired position
4. Clicks "Save as Default"
5. Position saved for future uploads

### Loading Defaults
- Automatically loaded when modal opens
- Click "Load My Default" to reload
- Click "Reset to default" to use system defaults

---

## 💡 Tips

- **Drag and Drop**: Use the interactive preview to visually position barcodes
- **Presets First**: Try presets before manual adjustment
- **Save Often**: Use "Save as Default" whenever you find a good position
- **Per-User**: Each user can have different defaults (great for departments)
- **Company Standards**: Admins can set system defaults to match company templates

---

## 🆘 Need Help?

**Defaults Not Loading?**
- Ensure migration ran: `php artisan migrate:status`
- Check browser console for errors
- Verify user is logged in

**Presets Not Showing?**
- Clear config cache: `php artisan config:clear`
- Refresh browser (Ctrl+F5)
- Check `config/barcode.php` exists

**Can't Save Defaults?**
- Verify routes are registered: `php artisan route:list | grep preferences`
- Check CSRF token is present
- Ensure user is authenticated

---

**Ready to use!** 🎉
