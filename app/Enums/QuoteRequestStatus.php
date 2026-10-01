<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuoteRequestStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case ProposalSent = 'proposal_sent';
    case Won = 'won';
    case Lost = 'lost';
    case Spam = 'spam';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Reviewing => 'Reviewing',
            self::Contacted => 'Contacted',
            self::Qualified => 'Qualified',
            self::ProposalSent => 'Proposal sent',
            self::Won => 'Won',
            self::Lost => 'Lost',
            self::Spam => 'Spam',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Reviewing, self::Contacted => 'warning',
            self::Qualified, self::ProposalSent => 'primary',
            self::Won => 'success',
            self::Lost => 'gray',
            self::Spam => 'danger',
        };
    }
}
