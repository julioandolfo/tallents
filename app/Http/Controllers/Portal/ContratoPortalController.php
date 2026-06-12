<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use Illuminate\Http\Request;

class ContratoPortalController extends Controller
{
    public function index()
    {
        $colaborador = auth()->user()->colaborador()->first();
        abort_unless($colaborador, 403, 'Conta não vinculada a um colaborador.');

        $contratos = $colaborador->contratos()->get();

        return view('portal.contratos', compact('contratos'));
    }

    public function show(Contrato $contrato)
    {
        $this->autorizar($contrato);

        return view('portal.contrato-show', compact('contrato'));
    }

    /** Assinatura digital: registra aceite, data/hora e IP. */
    public function assinar(Request $request, Contrato $contrato)
    {
        $this->autorizar($contrato);

        abort_unless($contrato->status === 'PENDENTE', 422, 'Contrato não está pendente de assinatura.');

        $request->validate(['aceite' => 'accepted']);

        $contrato->update([
            'status'        => 'ASSINADO',
            'assinado_em'   => now(),
            'assinatura_ip' => $request->ip(),
        ]);

        return redirect()->route('portal.contratos.show', $contrato)
            ->with('success', 'Contrato assinado com sucesso!');
    }

    /** Garante que o contrato pertence ao colaborador autenticado. */
    private function autorizar(Contrato $contrato): void
    {
        $colaborador = auth()->user()->colaborador()->first();
        abort_unless($colaborador && $contrato->colaborador_id === $colaborador->id, 403);
    }
}
