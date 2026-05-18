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

        return view('tipos-ocorrencias.index', compact('tiposOcorrencias', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-ocorrencias.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id'  => 'required|exists:empresas,id',
            'nome'        => 'required|string|max:255',
            'descricao'   => 'nullable|string|max:1000',
            'gravidade'   => 'nullable|in:LEVE,MEDIA,GRAVE,GRAVISSIMA',
            'ativo'       => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        TipoOcorrencia::create($data);

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência cadastrado com sucesso!');
    }

    public function show(TipoOcorrencia $tiposOcorrencia)
    {
        $tiposOcorrencia->load('empresa');

        return view('tipos-ocorrencias.show', compact('tiposOcorrencia'));
    }

    public function edit(TipoOcorrencia $tiposOcorrencia)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-ocorrencias.edit', compact('tiposOcorrencia', 'empresas'));
    }

    public function update(Request $request, TipoOcorrencia $tiposOcorrencia)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'gravidade'  => 'nullable|in:LEVE,MEDIA,GRAVE,GRAVISSIMA',
            'ativo'      => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo');

        $tiposOcorrencia->update($data);

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência atualizado com sucesso!');
    }

    public function destroy(TipoOcorrencia $tiposOcorrencia)
    {
        $tiposOcorrencia->delete();

        return redirect()
            ->route('tipos-ocorrencias.index')
            ->with('success', 'Tipo de ocorrência removido com sucesso!');
    }
}
