<?php

namespace App\Filament\Widgets;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\HoraExtra;
use App\Models\Ocorrencia;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstatisticasGerais extends StatsOverviewWidget
{
    protected ?string $heading = 'Visão Geral';

    protected function getStats(): array
    {
        $ativos = Colaborador::where('status', 'ATIVO')->count();
        $total  = Colaborador::count();

        $ocorrenciasMes = Ocorrencia::whereMonth('data_ocorrencia', now()->month)
            ->whereYear('data_ocorrencia', now()->year)
            ->count();

        $horasMes = (float) HoraExtra::whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->sum('horas');

        return [
            Stat::make('Colaboradores ativos', $ativos)
                ->description($total . ' no total')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Empresas', Empresa::where('ativa', true)->count())
                ->description('grupos ativos')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Ocorrências no mês', $ocorrenciasMes)
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Horas extras no mês', rtrim(rtrim(number_format($horasMes, 1, ',', '.'), '0'), ',') . 'h')
                ->description('lançadas')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
        ];
    }
}
