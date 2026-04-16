<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Models\PermissionMetadata;
use Illuminate\Support\Facades\Auth;

/**
 * Centralized authorization service for the document archive system.
 * 
 * This service provides a unified interface for authorization checks
 * across the two-layer permission system:
 * 
 * Layer 1: Action Permissions (Spatie)
 *   - Controls what users can DO (create, edit, delete, etc.)
 *   - Managed via roles and permissions
 * 
 * Layer 2: Document Classification
 *   - Controls what documents users can SEE (Public, Office Only, Custom)
 *   - Managed via DocumentAccessService
 */
class AuthorizationService
{
    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
    }

    /**
     * Check if the current user has a specific permission
     *
     * @param string $permission Permission name (new or legacy format)
     * @return bool
     */
    public function can(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Try the permission as-is first
        if ($user->hasPermissionTo($permission)) {
            return true;
        }

        // If it's a legacy permission, try converting to new format
        if (PermissionMetadata::isLegacyPermission($permission)) {
            $newPermission = PermissionMetadata::convertToNew($permission);
            return $user->hasPermissionTo($newPermission);
        }

        // If it's a new permission, try the legacy equivalent
        $legacyPermission = PermissionMetadata::convertToLegacy($permission);
        if ($legacyPermission !== $permission) {
            return $user->hasPermissionTo($legacyPermission);
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the current user's permissions grouped by module
     *
     * @return array
     */
    public function getUserPermissionsByModule(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $groupedPermissions = PermissionMetadata::getGroupedPermissions();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $result = [];

        foreach ($groupedPermissions as $moduleKey => $module) {
            $result[$moduleKey] = [
                'label' => $module['label'],
                'description' => $module['description'],
                'permissions' => [],
            ];

            foreach ($module['permissions'] as $permName => $permMeta) {
                // Check both new and legacy permission names
                $hasPermission = in_array($permName, $userPermissions) || 
                                in_array($permMeta['legacy'], $userPermissions);
                
                if ($hasPermission) {
                    $result[$moduleKey]['permissions'][] = [
                        'name' => $permName,
                        'label' => $permMeta['label'],
                        'description' => $permMeta['description'],
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Check if user can access a specific document (combines both layers)
     *
     * @param Document $document
     * @param string $action Action to perform (view, edit, delete)
     * @param User|null $user
     * @return bool
     */
    public function canAccessDocument(Document $document, string $action = 'view', User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Layer 1: Check if user has the permission to perform the action
        $permissionMap = [
            'view' => 'documents.view',
            'create' => 'documents.create',
            'edit' => 'documents.edit',
            'delete' => 'documents.delete',
        ];

        $requiredPermission = $permissionMap[$action] ?? null;
        if ($requiredPermission && !$this->can($requiredPermission)) {
            return false;
        }

        // Layer 2: Check document visibility/access based on classification
        switch ($action) {
            case 'view':
                return $this->documentAccessService->canViewDocument($document, $user);
            case 'edit':
                return $this->documentAccessService->canEditDocument($document, $user);
            case 'delete':
                return $this->documentAccessService->canDeleteDocument($document, $user);
            default:
                return $this->documentAccessService->canViewDocument($document, $user);
        }
    }

    /**
     * Get a human-readable explanation of authorization layers
     *
     * @return array
     */
    public function getAuthorizationLayers(): array
    {
        return [
            'layer1' => [
                'name' => 'Action Permissions',
                'description' => 'Controls what actions users can perform (create, edit, delete, etc.)',
                'managed_by' => 'Roles and Permissions system (Spatie)',
                'scope' => 'Feature-level access control',
            ],
            'layer2' => [
                'name' => 'Document Classification',
                'description' => 'Controls which documents users can see based on visibility rules',
                'managed_by' => 'Document classification system (Public, Office Only, Custom Offices, Private)',
                'scope' => 'Resource-level access control',
            ],
        ];
    }

    /**
     * Check if user is authorized for a module
     *
     * @param string $module Module name (roles, users, documents, offices, audit)
     * @param string $action Action (view, create, edit, delete)
     * @return bool
     */
    public function canAccessModule(string $module, string $action = 'view'): bool
    {
        $permission = "{$module}.{$action}";
        return $this->can($permission);
    }
}
