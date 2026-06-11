<?php
namespace App\Http\Controllers;

use App\Models\Advertencia;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;

class AdvertenciaController extends Controller
{
    public function index(Request $request)
    {
        $advertencias = Advertencia::with(['colaborador.empresa', 'aplicadaPor'])
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($s) => $s->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->tipo, fn($q, $v) => $q->where('tipo', $v))
            ->orderByDesc('data')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('advertencias.index', compact('advertencias', 'empresas'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $ocorrencia = $request->ocorrencia_id ? Ocorrencia::find($request->ocorrencia_id) : null;
        $colaboradorId = $request->colaborador_id ?? optional($ocorrencia)->colaborador_id;

        return view('advertencias.create', compact('colaboradores', 'ocorrencia', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'ocorrencia_id'  => 'nullable|exists:ocorrencias,id',
            'tipo'           => 'required|in:VERBAL,ESCRITA,SUSPENSAO',
            'motivo'         => 'required|string|max:2000',
            'data'           => 'required|date',
            'dias_suspensao' => 'nullable|integer|min:1|max:30',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $advertencia = Advertencia::create([
            'empresa_id'     => $colaborador->empresa_id,
            'colaborador_id' => $colaborador->id,
            'ocorrencia_id'  => $data['ocorrencia_id'] ?? null,
            'aplicada_por'   => auth()->id(),
            'tipo'           => $data['tipo'],
            'motivo'         => $data['motivo'],
            'data'           => $data['data'],
            'dias_suspensao' => $data['tipo'] === 'SUSPENSAO' ? ($data['dias_suspensao'] ?? null) : null,
        ]);

        // Registra no histórico da ocorrência de origem, se houver.
        if ($advertencia->ocorrencia_id) {
            optional(Ocorrencia::find($advertencia->ocorrencia_id))
                ?->registrarHistorico('Advertência gerada', $advertencia->tipoLabel());
        }

        return redirect()->route('advertencias.show', $advertencia)
            ->with('success', 'Advertência registrada com sucesso!');
    }

    public function show(Advertencia $advertencia)
    {
        $advertencia->load(['colaborador.empresa', 'aplicadaPor', 'ocorrencia']);

        return view('advertencias.show', compact('advertencia'));
    }

    public function destroy(Advertencia $advertencia)
    {
        $advertencia->delete();

        return redirect()->route('advertencias.index')
            ->with('success', 'Advertência removida.');
    }
}
