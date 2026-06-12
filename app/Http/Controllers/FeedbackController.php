<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $feedbacks = Feedback::with(['colaborador', 'autor'])
            ->when($request->tipo, fn($q, $v) => $q->where('tipo', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $colaboradores = Colaborador::orderBy('nome')->get();

        return view('feedbacks.index', compact('feedbacks', 'colaboradores'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('feedbacks.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id'      => 'required|exists:colaboradores,id',
            'tipo'                => 'required|in:ELOGIO,CONSTRUTIVO,GERAL',
            'mensagem'            => 'required|string|max:3000',
            'visivel_colaborador' => 'nullable|boolean',
            'data'                => 'nullable|date',
        ]);

        $data['autor_id'] = auth()->id();
        $data['visivel_colaborador'] = $request->boolean('visivel_colaborador');
        $data['data'] = $data['data'] ?? now()->toDateString();

        Feedback::create($data);

        return redirect()->route('feedbacks.index')->with('success', 'Feedback registrado!');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('feedbacks.index')->with('success', 'Feedback removido.');
    }
}
