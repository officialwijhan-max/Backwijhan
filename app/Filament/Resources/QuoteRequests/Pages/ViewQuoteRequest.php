<?php

namespace App\Filament\Resources\QuoteRequests\Pages;

use App\Enums\QuoteRequestStatus;
use App\Filament\Actions\ChangeStatusAction;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Http\Requests\Admin\UpdateQuoteRequestRequest;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteRequest extends ViewRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make(QuoteRequestStatus::class, UpdateQuoteRequestRequest::class),
            DeleteAction::make(),
        ];
    }
}
