<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\HoraExtra;
use Illuminate\Http\Request;

class HoraExtraController extends Controller
{
    public function index(Request $request)
    {
        $horasExtras = HoraExtra::with(['colaborador.empresa'])
            ->visivelPara($request->user())
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data', '<=', $v))
            ->orderByDesc('data')
            ->paginate(20)
            ->withQueryString();

        $totalValor  = HoraExtra::query()->visivelPara($request->user())
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->sum('valor');

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('horas-extras.index', compact('horasExtras', 'empresas', 'totalValor'));
    }

    public function create()
    {
        $colaboradores = Colaborador::with('empresa')
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('horas-extras.create', compact('colaboradores', 'empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'data'           => 'required|date',
            'quantidade'     => 'required|numeric|min:0.5|max:24',
            'motivo'         => 'nullable|string|max:1000',
            'observacao'     => 'nullable|string|max:1000',
            'status'         => 'nullable|string|max:50',
        ]);

        $colaborador = Colaborador::with('empresa')->findOrFail($data['colaborador_id']);

        // Valor hora = salário / 220; valor extra = valor_hora * (1 + percentual/100) * horas
        $percentual = $colaborador->empresa->percentual_hora_extra ?? 50;
        $horas      = $data['quantidade'];
        $valorHora  = ($colaborador->salario ?? 0) / 220;
        $valor      = round($valorHora * (1 + $percentual / 100) * $horas, 2);
        $status     = $data['status'] ?? 'pendente';

        $horaExtra = HoraExtra::create([
            'empresa_id'     => $colaborador->empresa_id,
            'colaborador_id' => $colaborador->id,
            'registrado_por' => auth()->id(),
            'data'           => $data['data'],
            'horas'          => $horas,
            'percentual'     => $percentual,
            'valor'          => $valor,
            'motivo'         => $data['motivo'] ?? null,
            'observacao'     => $data['observacao'] ?? null,
            'status'         => $status,
            'aprovado'       => $status === 'aprovado',
        ]);

        return redirect()
            ->route('horas-extras.show', $horaExtra)
            ->with('success', 'Hora extra registrada com sucesso!');
    }

    public function show(HoraExtra $horasExtra)
    {
        abort_unless($horasExtra->visivelPara(request()->user()), 403);

        $horasExtra->load(['colaborador.empresa', 'registradoPor']);

        return view('horas-extras.show', compact('horasExtra'));
    }

    public function edit(HoraExtra $horasExtra)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();

        return view('horas-extras.edit', [
            'horaExtra'     => $horasExtra,
            'horasExtra'    => $horasExtra,
            'colaboradores' => $colaboradores,
        ]);
    }

    public function update(Request $request, HoraExtra $horasExtra)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'data'           => 'required|date',
            'quantidade'     => 'required|numeric|min:0.5|max:24',
            'motivo'         => 'nullable|string|max:1000',
            'observacao'     => 'nullable|string|max:1000',
            'status'         => 'nullable|string|max:50',
        ]);

        $colaborador = Colaborador::with('empresa')->findOrFail($data['colaborador_id']);
        $percentual  = $colaborador->empresa->percentual_hora_extra ?? 50;
        $horas       = $data['quantidade'];
        $valorHora   = ($colaborador->salario ?? 0) / 220;
        $status      = $data['status'] ?? $horasExtra->status;

        $horasExtra->update([
            'empresa_id'     => $colaborador->empresa_id,
            'colaborador_id' => $colaborador->id,
            'data'           => $data['data'],
            'horas'          => $horas,
            'percentual'     => $percentual,
            'valor'          => round($valorHora * (1 + $percentual / 100) * $horas, 2),
            'motivo'         => $data['motivo'] ?? null,
            'observacao'     => $data['observacao'] ?? null,
            'status'         => $status,
            'aprovado'       => $status === 'aprovado',
        ]);

        return redirect()
            ->route('horas-extras.show', $horasExtra)
            ->with('success', 'Hora extra atualizada com sucesso!');
    }

    public function destroy(HoraExtra $horasExtra)
    {
        $horasExtra->delete();

        return redirect()
            ->route('horas-extras.index')
            ->with('success', 'Hora extra removida com sucesso!');
    }
}
