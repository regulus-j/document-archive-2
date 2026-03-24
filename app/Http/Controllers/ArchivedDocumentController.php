<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Office;
use App\Models\Document;

class ArchivedDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $company = $user->companies()->first();
        if (!$company) {
            $teams = collect();
            $documents = collect();
            $selectedTeamId = null;
        } else {
            $teams = $company->offices()->get();
            $selectedTeamId = $request->input('team_id', $teams->first()?->id);
            $documents = $selectedTeamId
                ? Document::whereIn('uploader', Office::find($selectedTeamId)?->users->pluck('id') ?? [])
                    ->where('company_id', $company->id)
                    ->where('is_archived', true)
                    ->orderByDesc('updated_at')
                    ->paginate(15)->appends(['team_id' => $selectedTeamId])
                : collect();
        }
        return view('archived_documents.index', compact('teams', 'documents', 'selectedTeamId'));
    }
}
