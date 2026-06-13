<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\OcorrenciaAnexo;
use App\Models\OcorrenciaComentario;
use App\Models\TipoOcorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OcorrenciaController extends Controller
{
    public function index(Request $request)
    {
        $ocorrencias = Ocorrencia::with(['colaborador.empresa', 'tipoOcorrencia', 'registradoPor'])
            ->visivelPara($request->user())
            ->when($request->search, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('nome', 'like', "%{$v}%")))
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
            'hora_ocorrencia'    => 'nullable|date_format:H:i',
            'tempo_atraso_minutos' => 'nullable|integer|min:0|max:1440',
            'tipo_ponto'         => 'nullable|in:ENTRADA,SAIDA,INTERVALO',
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
            'hora_ocorrencia'       => $data['hora_ocorrencia'] ?? null,
            'tempo_atraso_minutos'  => $data['tempo_atraso_minutos'] ?? null,
            'tipo_ponto'            => $data['tipo_ponto'] ?? null,
            'gravidade'             => $data['gravidade'] ?? null,
            'descricao'             => $data['observacao'] ?? null,
            'notificar_colaborador' => $request->boolean('notificar_colaborador'),
        ]);

        $ocorrencia->registrarHistorico('Ocorrência registrada');

        // Notifica o colaborador por e-mail e push, se solicitado (best-effort).
        if ($ocorrencia->notificar_colaborador) {
            $ocorrencia->load(['colaborador', 'tipoOcorrencia']);
            app(\App\Services\EmailService::class)->enviar(
                $colaborador->emailContato(),
                new \App\Mail\OcorrenciaRegistrada($ocorrencia)
            );
            app(\App\Services\PushService::class)->enviar(
                [$colaborador->id],
                'Nova ocorrência registrada',
                (optional($ocorrencia->tipoOcorrencia)->nome ?? 'Ocorrência') . ' em ' . optional($ocorrencia->data_ocorrencia)->format('d/m/Y'),
                ['ocorrencia_id' => $ocorrencia->id]
            );
        }

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', 'Ocorrência registrada com sucesso!');
    }

    public function show(Ocorrencia $ocorrencia)
    {
        abort_unless($ocorrencia->visivelPara(request()->user()), 403);

        $ocorrencia->load([
            'colaborador.empresa', 'tipoOcorrencia', 'registradoPor',
            'anexos.usuario', 'comentarios.usuario', 'historico.usuario',
        ]);

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
            'hora_ocorrencia'    => 'nullable|date_format:H:i',
            'tempo_atraso_minutos' => 'nullable|integer|min:0|max:1440',
            'tipo_ponto'         => 'nullable|in:ENTRADA,SAIDA,INTERVALO',
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
            'hora_ocorrencia'       => $data['hora_ocorrencia'] ?? null,
            'tempo_atraso_minutos'  => $data['tempo_atraso_minutos'] ?? null,
            'tipo_ponto'            => $data['tipo_ponto'] ?? null,
            'gravidade'             => $data['gravidade'] ?? null,
            'descricao'             => $data['observacao'] ?? null,
            'notificar_colaborador' => $request->boolean('notificar_colaborador'),
        ]);

        $ocorrencia->registrarHistorico('Ocorrência atualizada');

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', 'Ocorrência atualizada com sucesso!');
    }

    public function destroy(Ocorrencia $ocorrencia)
    {
        foreach ($ocorrencia->anexos as $anexo) {
            Storage::disk('public')->delete($anexo->caminho);
        }

        $ocorrencia->delete();

        return redirect()
            ->route('ocorrencias.index')
            ->with('success', 'Ocorrência removida com sucesso!');
    }

    /** Relatório agregado de ocorrências (por tipo, gravidade e colaborador). */
    public function relatorio(Request $request)
    {
        $base = Ocorrencia::query()
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->tipo_ocorrencia_id, fn($q, $v) => $q->where('tipo_ocorrencia_id', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data_ocorrencia', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data_ocorrencia', '<=', $v));

        $total = (clone $base)->count();

        $porGravidade = (clone $base)->selectRaw('gravidade, COUNT(*) as total')
            ->groupBy('gravidade')->pluck('total', 'gravidade');

        $porTipo = (clone $base)->with('tipoOcorrencia')
            ->selectRaw('tipo_ocorrencia_id, COUNT(*) as total')
            ->groupBy('tipo_ocorrencia_id')->orderByDesc('total')->get();

        $porColaborador = (clone $base)->with('colaborador')
            ->selectRaw('colaborador_id, COUNT(*) as total')
            ->groupBy('colaborador_id')->orderByDesc('total')->take(15)->get();

        $totalAtraso = (clone $base)->sum('tempo_atraso_minutos');

        $empresas         = Empresa::where('ativa', true)->orderBy('nome')->get();
        $tiposOcorrencias = TipoOcorrencia::orderBy('nome')->get();
        $colaboradores    = Colaborador::orderBy('nome')->get();

        return view('ocorrencias.relatorio', compact(
            'total', 'porGravidade', 'porTipo', 'porColaborador', 'totalAtraso',
            'empresas', 'tiposOcorrencias', 'colaboradores'
        ));
    }

    // ─── Comentários ──────────────────────────────────────────────────────────
    public function adicionarComentario(Request $request, Ocorrencia $ocorrencia)
    {
        $data = $request->validate(['comentario' => 'required|string|max:2000']);

        $ocorrencia->comentarios()->create([
            'usuario_id' => auth()->id(),
            'comentario' => $data['comentario'],
        ]);
        $ocorrencia->registrarHistorico('Comentário adicionado');

        return back()->with('success', 'Comentário adicionado.');
    }

    public function removerComentario(OcorrenciaComentario $comentario)
    {
        $ocorrencia = $comentario->ocorrencia;
        $comentario->delete();
        $ocorrencia?->registrarHistorico('Comentário removido');

        return back()->with('success', 'Comentário removido.');
    }

    // ─── Anexos ───────────────────────────────────────────────────────────────
    public function adicionarAnexo(Request $request, Ocorrencia $ocorrencia)
    {
        $request->validate(['arquivo' => 'required|file|max:5120']); // 5 MB

        $file = $request->file('arquivo');
        $caminho = $file->store("ocorrencias/{$ocorrencia->id}", 'public');

        $ocorrencia->anexos()->create([
            'usuario_id'    => auth()->id(),
            'nome_original' => $file->getClientOriginalName(),
            'caminho'       => $caminho,
            'mime'          => $file->getClientMimeType(),
            'tamanho'       => $file->getSize(),
        ]);
        $ocorrencia->registrarHistorico('Anexo adicionado', $file->getClientOriginalName());

        return back()->with('success', 'Anexo enviado.');
    }

    public function removerAnexo(OcorrenciaAnexo $anexo)
    {
        $ocorrencia = $anexo->ocorrencia;
        Storage::disk('public')->delete($anexo->caminho);
        $anexo->delete();
        $ocorrencia?->registrarHistorico('Anexo removido');

        return back()->with('success', 'Anexo removido.');
    }
}
