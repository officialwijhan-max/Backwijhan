<?php

namespace App\Filament\Actions;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

/**
 * Lets an admin move a lead (contact / quote request) to another status.
 * The allowed values and validation are taken from the same admin
 * FormRequest the JSON API uses, so both admin surfaces stay consistent.
 */
class ChangeStatusAction
{
    /**
     * @param  class-string<BackedEnum>  $statusEnum
     * @param  class-string  $updateRequest  FormRequest whose rules() define `status`
     */
    public static function make(string $statusEnum, string $updateRequest): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('gray')
            ->modalWidth('md')
            ->authorize('update')
            ->fillForm(fn (Model $record): array => ['status' => $record->status->value])
            ->schema([
                Select::make('status')
                    ->options($statusEnum)
                    ->required()
                    ->rules((new $updateRequest)->rules()['status']),
            ])
            ->action(function (Model $record, array $data): void {
                $record->update(['status' => $data['status']]);
            })
            ->successNotificationTitle('Status updated');
    }
}
