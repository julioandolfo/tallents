<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Contrato;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $contratos = Contrato::with('colaborador')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('contratos.index', compact('contratos'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('contratos.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'titulo'         => 'required|string|max:255',
            'tipo'           => 'required|in:CONTRATO,ESTAGIO,ADITIVO,TERMO',
            'conteudo'       => 'nullable|string',
            'arquivo'        => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('arquivo')) {
            $data['arquivo'] = $request->file('arquivo')->store('contratos', 'public');
        }

        $data['criado_por'] = auth()->id();
        $data['status'] = 'PENDENTE';

        $contrato = Contrato::create($data);

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato criado!');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['colaborador', 'criadoPor']);

        return view('contratos.show', compact('contrato'));
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();

        return redirect()->route('contratos.index')->with('success', 'Contrato removido.');
    }

    /** Cancela ou reabre um contrato (uso administrativo). */
    public function status(Request $request, Contrato $contrato)
    {
        $data = $request->validate(['status' => 'required|in:PENDENTE,CANCELADO']);

        $contrato->update(['status' => $data['status']]);

        return back()->with('success', 'Status atualizado.');
    }
}
