<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Candidato;
use App\Models\Empresa;
use App\Models\Vaga;
use Illuminate\Http\Request;

class VagaController extends Controller
{
    public function index(Request $request)
    {
        $vagas = Vaga::with(['empresa', 'cargo'])
            ->withCount('candidatos')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('vagas.index', compact('vagas', 'empresas'));
    }

    public function create()
    {
        return view('vagas.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['aberta_em'] = now();

        Vaga::create($data);

        return redirect()->route('vagas.index')->with('success', 'Vaga criada!');
    }

    public function show(Vaga $vaga)
    {
        $vaga->load(['empresa', 'cargo', 'responsavel']);

        // Agrupa candidatos por etapa para montar o funil (kanban).
        $candidatos = $vaga->candidatos()->get()->groupBy('etapa');

        return view('vagas.show', compact('vaga', 'candidatos'));
    }

    public function edit(Vaga $vaga)
    {
        return view('vagas.edit', array_merge(['vaga' => $vaga], $this->formData()));
    }

    public function update(Request $request, Vaga $vaga)
    {
        $vaga->update($this->validateData($request));

        return redirect()->route('vagas.show', $vaga)->with('success', 'Vaga atualizada!');
    }

    public function destroy(Vaga $vaga)
    {
        $vaga->delete();

        return redirect()->route('vagas.index')->with('success', 'Vaga removida.');
    }

    private function formData(): array
    {
        return [
            'empresas' => Empresa::where('ativa', true)->orderBy('nome')->get(),
            'cargos'   => Cargo::orderBy('nome')->get(),
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'empresa_id'     => 'nullable|exists:empresas,id',
            'cargo_id'       => 'nullable|exists:cargos,id',
            'titulo'         => 'required|string|max:255',
            'descricao'      => 'nullable|string',
            'requisitos'     => 'nullable|string',
            'modalidade'     => 'required|in:PRESENCIAL,REMOTO,HIBRIDO',
            'tipo_contrato'  => 'nullable|string|max:50',
            'salario_min'    => 'nullable|numeric|min:0',
            'salario_max'    => 'nullable|numeric|min:0',
            'quantidade'     => 'required|integer|min:1',
            'status'         => 'required|in:ABERTA,PAUSADA,FECHADA',
        ]);
    }
}
