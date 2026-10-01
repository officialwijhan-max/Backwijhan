<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Enums\ContactStatus;
use App\Filament\Actions\ChangeStatusAction;
use App\Filament\Resources\Contacts\ContactResource;
use App\Http\Requests\Admin\UpdateContactRequest;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make(ContactStatus::class, UpdateContactRequest::class),
            DeleteAction::make(),
        ];
    }
}
