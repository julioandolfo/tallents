<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Empresa;
use Illuminate\Http\Request;

class ComunicadoController extends Controller
{
    public function index(Request $request)
    {
        $comunicados = Comunicado::with(['autor', 'empresa'])
            ->when($request->categoria, fn($q, $v) => $q->where('categoria', $v))
            ->orderByDesc('destaque')
            ->orderByDesc('publicado_em')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('comunicados.index', compact('comunicados', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('comunicados.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['autor_id'] = auth()->id();
        $data['publicado_em'] = ($data['publicado'] ?? false) ? now() : null;

        Comunicado::create($data);

        return redirect()->route('comunicados.index')
            ->with('success', 'Comunicado publicado!');
    }

    public function show(Comunicado $comunicado)
    {
        $comunicado->load(['autor', 'empresa']);

        return view('comunicados.show', compact('comunicado'));
    }

    public function edit(Comunicado $comunicado)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('comunicados.edit', compact('comunicado', 'empresas'));
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        $data = $this->validateData($request);

        // Marca a data de publicação na primeira vez que é publicado.
        if (($data['publicado'] ?? false) && ! $comunicado->publicado_em) {
            $data['publicado_em'] = now();
        }

        $comunicado->update($data);

        return redirect()->route('comunicados.show', $comunicado)
            ->with('success', 'Comunicado atualizado!');
    }

    public function destroy(Comunicado $comunicado)
    {
        $comunicado->delete();

        return redirect()->route('comunicados.index')
            ->with('success', 'Comunicado removido.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'titulo'     => 'required|string|max:255',
            'conteudo'   => 'required|string',
            'categoria'  => 'required|in:GERAL,RH,EVENTO,URGENTE',
            'destaque'   => 'nullable|boolean',
            'publicado'  => 'nullable|boolean',
        ]);

        $data['destaque'] = $request->boolean('destaque');
        $data['publicado'] = $request->boolean('publicado');

        return $data;
    }
}
