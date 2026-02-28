<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Models\CompanyUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DocumentAccessService
{
    /**
     * Check if a user can view document details
     *
     * @param Document $document
     * @param User|null $user
     * @return bool
     */
    public function canViewDocument(Document $document, User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Document uploader can always view their own documents
        if ($document->uploader === $user->id) {
            return true;
        }

        // Check if user is a workflow recipient
        if ($document->documentWorkflow()->where('recipient_id', $user->id)->exists()) {
            return true;
        }

        // Company admins can view all documents in their company
        if ($user->hasRole('company-admin')) {
            return $this->isInSameCompany($document, $user);
        }

        // Super admins can view everything
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Check based on classification
        switch ($document->classification) {
            case 'Public':
                // Public documents can be viewed by anyone in the same company
                return $this->isInSameCompany($document, $user);

            case 'Office Only':
                // Office Only documents can be viewed by users in the same office as the uploader
                return $this->isInSameOffice($document, $user);

            case 'Custom Offices':
                // Custom Offices documents can be viewed by users in the allowed offices
                return $this->isInAllowedOffice($document, $user);

            default:
                // For backward compatibility with old "Private" classification
                if ($document->classification === 'Private') {
                    // Check if user has specific permissions through allowed viewers
                    return $document->allowedViewers()->where('user_id', $user->id)->exists();
                }
                return false;
        }
    }

    /**
     * Check if user is in same company as document uploader
     *
     * @param Document $document
     * @param User $user
     * @return bool
     */
    private function isInSameCompany(Document $document, User $user): bool
    {
        // First check if we have both a document and a user
        if (!$document || !$user) {
            return false;
        }

        // Get the document uploader's company IDs
        $uploaderCompanyIds = CompanyUser::where('user_id', $document->uploader)
            ->pluck('company_id')
            ->toArray();

        // Get the user's company IDs
        $userCompanyIds = CompanyUser::where('user_id', $user->id)
            ->pluck('company_id')
            ->toArray();

        // Check if there are any companies in common
        return !empty(array_intersect($uploaderCompanyIds, $userCompanyIds));
    }

    /**
     * Check if user is in same office as document uploader
     *
     * @param Document $document
     * @param User $user
     * @return bool
     */
    private function isInSameOffice(Document $document, User $user): bool
    {
        $documentUploaderOffices = $document->user->offices()->pluck('offices.id');
        $userOffices = $user->offices()->pluck('offices.id');

        return $documentUploaderOffices->intersect($userOffices)->isNotEmpty();
    }

    /**
     * Check if user is in an office that's allowed to view the document
     *
     * @param Document $document
     * @param User $user
     * @return bool
     */
    private function isInAllowedOffice(Document $document, User $user): bool
    {
        $allowedOfficeIds = $document->allowedOffices()->pluck('office_id');
        $userOfficeIds = $user->offices()->pluck('offices.id');

        return $allowedOfficeIds->intersect($userOfficeIds)->isNotEmpty();
    }

    /**
     * Get filtered documents based on user's access level
     *
     * @param User|null $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAccessibleDocuments(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return Document::whereRaw('1 = 0'); // Return empty query
        }

        $query = Document::query();

        // Super admins can see everything
        if ($user->hasRole('super-admin')) {
            return $query;
        }

        // Company admins can see all documents in their own company only
        if ($user->hasRole('company-admin')) {
            // Get the admin's company ID
            $adminCompanyId = $user->companies()->first()->id ?? null;

            if (!$adminCompanyId) {
                return Document::whereRaw('1 = 0'); // No company, no access
            }

            // Return only documents from users in the same company
            return $query->whereHas('user.companies', function($q) use ($adminCompanyId) {
                $q->where('company_accounts.id', $adminCompanyId);
            });
        }

        // Regular users can only see their own uploaded documents
        // OR documents where they are a workflow recipient
        // OR documents classified as accessible to them
        if (!$user->hasRole('company-admin')) {
            $userId = $user->id;

            // Get the user's company IDs for company-based classification access
            $userCompanyIds = CompanyUser::where('user_id', $userId)
                ->pluck('company_id')
                ->toArray();

            // Get the user's office IDs for office-based classification access
            $userOfficeIds = $user->offices()->pluck('offices.id')->toArray();

            return $query->where(function ($q) use ($userId, $userCompanyIds, $userOfficeIds) {
                // Documents the user uploaded
                $q->where('uploader', $userId)
                  // Documents where the user is a workflow recipient
                  ->orWhereHas('documentWorkflow', function ($wq) use ($userId) {
                      $wq->where('recipient_id', $userId);
                  })
                  // Public documents from users in the same company
                  ->orWhere(function ($cq) use ($userCompanyIds) {
                      if (!empty($userCompanyIds)) {
                          $cq->where('classification', 'Public')
                             ->whereHas('user.companies', function ($uq) use ($userCompanyIds) {
                                 $uq->whereIn('company_accounts.id', $userCompanyIds);
                             });
                      }
                  })
                  // Office Only documents from users in the same office
                  ->orWhere(function ($oq) use ($userOfficeIds) {
                      if (!empty($userOfficeIds)) {
                          $oq->where('classification', 'Office Only')
                             ->whereHas('user.offices', function ($uoq) use ($userOfficeIds) {
                                 $uoq->whereIn('offices.id', $userOfficeIds);
                             });
                      }
                  });
            });
        }

        // For company admins, show all documents in their company
        $adminCompanyId = $user->companies()->first()->id ?? null;
        if (!$adminCompanyId) {
            return Document::whereRaw('1 = 0'); // No company, no access
        }
        return $query->whereHas('user.companies', function($q) use ($adminCompanyId) {
            $q->where('company_accounts.id', $adminCompanyId);
        });
    }

    /**
     * Get a human-readable description of document access level
     *
     * @param Document $document
     * @return string
     */
    public function getAccessDescription(Document $document): string
    {
        switch ($document->classification) {
            case 'Public':
                return 'Visible to all company users';
            case 'Office Only':
                return 'Visible to users in the same office';
            case 'Private':
                return 'Visible to selected users only';
            default:
                return 'Access level unknown';
        }
    }

    /**
     * Check if current user can edit a document
     *
     * @param Document $document
     * @param User|null $user
     * @return bool
     */
    public function canEditDocument(Document $document, User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Document uploader can edit their own documents
        if ($document->uploader === $user->id) {
            return true;
        }

        // Company admins can only edit their own documents
        if ($user->hasRole('company-admin')) {
            return $document->uploader === $user->id;
        }

        // Super admins can edit everything
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return false;
    }

    /**
     * Check if current user can delete a document
     *
     * @param Document $document
     * @param User|null $user
     * @return bool
     */
    public function canDeleteDocument(Document $document, User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Document uploader can delete their own documents
        if ($document->uploader === $user->id) {
            return true;
        }

        // Company admins can delete documents in their company but not edit them
        if ($user->hasRole('company-admin')) {
            return $this->isInSameCompany($document, $user);
        }

        // Super admins can delete everything
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return false;
    }
}
