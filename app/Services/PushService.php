<?php

namespace App\Services;

use App\Models\ConfiguracaoPush;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envio de notificações push via OneSignal (REST API).
 * Best-effort: se não configurado/ativo, apenas registra e segue.
 */
class PushService
{
    public function habilitado(): bool
    {
        $cfg = ConfiguracaoPush::first();

        return $cfg && $cfg->ativo && filled($cfg->onesignal_app_id) && filled($cfg->onesignal_api_key);
    }

    /**
     * Envia um push para os external_user_ids informados.
     * Retorna true se a chamada foi aceita; false se pulou/falhou.
     */
    public function enviar(array $externalUserIds, string $titulo, string $mensagem, array $dados = []): bool
    {
        $ids = array_values(array_filter(array_map('strval', $externalUserIds)));
        if (empty($ids) || ! $this->habilitado()) {
            return false;
        }

        $cfg = ConfiguracaoPush::first();

        try {
            $resp = Http::withHeaders([
                'Authorization' => 'Basic ' . $cfg->onesignal_api_key,
                'Content-Type'  => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id'                  => $cfg->onesignal_app_id,
                'include_external_user_ids' => $ids,
                'channel_for_external_user_ids' => 'push',
                'headings'                => ['en' => $titulo, 'pt' => $titulo],
                'contents'                => ['en' => $mensagem, 'pt' => $mensagem],
                'data'                    => $dados,
            ]);

            if ($resp->failed()) {
                Log::warning('OneSignal: falha ao enviar push.', ['status' => $resp->status(), 'body' => $resp->body()]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('OneSignal: exceção ao enviar push: ' . $e->getMessage());

            return false;
        }
    }
}
