<?php

namespace App\Models;

use App\Enums\ContactStatus;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'subject',
        'message',
        'project_type',
        'budget_range',
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
            'status' => ContactStatus::class,
            'submitted_at' => 'datetime',
        ];
    }
}
