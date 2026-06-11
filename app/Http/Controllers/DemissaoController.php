<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Demissao;
use App\Models\Empresa;
use Illuminate\Http\Request;

class DemissaoController extends Controller
{
    public function index(Request $request)
    {
        $demissoes = Demissao::with(['colaborador.empresa', 'registradoPor'])
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->tipo, fn($q, $v) => $q->where('tipo', $v))
            ->orderByDesc('data_desligamento')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('demissoes.index', compact('demissoes', 'empresas'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('demissoes.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id'    => 'required|exists:colaboradores,id',
            'tipo'              => 'required|in:PEDIDO,SEM_JUSTA_CAUSA,JUSTA_CAUSA,FIM_CONTRATO,ACORDO',
            'data_desligamento' => 'required|date',
            'aviso_previo'      => 'nullable|in:TRABALHADO,INDENIZADO,DISPENSADO',
            'motivo'            => 'nullable|string|max:2000',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $demissao = Demissao::create([
            'colaborador_id'    => $colaborador->id,
            'empresa_id'        => $colaborador->empresa_id,
            'registrado_por'    => auth()->id(),
            'tipo'              => $data['tipo'],
            'data_desligamento' => $data['data_desligamento'],
            'aviso_previo'      => $data['aviso_previo'] ?? null,
            'motivo'            => $data['motivo'] ?? null,
        ]);

        // Desliga o colaborador: marca como inativo e registra a data de demissão.
        $colaborador->update([
            'status'        => 'INATIVO',
            'data_demissao' => $data['data_desligamento'],
        ]);

        return redirect()->route('demissoes.show', $demissao)
            ->with('success', 'Desligamento registrado com sucesso!');
    }

    public function show(Demissao $demissao)
    {
        $demissao->load(['colaborador.empresa', 'colaborador.cargo', 'registradoPor']);

        return view('demissoes.show', compact('demissao'));
    }

    public function destroy(Demissao $demissao)
    {
        // Reverte o desligamento: reativa o colaborador e limpa a data de demissão.
        $demissao->colaborador?->update([
            'status'        => 'ATIVO',
            'data_demissao' => null,
        ]);

        $demissao->delete();

        return redirect()->route('demissoes.index')
            ->with('success', 'Desligamento revertido e colaborador reativado.');
    }
}
