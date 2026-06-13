<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com a Autentique (assinatura eletrônica) via API GraphQL v2.
 *
 * Best-effort: se o token não estiver configurado ou ocorrer erro, registra
 * no log e retorna null/false sem quebrar o fluxo. Configure em
 * config/services.php (AUTENTIQUE_TOKEN / AUTENTIQUE_SANDBOX).
 */
class AutentiqueService
{
    private string $endpoint = 'https://api.autentique.com.br/v2/graphql';

    public function habilitado(): bool
    {
        return filled(config('services.autentique.token'));
    }

    private function token(): ?string
    {
        return config('services.autentique.token');
    }

    /**
     * Cria um documento na Autentique a partir de um PDF e o envia para
     * assinatura do signatário. Retorna ['id' => ..., 'url' => link] ou null.
     */
    public function enviarDocumento(string $nomeDocumento, string $pdfPath, string $signatarioNome, string $signatarioEmail): ?array
    {
        if (! $this->habilitado() || ! is_file($pdfPath)) {
            Log::info('Autentique não enviado: token ausente ou PDF inexistente.');
            return null;
        }

        $sandbox = config('services.autentique.sandbox') ? 'true' : 'false';

        $query = <<<GQL
        mutation CreateDocument(\$document: DocumentInput!, \$signers: [SignerInput!]!, \$file: Upload!) {
          createDocument(sandbox: {$sandbox}, document: \$document, signers: \$signers, file: \$file) {
            id
            name
            signatures { public_id email action { name } link { short_link } }
          }
        }
        GQL;

        $operations = json_encode([
            'query' => $query,
            'variables' => [
                'document' => ['name' => $nomeDocumento],
                'signers'  => [[
                    'email'  => $signatarioEmail,
                    'action' => 'SIGN',
                    'name'   => $signatarioNome,
                ]],
                'file' => null,
            ],
        ]);

        $map = json_encode(['0' => ['variables.file']]);

        try {
            $resp = Http::withToken($this->token())
                ->attach('0', file_get_contents($pdfPath), basename($pdfPath))
                ->post($this->endpoint, [
                    'operations' => $operations,
                    'map'        => $map,
                ]);

            if ($resp->failed()) {
                Log::warning('Autentique: falha ao criar documento.', ['status' => $resp->status(), 'body' => $resp->body()]);
                return null;
            }

            $doc = data_get($resp->json(), 'data.createDocument');
            if (! $doc) {
                Log::warning('Autentique: resposta sem documento.', ['body' => $resp->body()]);
                return null;
            }

            return [
                'id'  => $doc['id'] ?? null,
                'url' => data_get($doc, 'signatures.0.link.short_link'),
            ];
        } catch (\Throwable $e) {
            Log::warning('Autentique: exceção ao enviar documento: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Consulta um documento e indica se já está assinado por todos.
     * Retorna ['assinado' => bool, 'assinaturas' => array] ou null em erro.
     */
    public function consultarDocumento(string $documentId): ?array
    {
        if (! $this->habilitado()) {
            return null;
        }

        $query = <<<GQL
        query { document(id: "{$documentId}") {
            id
            signatures { email signed { created_at } }
        } }
        GQL;

        try {
            $resp = Http::withToken($this->token())->post($this->endpoint, ['query' => $query]);
            if ($resp->failed()) {
                return null;
            }
            $sigs = data_get($resp->json(), 'data.document.signatures', []);
            $assinado = ! empty($sigs) && collect($sigs)->every(fn($s) => ! empty(data_get($s, 'signed.created_at')));

            return ['assinado' => $assinado, 'assinaturas' => $sigs];
        } catch (\Throwable $e) {
            Log::warning('Autentique: exceção ao consultar documento: ' . $e->getMessage());
            return null;
        }
    }
}
