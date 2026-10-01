<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\QuoteRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SubmissionsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Submissions, last 14 days';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $ago): Carbon => now()->subDays($ago)->startOfDay());

        return [
            'datasets' => [
                [
                    'label' => 'Contact requests',
                    'data' => $this->perDay(Contact::class, $days),
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Quote requests',
                    'data' => $this->perDay(QuoteRequest::class, $days),
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $days->map(fn (Carbon $day): string => $day->format('M j'))->all(),
        ];
    }

    /**
     * @param  class-string<Model>  $model
     * @param  \Illuminate\Support\Collection<int, Carbon>  $days
     * @return list<int>
     */
    private function perDay(string $model, $days): array
    {
        // Grouped in PHP (not SQL) so it behaves the same on MySQL and SQLite.
        $counts = $model::query()
            ->where('submitted_at', '>=', $days->first())
            ->pluck('submitted_at')
            ->countBy(fn ($submittedAt): string => Carbon::parse($submittedAt)->toDateString());

        return $days->map(fn (Carbon $day): int => (int) $counts->get($day->toDateString(), 0))->all();
    }
}
