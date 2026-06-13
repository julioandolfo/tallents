<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\HoraExtra;
use App\Models\Ocorrencia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalColaboradores = Colaborador::count();
        $colaboradoresAtivos = Colaborador::where('status', 'ATIVO')->count();
        $totalEmpresas = Empresa::where('ativa', true)->count();

        $ocorrenciasMes = Ocorrencia::whereMonth('data_ocorrencia', now()->month)
            ->whereYear('data_ocorrencia', now()->year)
            ->count();

        $horasExtrasMes = (float) HoraExtra::whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->sum('horas');

        $ultimasOcorrencias = Ocorrencia::with(['colaborador', 'tipoOcorrencia'])
            ->latest('data_ocorrencia')
            ->latest('id')
            ->take(5)
            ->get();

        $colaboradoresRecentes = Colaborador::with('cargo')
            ->latest('id')
            ->take(5)
            ->get();

        // ─── Gráficos: últimos 6 meses ──────────────────────────────────────
        $meses = collect(range(5, 0))->map(fn($i) => now()->startOfMonth()->subMonths($i));
        $labelsMeses = $meses->map(fn($m) => $m->translatedFormat('M/y'))->values();

        $ocorrenciasPorMes = $meses->map(fn($m) => Ocorrencia::whereYear('data_ocorrencia', $m->year)
            ->whereMonth('data_ocorrencia', $m->month)->count())->values();

        $horasPorMes = $meses->map(fn($m) => (float) HoraExtra::whereYear('data', $m->year)
            ->whereMonth('data', $m->month)->sum('horas'))->values();

        // Colaboradores ativos por empresa (top 6).
        $porEmpresa = Colaborador::where('status', 'ATIVO')
            ->select('empresa_id', DB::raw('count(*) as total'))
            ->groupBy('empresa_id')
            ->with('empresa:id,nome')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // Ranking de ocorrências por colaborador (ano corrente, top 5).
        $rankingOcorrencias = Ocorrencia::select('colaborador_id', DB::raw('count(*) as total'))
            ->whereYear('data_ocorrencia', now()->year)
            ->groupBy('colaborador_id')
            ->with(['colaborador:id,nome,empresa_id', 'colaborador.empresa:id,nome'])
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $charts = [
            'meses'           => $labelsMeses,
            'ocorrenciasMes'  => $ocorrenciasPorMes,
            'horasMes'        => $horasPorMes,
            'empresasLabels'  => $porEmpresa->map(fn($r) => optional($r->empresa)->nome ?? '—')->values(),
            'empresasValores' => $porEmpresa->pluck('total')->values(),
        ];

        return view('dashboard.index', compact(
            'totalColaboradores',
            'colaboradoresAtivos',
            'totalEmpresas',
            'ocorrenciasMes',
            'horasExtrasMes',
            'ultimasOcorrencias',
            'colaboradoresRecentes',
            'charts',
            'rankingOcorrencias',
        ));
    }
}
