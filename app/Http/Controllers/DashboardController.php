<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\HoraExtra;
use App\Models\Ocorrencia;

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

        return view('dashboard.index', compact(
            'totalColaboradores',
            'colaboradoresAtivos',
            'totalEmpresas',
            'ocorrenciasMes',
            'horasExtrasMes',
            'ultimasOcorrencias',
            'colaboradoresRecentes',
        ));
    }
}
