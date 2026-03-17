<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Support\Str;

class DownloadFilenameService
{
    public function forDocument(Document $document): string
    {
        $document->loadMissing(['company', 'originatingOffice', 'office', 'user']);

        $extension = pathinfo((string) $document->path, PATHINFO_EXTENSION);
        $baseTitle = $this->filenameStem($document->title ?: 'document');

        return $this->build(
            companyName: $document->company->company_name ?? null,
            officeName: $document->originatingOffice->name ?? $document->office->name ?? null,
            uploader: $document->user,
            fileStem: $baseTitle,
            extension: $extension
        );
    }

    public function forAttachment(DocumentAttachment $attachment): string
    {
        $attachment->loadMissing(['document.company', 'document.originatingOffice', 'document.office', 'document.user', 'uploader']);

        $document = $attachment->document;
        $uploader = $attachment->uploader ?: $document?->user;
        $sourceName = $attachment->filename ?: ($document?->title ?: 'attachment');
        $fileStem = $this->filenameStem($sourceName);
        $extension = pathinfo((string) ($attachment->path ?: $attachment->filename), PATHINFO_EXTENSION);

        return $this->build(
            companyName: $document?->company?->company_name,
            officeName: $document?->originatingOffice?->name ?? $document?->office?->name,
            uploader: $uploader,
            fileStem: $fileStem,
            extension: $extension
        );
    }

    public function forVersion(DocumentVersion $version, Document $document): string
    {
        $document->loadMissing(['company', 'originatingOffice', 'office', 'user']);
        $version->loadMissing('uploader');

        $uploader = $version->uploader ?: $document->user;
        $sourceName = $version->original_filename ?: ($document->title ?: 'document-version');
        $fileStem = $this->filenameStem($sourceName);
        $extension = pathinfo((string) ($version->file_path ?: $version->original_filename), PATHINFO_EXTENSION);
        $versionTag = 'v' . ((int) $version->version_number ?: 1);

        return $this->build(
            companyName: $document->company->company_name ?? null,
            officeName: $document->originatingOffice->name ?? $document->office->name ?? null,
            uploader: $uploader,
            fileStem: $fileStem . '-' . $versionTag,
            extension: $extension
        );
    }

    private function build(?string $companyName, ?string $officeName, ?User $uploader, string $fileStem, ?string $extension): string
    {
        $uploaderName = trim(($uploader?->first_name ?? '') . ' ' . ($uploader?->last_name ?? ''));

        $parts = [
            $this->slugPart($companyName, 'company'),
            $this->slugPart($officeName, 'office'),
            $this->slugPart($uploaderName, 'uploader'),
            $this->slugPart($fileStem, 'document'),
        ];

        $filename = implode('_', $parts);
        $filename = Str::limit($filename, 180, '');

        $cleanExtension = strtolower((string) $extension);
        $cleanExtension = preg_replace('/[^a-z0-9]+/i', '', $cleanExtension ?? '') ?: null;

        return $cleanExtension ? ($filename . '.' . $cleanExtension) : $filename;
    }

    private function slugPart(?string $value, string $fallback): string
    {
        $slug = Str::slug(Str::ascii((string) $value), '-');

        return $slug !== '' ? $slug : $fallback;
    }

    private function filenameStem(string $value): string
    {
        $stem = pathinfo($value, PATHINFO_FILENAME);

        return $stem !== '' ? $stem : 'document';
    }
}