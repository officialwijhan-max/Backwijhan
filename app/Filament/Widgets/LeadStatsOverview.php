<?php

namespace App\Filament\Widgets;

use App\Enums\ContactStatus;
use App\Enums\QuoteRequestStatus;
use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\Contact;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        return [
            Stat::make('New contact requests', Contact::where('status', ContactStatus::New)->count())
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->url(ContactResource::getUrl('index')),
            Stat::make('New quote requests', QuoteRequest::where('status', QuoteRequestStatus::New)->count())
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->url(QuoteRequestResource::getUrl('index')),
        ];
    }
}
