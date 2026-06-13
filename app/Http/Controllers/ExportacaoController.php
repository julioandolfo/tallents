<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\FechamentoPagamento;
use App\Models\HoraExtra;
use App\Models\Ocorrencia;
use App\Support\CsvExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportacaoController extends Controller
{
    // ─── Colaboradores ──────────────────────────────────────────────────────
    private function colaboradoresQuery(Request $request)
    {
        return Colaborador::with(['empresa', 'setor', 'cargo'])
            ->visivelPara($request->user())
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%{$v}%"))
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('nome');
    }

    public function colaboradoresCsv(Request $request)
    {
        $linhas = $this->colaboradoresQuery($request)->get()->map(fn($c) => [
            $c->nome, $c->cpf, optional($c->empresa)->nome, optional($c->setor)->nome,
            optional($c->cargo)->nome, $c->status,
            $c->salario ? number_format($c->salario, 2, ',', '.') : '',
        ]);

        return CsvExport::download('colaboradores.csv',
            ['Nome', 'CPF', 'Empresa', 'Setor', 'Cargo', 'Status', 'Salário'], $linhas);
    }

    public function colaboradoresPdf(Request $request)
    {
        $colaboradores = $this->colaboradoresQuery($request)->get();

        return Pdf::loadView('exportacoes.colaboradores', compact('colaboradores'))
            ->setPaper('a4', 'landscape')
            ->download('colaboradores.pdf');
    }

    // ─── Ocorrências ────────────────────────────────────────────────────────
    private function ocorrenciasQuery(Request $request)
    {
        return Ocorrencia::with(['colaborador.empresa', 'tipoOcorrencia'])
            ->visivelPara($request->user())
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($s) => $s->where('empresa_id', $v)))
            ->when($request->tipo_ocorrencia_id, fn($q, $v) => $q->where('tipo_ocorrencia_id', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data_ocorrencia', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data_ocorrencia', '<=', $v))
            ->orderByDesc('data_ocorrencia');
    }

    public function ocorrenciasCsv(Request $request)
    {
        $linhas = $this->ocorrenciasQuery($request)->get()->map(fn($o) => [
            optional($o->colaborador)->nome, optional(optional($o->colaborador)->empresa)->nome,
            optional($o->tipoOcorrencia)->nome, optional($o->data_ocorrencia)->format('d/m/Y'),
            $o->gravidade,
        ]);

        return CsvExport::download('ocorrencias.csv',
            ['Colaborador', 'Empresa', 'Tipo', 'Data', 'Gravidade'], $linhas);
    }

    public function ocorrenciasPdf(Request $request)
    {
        $ocorrencias = $this->ocorrenciasQuery($request)->get();

        return Pdf::loadView('exportacoes.ocorrencias', compact('ocorrencias'))->download('ocorrencias.pdf');
    }

    // ─── Horas extras ───────────────────────────────────────────────────────
    public function horasExtrasCsv(Request $request)
    {
        $linhas = HoraExtra::query()->with(['colaborador.empresa'])
            ->visivelPara($request->user())
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($s) => $s->where('empresa_id', $v)))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('data')
            ->get()->map(fn($h) => [
                optional($h->colaborador)->nome, optional(optional($h->colaborador)->empresa)->nome,
                optional($h->data)->format('d/m/Y'), $h->horas, number_format((float) $h->valor, 2, ',', '.'), $h->status,
            ]);

        return CsvExport::download('horas-extras.csv',
            ['Colaborador', 'Empresa', 'Data', 'Horas', 'Valor', 'Status'], $linhas);
    }

    // ─── Demonstrativo de fechamento (PDF) ──────────────────────────────────
    public function fechamentoPdf(Request $request, FechamentoPagamento $fechamento)
    {
        abort_unless($fechamento->visivelPara($request->user()), 403);
        $fechamento->load(['empresa', 'itens.colaborador']);

        return Pdf::loadView('exportacoes.fechamento', compact('fechamento'))
            ->setPaper('a4', 'landscape')
            ->download('fechamento-' . $fechamento->mes . '-' . $fechamento->ano . '.pdf');
    }
}
