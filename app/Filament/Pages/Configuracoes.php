<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Configuracoes extends Page
{
    protected string $view = 'filament.pages.configuracoes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configurações';

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?int $navigationSort = 9;

    protected static ?string $title = 'Configurações';

    /** Configuração de e-mail atual (vinda do ambiente / Coolify). */
    public function getMailConfig(): array
    {
        return [
            'Driver'    => config('mail.default'),
            'Host'      => config('mail.mailers.smtp.host') ?: '—',
            'Porta'     => config('mail.mailers.smtp.port') ?: '—',
            'Usuário'   => config('mail.mailers.smtp.username') ?: '—',
            'Remetente' => config('mail.from.address') ?: '—',
            'Nome'      => config('mail.from.name') ?: '—',
        ];
    }
}
