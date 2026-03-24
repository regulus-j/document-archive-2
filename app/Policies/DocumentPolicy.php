<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentAccessService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class DocumentPolicy
{
    use HandlesAuthorization;

    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
    }

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): bool
    {
        return $this->documentAccessService->canViewDocument($document, $user);
    }

    /**
     * Determine whether the user can edit the document.
     */
    public function update(User $user, Document $document): bool
    {
        return $this->documentAccessService->canEditDocument($document, $user);
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        return $this->documentAccessService->canDeleteDocument($document, $user);
    }

    /**
     * Determine whether the user can restore the document.
     */
    public function restore(User $user, Document $document): bool
    {
        // Document owner can always restore their documents
        if ($document->uploader === $user->id) {
            return true;
        }

        // Get the user's roles directly from the database
        $isCompanyAdmin = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'company-admin')
            ->exists();

        if ($isCompanyAdmin) {
            // Check if the user and document are in the same company through the pivot table
            $userCompanyIds = DB::table('company_users')
                ->where('user_id', $user->id)
                ->pluck('company_id');

            $documentUploaderCompanyIds = DB::table('company_users')
                ->where('user_id', $document->uploader)
                ->pluck('company_id');

            return $userCompanyIds->intersect($documentUploaderCompanyIds)->isNotEmpty();
        }

        return false;
    }
}
