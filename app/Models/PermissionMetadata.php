<?php

namespace App\Models;

class PermissionMetadata
{
    /**
     * Get all permissions organized by module with metadata
     *
     * @return array
     */
    public static function getGroupedPermissions(): array
    {
        return [
            'roles' => [
                'label' => 'Roles & Permissions',
                'description' => 'Manage roles and assign permissions to users',
                'icon' => 'shield-check',
                'color' => 'blue',
                'permissions' => [
                    'roles.view' => [
                        'label' => 'View Roles',
                        'description' => 'View the list of roles and their permissions',
                        'legacy' => 'role-list',
                    ],
                    'roles.create' => [
                        'label' => 'Create Roles',
                        'description' => 'Create new roles and assign permissions',
                        'legacy' => 'role-create',
                    ],
                    'roles.edit' => [
                        'label' => 'Edit Roles',
                        'description' => 'Modify existing roles and their permissions',
                        'legacy' => 'role-edit',
                    ],
                    'roles.delete' => [
                        'label' => 'Delete Roles',
                        'description' => 'Delete roles from the system',
                        'legacy' => 'role-delete',
                    ],
                ]
            ],
            'users' => [
                'label' => 'User Management',
                'description' => 'Manage user accounts and access',
                'icon' => 'users',
                'color' => 'green',
                'permissions' => [
                    'users.view' => [
                        'label' => 'View Users',
                        'description' => 'View the list of users in the system',
                        'legacy' => 'user-list',
                    ],
                    'users.create' => [
                        'label' => 'Create Users',
                        'description' => 'Create new user accounts and invite users',
                        'legacy' => 'user-create',
                    ],
                    'users.edit' => [
                        'label' => 'Edit Users',
                        'description' => 'Modify user information and settings',
                        'legacy' => 'user-edit',
                    ],
                    'users.delete' => [
                        'label' => 'Delete Users',
                        'description' => 'Remove user accounts from the system',
                        'legacy' => 'user-delete',
                    ],
                ]
            ],
            'offices' => [
                'label' => 'Office Management',
                'description' => 'Manage offices and teams',
                'icon' => 'building',
                'color' => 'purple',
                'permissions' => [
                    'offices.view' => [
                        'label' => 'View Offices',
                        'description' => 'View the list of offices and teams',
                        'legacy' => 'office-list',
                    ],
                    'offices.create' => [
                        'label' => 'Create Offices',
                        'description' => 'Create new offices and teams',
                        'legacy' => 'office-create',
                    ],
                    'offices.edit' => [
                        'label' => 'Edit Offices',
                        'description' => 'Modify office information and settings',
                        'legacy' => 'office-edit',
                    ],
                    'offices.delete' => [
                        'label' => 'Delete Offices',
                        'description' => 'Remove offices from the system',
                        'legacy' => 'office-delete',
                    ],
                ]
            ],
            'documents' => [
                'label' => 'Document Actions',
                'description' => 'Perform actions on documents (visibility is controlled separately by classification)',
                'icon' => 'file-text',
                'color' => 'indigo',
                'note' => 'Document visibility is controlled by classification (Public/Office Only/Custom Offices) separately from these action permissions.',
                'permissions' => [
                    'documents.view' => [
                        'label' => 'View Documents',
                        'description' => 'Access the documents module and view documents',
                        'legacy' => 'document-list',
                    ],
                    'documents.create' => [
                        'label' => 'Create Documents',
                        'description' => 'Upload and create new documents',
                        'legacy' => 'document-create',
                    ],
                    'documents.edit' => [
                        'label' => 'Edit Documents',
                        'description' => 'Modify document information (subject to ownership rules)',
                        'legacy' => 'document-edit',
                    ],
                    'documents.delete' => [
                        'label' => 'Delete Documents',
                        'description' => 'Delete documents from the system',
                        'legacy' => 'document-delete',
                    ],
                    'documents.release' => [
                        'label' => 'Release Documents',
                        'description' => 'Mark documents as released in workflow',
                        'legacy' => 'document-release',
                    ],
                    'documents.receive' => [
                        'label' => 'Receive Documents',
                        'description' => 'Mark documents as received in workflow',
                        'legacy' => 'document-receive',
                    ],
                ]
            ],
            'audit' => [
                'label' => 'Audit & Logs',
                'description' => 'View system audit logs and activity',
                'icon' => 'clipboard-list',
                'color' => 'yellow',
                'permissions' => [
                    'audit.view' => [
                        'label' => 'View Audit Logs',
                        'description' => 'View system audit trails and activity logs',
                        'legacy' => 'audit-list',
                    ],
                ]
            ],
        ];
    }

    /**
     * Get all new permission names as a flat array
     *
     * @return array
     */
    public static function getAllNewPermissions(): array
    {
        $permissions = [];
        foreach (self::getGroupedPermissions() as $group) {
            foreach ($group['permissions'] as $permission => $metadata) {
                $permissions[] = $permission;
            }
        }
        return $permissions;
    }

    /**
     * Get mapping from legacy permission names to new names
     *
     * @return array
     */
    public static function getLegacyMapping(): array
    {
        $mapping = [];
        foreach (self::getGroupedPermissions() as $group) {
            foreach ($group['permissions'] as $newName => $metadata) {
                if (isset($metadata['legacy'])) {
                    $mapping[$metadata['legacy']] = $newName;
                }
            }
        }
        return $mapping;
    }

    /**
     * Get mapping from new permission names to legacy names
     *
     * @return array
     */
    public static function getReverseLegacyMapping(): array
    {
        return array_flip(self::getLegacyMapping());
    }

    /**
     * Check if a permission name is a legacy name
     *
     * @param string $permission
     * @return bool
     */
    public static function isLegacyPermission(string $permission): bool
    {
        return array_key_exists($permission, self::getLegacyMapping());
    }

    /**
     * Convert legacy permission name to new name
     *
     * @param string $permission
     * @return string
     */
    public static function convertToNew(string $permission): string
    {
        $mapping = self::getLegacyMapping();
        return $mapping[$permission] ?? $permission;
    }

    /**
     * Convert new permission name to legacy name
     *
     * @param string $permission
     * @return string
     */
    public static function convertToLegacy(string $permission): string
    {
        $mapping = self::getReverseLegacyMapping();
        return $mapping[$permission] ?? $permission;
    }
}
