<?php
namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Promocao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocaoController extends Controller
{
    public function index(Request $request)
    {
        $promocoes = Promocao::with(['colaborador.empresa', 'cargoAnterior', 'cargoNovo', 'registradoPor'])
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data_promocao', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data_promocao', '<=', $v))
            ->orderByDesc('data_promocao')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('promocoes.index', compact('promocoes', 'empresas'));
    }

    public function create()
    {
        $colaboradores = Colaborador::with('empresa')
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        $cargos = Cargo::where('ativo', true)->orderBy('nome')->get();

        return view('promocoes.create', compact('colaboradores', 'cargos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id'    => 'required|exists:colaboradores,id',
            'novo_cargo_id'     => 'required|exists:cargos,id',
            'cargo_anterior_id' => 'nullable|exists:cargos,id',
            'data'              => 'required|date',
            'novo_salario'      => 'required|numeric|min:0',
            'salario_anterior'  => 'nullable|numeric|min:0',
            'motivo'            => 'nullable|string|max:2000',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        DB::transaction(function () use ($data, $colaborador) {
            Promocao::create([
                'empresa_id'        => $colaborador->empresa_id,
                'colaborador_id'    => $colaborador->id,
                'registrado_por'    => auth()->id(),
                'tipo'              => 'PROMOCAO',
                'cargo_anterior_id' => $data['cargo_anterior_id'] ?? $colaborador->cargo_id,
                'cargo_novo_id'     => $data['novo_cargo_id'],
                'salario_anterior'  => $data['salario_anterior'] ?? $colaborador->salario ?? 0,
                'salario_novo'      => $data['novo_salario'],
                'data_promocao'     => $data['data'],
                'motivo'            => $data['motivo'] ?? null,
            ]);

            $colaborador->update([
                'cargo_id' => $data['novo_cargo_id'],
                'salario'  => $data['novo_salario'],
            ]);
        });

        return redirect()
            ->route('promocoes.index')
            ->with('success', 'Promoção registrada e salário do colaborador atualizado com sucesso!');
    }

    public function show(Promocao $promocao)
    {
        $promocao->load(['colaborador.empresa', 'cargoAnterior', 'cargoNovo', 'registradoPor']);

        return view('promocoes.show', compact('promocao'));
    }
}
