<?php

namespace App\Filament\Resources\CaseStudies\Pages;

use App\Filament\Resources\CaseStudies\CaseStudyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseStudy extends CreateRecord
{
    protected static string $resource = CaseStudyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
