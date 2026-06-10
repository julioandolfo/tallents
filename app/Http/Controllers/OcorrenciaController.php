<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\TipoOcorrencia;
use Illuminate\Http\Request;

class OcorrenciaController extends Controller
{
    public function index(Request $request)
    {
        $ocorrencias = Ocorrencia::with(['colaborador.empresa', 'tipoOcorrencia', 'registradoPor'])
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->tipo_ocorrencia_id, fn($q, $v) => $q->where('tipo_ocorrencia_id', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data_ocorrencia', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data_ocorrencia', '<=', $v))
            ->orderByDesc('data_ocorrencia')
            ->paginate(20)
            ->withQueryString();

        $empresas          = Empresa::where('ativa', true)->orderBy('nome')->get();
        $tiposOcorrencias  = TipoOcorrencia::where('ativo', true)->orderBy('nome')->get();

        return view('ocorrencias.index', compact('ocorrencias', 'empresas', 'tiposOcorrencias'));
    }

    public function create()
    {
        $colaboradores    = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $tiposOcorrencias = TipoOcorrencia::where('ativo', true)->orderBy('nome')->get();

        return view('ocorrencias.create', compact('colaboradores', 'tiposOcorrencias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id'     => 'required|exists:colaboradores,id',
            'tipo_ocorrencia_id' => 'required|exists:tipos_ocorrencias,id',
            'data'               => 'required|date',
            'gravidade'          => 'nullable|string|max:50',
            'observacao'         => 'nullable|string|max:2000',
            'notificar_colaborador' => 'boolean',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $ocorrencia = Ocorrencia::create([
            'empresa_id'            => $colaborador->empresa_id,
            'colaborador_id'        => $colaborador->id,
            'tipo_ocorrencia_id'    => $data['tipo_ocorrencia_id'],
            'registrado_por'        => auth()->id(),
            'data_ocorrencia'       => $data['data'],
            'gravidade'             => $data['gravidade'] ?? null,
            'descricao'             => $data['observacao'] ?? null,
            'notificar_colaborador' => $request->boolean('notificar_colaborador'),
        ]);

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', 'Ocorrência registrada com sucesso!');
    }

    public function show(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load(['colaborador.empresa', 'tipoOcorrencia', 'registradoPor']);

        return view('ocorrencias.show', compact('ocorrencia'));
    }

    public function edit(Ocorrencia $ocorrencia)
    {
        $colaboradores    = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $tiposOcorrencias = TipoOcorrencia::where('ativo', true)->orderBy('nome')->get();

        return view('ocorrencias.edit', compact('ocorrencia', 'colaboradores', 'tiposOcorrencias'));
    }

    public function update(Request $request, Ocorrencia $ocorrencia)
    {
        $data = $request->validate([
            'colaborador_id'     => 'required|exists:colaboradores,id',
            'tipo_ocorrencia_id' => 'required|exists:tipos_ocorrencias,id',
            'data'               => 'required|date',
            'gravidade'          => 'nullable|string|max:50',
            'observacao'         => 'nullable|string|max:2000',
            'notificar_colaborador' => 'boolean',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $ocorrencia->update([
            'empresa_id'            => $colaborador->empresa_id,
            'colaborador_id'        => $colaborador->id,
            'tipo_ocorrencia_id'    => $data['tipo_ocorrencia_id'],
            'data_ocorrencia'       => $data['data'],
            'gravidade'             => $data['gravidade'] ?? null,
            'descricao'             => $data['observacao'] ?? null,
            'notificar_colaborador' => $request->boolean('notificar_colaborador'),
        ]);

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', 'Ocorrência atualizada com sucesso!');
    }

    public function destroy(Ocorrencia $ocorrencia)
    {
        $ocorrencia->delete();

        return redirect()
            ->route('ocorrencias.index')
            ->with('success', 'Ocorrência removida com sucesso!');
    }
}
