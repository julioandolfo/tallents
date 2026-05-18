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
            'colaborador_id'  => 'required|exists:colaboradores,id',
            'cargo_novo_id'   => 'required|exists:cargos,id',
            'data_promocao'   => 'required|date',
            'salario_novo'    => 'required|numeric|min:0',
            'motivo'          => 'nullable|string|max:2000',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $data['cargo_anterior_id'] = $colaborador->cargo_id;
        $data['salario_anterior']  = $colaborador->salario;
        $data['registrado_por']    = auth()->id();

        DB::transaction(function () use ($data, $colaborador) {
            $promocao = Promocao::create($data);

            $colaborador->update([
                'cargo_id' => $data['cargo_novo_id'],
                'salario'  => $data['salario_novo'],
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
