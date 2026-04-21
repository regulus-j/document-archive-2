<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class RoleTemplate
{
    /**
     * Get all predefined role templates
     *
     * @return array
     */
    public static function getTemplates(): array
    {
        return [
            'document-manager' => [
                'name' => 'Document Manager',
                'description' => 'Full control over documents and workflows',
                'icon' => 'file-text',
                'color' => 'indigo',
                'permissions' => [
                    'documents.manage',
                    'documents.workflow.initiate',
                    'documents.workflow.participate',
                    'documents.workflow.admin',
                ]
            ],
            'viewer' => [
                'name' => 'Viewer Only',
                'description' => 'Read-only access to documents and data',
                'icon' => 'eye',
                'color' => 'blue',
                'permissions' => [
                    'documents.workflow.participate',
                ]
            ],
            'hr-manager' => [
                'name' => 'HR Manager',
                'description' => 'Manage users and view documents',
                'icon' => 'users',
                'color' => 'green',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'documents.manage',
                    'documents.workflow.participate',
                ]
            ],
            'office-manager' => [
                'name' => 'Office Manager',
                'description' => 'Manage offices/teams and documents',
                'icon' => 'building',
                'color' => 'purple',
                'permissions' => [
                    'offices.view',
                    'offices.create',
                    'offices.edit',
                    'offices.delete',
                    'documents.manage',
                    'documents.workflow.initiate',
                    'documents.workflow.participate',
                ]
            ],
            'auditor' => [
                'name' => 'Auditor',
                'description' => 'View audit logs and reports',
                'icon' => 'clipboard-list',
                'color' => 'yellow',
                'permissions' => [
                    'audit.view',
                    'documents.workflow.participate',
                ]
            ],
        ];
    }

    /**
     * Get permission IDs for a template
     *
     * @param string $templateKey
     * @return array
     */
    public static function getPermissionIds(string $templateKey): array
    {
        $templates = self::getTemplates();
        if (!isset($templates[$templateKey])) {
            return [];
        }

        $permissionNames = $templates[$templateKey]['permissions'];
        $permissions = Permission::whereIn('name', $permissionNames)->get();
        
        return $permissions->pluck('id')->toArray();
    }

    /**
     * Get SVG icon path based on icon name
     *
     * @param string $iconName
     * @return string
     */
    public static function getIconPath(string $iconName): string
    {
        $icons = [
            'file-text' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'eye' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'building' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'clipboard-list' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        ];
        
        return $icons[$iconName] ?? $icons['file-text'];
    }
}
