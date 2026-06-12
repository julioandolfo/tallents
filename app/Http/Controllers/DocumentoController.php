<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Empresa;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $documentos = Documento::with('empresa')
            ->when($request->categoria, fn($q, $v) => $q->where('categoria', $v))
            ->orderBy('categoria')
            ->orderBy('titulo')
            ->paginate(20)
            ->withQueryString();

        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('documentos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('arquivo')) {
            $data['arquivo'] = $request->file('arquivo')->store('documentos', 'public');
        }

        Documento::create($data);

        return redirect()->route('documentos.index')->with('success', 'Documento adicionado!');
    }

    public function edit(Documento $documento)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('documentos.edit', compact('documento', 'empresas'));
    }

    public function update(Request $request, Documento $documento)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('arquivo')) {
            $data['arquivo'] = $request->file('arquivo')->store('documentos', 'public');
        }

        $documento->update($data);

        return redirect()->route('documentos.index')->with('success', 'Documento atualizado!');
    }

    public function destroy(Documento $documento)
    {
        $documento->delete();

        return redirect()->route('documentos.index')->with('success', 'Documento removido.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'titulo'     => 'required|string|max:255',
            'descricao'  => 'nullable|string',
            'categoria'  => 'required|in:MANUAL,POLITICA,FORMULARIO,OUTRO',
            'arquivo'    => 'nullable|file|max:10240',
            'url'        => 'nullable|url|max:500',
        ]);
    }
}
