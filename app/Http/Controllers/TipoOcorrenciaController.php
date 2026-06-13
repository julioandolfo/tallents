<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\TipoOcorrencia;
use Illuminate\Http\Request;

class TipoOcorrenciaController extends Controller
{
    public function index(Request $request)
    {
        $tiposOcorrencias = TipoOcorrencia::with('empresa')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-ocorrencias.index', [
            'tipos'    => $tiposOcorrencias,
            'tiposOcorrencias' => $tiposOcorrencias,
            'empresas' => $empresas,
        ]);
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-ocorrencias.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id'  => 'nullable|exists:empresas,id',
            'nome'        => 'required|string|max:255',
            'descricao'   => 'nullable|string|max:1000',
            'categoria'   => 'nullable|in:ATRASO,FALTA,PONTO,OUTROS',
            'permite_tempo_atraso' => 'boolean',
            'permite_tipo_ponto'   => 'boolean',
            'gravidade'   => 'nullable|string|max:50',
            'ativo'       => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request);
        $data['ativo']      = $request->boolean('ativo', true);
        $data['permite_tempo_atraso'] = $request->boolean('permite_tempo_atraso');
        $data['permite_tipo_ponto']   = $request->boolean('permite_tipo_ponto');

        TipoOcorrencia::create($data);

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência cadastrado com sucesso!');
    }

    public function show(TipoOcorrencia $tiposOcorrencia)
    {
        return redirect()->route('tipos-ocorrencias.edit', $tiposOcorrencia);
    }

    public function edit(TipoOcorrencia $tiposOcorrencia)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-ocorrencias.edit', compact('tiposOcorrencia', 'empresas'));
    }

    public function update(Request $request, TipoOcorrencia $tiposOcorrencia)
    {
        $data = $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'categoria'  => 'nullable|in:ATRASO,FALTA,PONTO,OUTROS',
            'permite_tempo_atraso' => 'boolean',
            'permite_tipo_ponto'   => 'boolean',
            'gravidade'  => 'nullable|string|max:50',
            'ativo'      => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request, $tiposOcorrencia->empresa_id);
        $data['ativo']      = $request->boolean('ativo', true);
        $data['permite_tempo_atraso'] = $request->boolean('permite_tempo_atraso');
        $data['permite_tipo_ponto']   = $request->boolean('permite_tipo_ponto');

        $tiposOcorrencia->update($data);

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência atualizado com sucesso!');
    }

    public function destroy(TipoOcorrencia $tiposOcorrencia)
    {
        if ($tiposOcorrencia->ocorrencias()->exists()) {
            return redirect()
                ->route('tipos-ocorrencias.index')
                ->with('error', 'Não é possível remover: há ocorrências registradas com este tipo.');
        }

        $tiposOcorrencia->delete();

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência removido com sucesso!');
    }
}
