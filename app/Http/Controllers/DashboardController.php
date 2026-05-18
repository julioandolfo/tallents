<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_colaboradores' => Colaborador::where('status', 'ATIVO')->count(),
            'total_empresas'      => Empresa::where('ativa', true)->count(),
            'ocorrencias_mes'     => Ocorrencia::whereMonth('data_ocorrencia', now()->month)->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
