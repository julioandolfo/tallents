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
            ->visivelPara($request->user())
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
        abort_unless($fechamento->visivelPara(request()->user()), 403);

        $fechamento->load(['empresa', 'itens.colaborador', 'criadoPor', 'fechadoPor']);

        // Colaboradores ativos da empresa, para seleção ao processar.
        $ativos = Colaborador::where('empresa_id', $fechamento->empresa_id)
            ->where('status', 'ATIVO')->orderBy('nome')->get();

        return view('fechamentos.show', compact('fechamento', 'ativos'));
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

        // Colaboradores selecionados (checkbox); vazio = todos os ativos da empresa.
        $selecionados = (array) $request->input('colaborador_ids', []);

        DB::transaction(function () use ($fechamento, $selecionados) {
            $empresa = $fechamento->empresa;
            $mes     = (int) $fechamento->mes;
            $ano     = (int) $fechamento->ano;

            // Preserva ajustes manuais (descontos/adicionais/obs) por colaborador
            // antes de recalcular as bases.
            $ajustes = $fechamento->itens()->get()
                ->keyBy('colaborador_id')
                ->map(fn($i) => ['descontos' => (float) $i->descontos, 'adicionais' => (float) $i->adicionais, 'observacoes' => $i->observacoes]);

            $fechamento->itens()->delete();

            $totalGeral = 0;

            $colaboradores = Colaborador::where('empresa_id', $empresa->id)
                ->where('status', 'ATIVO')
                ->when(! empty($selecionados), fn($q) => $q->whereIn('id', $selecionados))
                ->get();

            foreach ($colaboradores as $colaborador) {
                $salario = (float) ($colaborador->salario ?? 0);

                // Horas extras aprovadas do mês de referência.
                $totalHorasExtras = (float) HoraExtra::where('colaborador_id', $colaborador->id)
                    ->where('status', 'APROVADO')
                    ->whereMonth('data', $mes)
                    ->whereYear('data', $ano)
                    ->sum('valor');

                // Bônus vigentes no mês (por janela de datas, não por created_at).
                $totalBonus = (float) ColaboradorBonus::where('colaborador_id', $colaborador->id)
                    ->vigentesEm($mes, $ano)
                    ->sum('valor');

                $ajuste     = $ajustes[$colaborador->id] ?? ['descontos' => 0, 'adicionais' => 0, 'observacoes' => null];
                $descontos  = (float) $ajuste['descontos'];
                $adicionais = (float) $ajuste['adicionais'];

                $total = $salario + $totalHorasExtras + $totalBonus - $descontos + $adicionais;
                $totalGeral += $total;

                $fechamento->itens()->create([
                    'colaborador_id'     => $colaborador->id,
                    'salario_base'       => $salario,
                    'total_horas_extras' => $totalHorasExtras,
                    'total_bonus'        => $totalBonus,
                    'descontos'          => $descontos,
                    'adicionais'         => $adicionais,
                    'total'              => $total,
                    'observacoes'        => $ajuste['observacoes'],
                ]);
            }

            $fechamento->update([
                'status'      => 'FECHADO',
                'total_geral' => $totalGeral,
                'fechado_por' => auth()->id(),
                'fechado_em'  => now(),
            ]);
        });

        // Envia o demonstrativo de pagamento a cada colaborador (best-effort).
        $email = app(\App\Services\EmailService::class);
        if ($email->habilitado()) {
            foreach ($fechamento->itens()->with(['colaborador', 'fechamento'])->get() as $item) {
                if ($item->colaborador) {
                    $email->enviar($item->colaborador->emailContato(), new \App\Mail\FechamentoColaborador($item));
                }
            }
        }

        return redirect()
            ->route('fechamentos.show', $fechamento)
            ->with('success', 'Fechamento processado com sucesso!');
    }

    /** Edita descontos/adicionais/observações de um item e recalcula totais. */
    public function atualizarItem(Request $request, \App\Models\FechamentoPagamentoItem $item)
    {
        $fechamento = $item->fechamento;

        if ($fechamento->status === 'FECHADO') {
            return back()->with('error', 'Fechamento já processado — reabra para editar.');
        }

        $data = $request->validate([
            'descontos'   => 'nullable|numeric|min:0',
            'adicionais'  => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $item->descontos   = $data['descontos'] ?? 0;
        $item->adicionais  = $data['adicionais'] ?? 0;
        $item->observacoes = $data['observacoes'] ?? null;
        $item->recalcular();
        $item->save();

        // Recalcula o total geral do fechamento.
        $fechamento->update(['total_geral' => $fechamento->itens()->sum('total')]);

        return back()->with('success', 'Item atualizado.');
    }

    /** Reabre um fechamento FECHADO para edição. */
    public function reabrir(FechamentoPagamento $fechamento)
    {
        if ($fechamento->status === 'FECHADO') {
            $fechamento->update(['status' => 'ABERTO', 'fechado_por' => null, 'fechado_em' => null]);
        }

        return back()->with('success', 'Fechamento reaberto para edição.');
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
