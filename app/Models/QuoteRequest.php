<?php

namespace App\Models;

use App\Enums\QuoteRequestStatus;
use Database\Factories\QuoteRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    /** @use HasFactory<QuoteRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'project_type',
        'budget_range',
        'description',
        'locale',
        'status',
        'source',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuoteRequestStatus::class,
            'submitted_at' => 'datetime',
        ];
    }
}
