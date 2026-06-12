<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $eventos = Evento::with('empresa')
            ->when($request->tipo, fn($q, $v) => $q->where('tipo', $v))
            ->when($request->passados, null, fn($q) => $q->where('inicio', '>=', now()->startOfDay()))
            ->orderBy('inicio')
            ->paginate(20)
            ->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('eventos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        Evento::create($this->validateData($request));

        return redirect()->route('eventos.index')->with('success', 'Evento criado!');
    }

    public function edit(Evento $evento)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('eventos.edit', compact('evento', 'empresas'));
    }

    public function update(Request $request, Evento $evento)
    {
        $evento->update($this->validateData($request));

        return redirect()->route('eventos.index')->with('success', 'Evento atualizado!');
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento removido.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'titulo'     => 'required|string|max:255',
            'descricao'  => 'nullable|string',
            'tipo'       => 'required|in:REUNIAO,TREINAMENTO,FOLGA,ANIVERSARIO,OUTRO',
            'inicio'     => 'required|date',
            'fim'        => 'nullable|date|after_or_equal:inicio',
            'local'      => 'nullable|string|max:255',
        ]);
    }
}
