<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recebe eventos de assinatura da Autentique e atualiza o contrato.
 * Configure a URL deste webhook no painel da Autentique.
 */
class AutentiqueWebhookController extends Controller
{
    public function handle(Request $request, \App\Services\AutentiqueService $autentique)
    {
        // Verificação opcional por segredo compartilhado (configurado na UI).
        $segredo = $autentique->webhookSecret();
        if ($segredo && ! hash_equals($segredo, (string) $request->header('X-Webhook-Secret'))) {
            return response()->json(['ok' => false], 401);
        }

        // O payload da Autentique pode trazer o id do documento em chaves variadas.
        $documentId = $request->input('document.id')
            ?? $request->input('document_id')
            ?? $request->input('partes.document.id');

        $evento = (string) ($request->input('event') ?? $request->input('event.type') ?? '');

        if ($documentId) {
            $contrato = Contrato::where('autentique_document_id', $documentId)->first();
            if ($contrato && str_contains(strtolower($evento), 'sign')) {
                $contrato->update(['status' => 'ASSINADO', 'assinado_em' => now()]);
            }
        } else {
            Log::info('Autentique webhook recebido sem document id.', ['payload' => $request->all()]);
        }

        return response()->json(['ok' => true]);
    }
}
