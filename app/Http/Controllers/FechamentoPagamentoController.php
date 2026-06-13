<?php
namespace App\Http\Controllers;

use App\Models\ColaboradorBonus;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\FechamentoPagamento;
use App\Models\HoraExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FechamentoPagamentoController extends Controller
{
    public function index(Request $request)
    {
        $fechamentos = FechamentoPagamento::with('empresa')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->ano, fn($q, $v) => $q->where('ano', $v))
            ->orderByDesc('ano')
            ->orderByDesc('mes')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('fechamentos.index', compact('fechamentos', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('fechamentos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'mes'        => 'required|integer|min:1|max:12',
            'ano'        => 'required|integer|min:2000|max:2100',
        ]);

        // Verifica se já existe fechamento para empresa/mes/ano
        $existe = FechamentoPagamento::where('empresa_id', $data['empresa_id'])
            ->where('mes', $data['mes'])
            ->where('ano', $data['ano'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['mes' => 'Já existe um fechamento para esta empresa neste período.'])->withInput();
        }

        $data['status']      = 'ABERTO';
        $data['criado_por']  = auth()->id();

        $fechamento = FechamentoPagamento::create($data);

        return redirect()
            ->route('fechamentos.show', $fechamento)
            ->with('success', 'Fechamento criado com sucesso!');
    }

    public function show(FechamentoPagamento $fechamento)
    {
        $fechamento->load(['empresa', 'itens.colaborador', 'criadoPor', 'fechadoPor']);

        return view('fechamentos.show', compact('fechamento'));
    }

    public function edit(FechamentoPagamento $fechamento)
    {
        if ($fechamento->status !== 'ABERTO') {
            return redirect()
                ->route('fechamentos.show', $fechamento)
                ->with('error', 'Só é possível editar fechamentos com status ABERTO.');
        }

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('fechamentos.edit', compact('fechamento', 'empresas'));
    }

    public function update(Request $request, FechamentoPagamento $fechamento)
    {
        if ($fechamento->status !== 'ABERTO') {
            return redirect()
                ->route('fechamentos.show', $fechamento)
                ->with('error', 'Não é possível editar um fechamento já processado.');
        }

        $data = $request->validate([
            'observacoes' => 'nullable|string|max:2000',
        ]);

        $fechamento->update($data);

        return redirect()
            ->route('fechamentos.show', $fechamento)
            ->with('success', 'Fechamento atualizado com sucesso!');
    }

    public function fechar(Request $request, FechamentoPagamento $fechamento)
    {
        if ($fechamento->status === 'FECHADO') {
            return redirect()
                ->route('fechamentos.show', $fechamento)
                ->with('error', 'Este fechamento já foi processado.');
        }

        DB::transaction(function () use ($fechamento) {
            $empresa = $fechamento->empresa;
            $mes     = $fechamento->mes;
            $ano     = $fechamento->ano;

            // Remove itens anteriores se houver
            $fechamento->itens()->delete();

            $totalGeral = 0;

            // Busca colaboradores ativos da empresa
            $colaboradores = Colaborador::where('empresa_id', $empresa->id)
                ->where('status', 'ATIVO')
                ->get();

            foreach ($colaboradores as $colaborador) {
                $salario = $colaborador->salario ?? 0;

                // Horas extras aprovadas do mês
                $totalHorasExtras = HoraExtra::where('colaborador_id', $colaborador->id)
                    ->where('status', 'APROVADO')
                    ->whereMonth('data', $mes)
                    ->whereYear('data', $ano)
                    ->sum('valor');

                // Bônus ativos
                $totalBonus = ColaboradorBonus::where('colaborador_id', $colaborador->id)
                    ->where('ativo', true)
                    ->whereMonth('created_at', $mes)
                    ->whereYear('created_at', $ano)
                    ->sum('valor');

                $totalColaborador = $salario + $totalHorasExtras + $totalBonus;
                $totalGeral      += $totalColaborador;

                $fechamento->itens()->create([
                    'colaborador_id'   => $colaborador->id,
                    'salario'          => $salario,
                    'horas_extras'     => $totalHorasExtras,
                    'bonus'            => $totalBonus,
                    'total'            => $totalColaborador,
                ]);
            }

            $fechamento->update([
                'status'      => 'FECHADO',
                'total_geral' => $totalGeral,
                'fechado_por' => auth()->id(),
                'fechado_em'  => now(),
            ]);
        });

        return redirect()
            ->route('fechamentos.show', $fechamento)
            ->with('success', 'Fechamento processado com sucesso!');
    }

    public function destroy(FechamentoPagamento $fechamento)
    {
        if ($fechamento->status !== 'ABERTO') {
            return redirect()
                ->route('fechamentos.index')
                ->with('error', 'Só é possível excluir fechamentos com status ABERTO.');
        }

        $fechamento->itens()->delete();
        $fechamento->delete();

        return redirect()
            ->route('fechamentos.index')
            ->with('success', 'Fechamento removido com sucesso!');
    }
}
