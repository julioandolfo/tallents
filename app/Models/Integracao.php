<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integracao extends Model
{
    protected $table = 'integracoes';

    protected $fillable = [
        'provider', 'ativo', 'credenciais',
    ];

    protected $casts = [
        'ativo'       => 'boolean',
        'credenciais' => 'array',
    ];

    /**
     * Catálogo de integrações suportadas e os campos de credencial de cada uma.
     * As credenciais são preenchidas pelo cliente posteriormente.
     */
    public const PROVIDERS = [
        'WHATSAPP' => [
            'nome'   => 'WhatsApp (Cloud API)',
            'desc'   => 'Envio de notificações e comunicados via WhatsApp.',
            'campos' => [
                'phone_number_id' => 'Phone Number ID',
                'token'           => 'Access Token',
                'template'        => 'Template padrão (opcional)',
            ],
        ],
        'SLACK' => [
            'nome'   => 'Slack',
            'desc'   => 'Notificações em canais do Slack via Webhook.',
            'campos' => [
                'webhook_url' => 'Incoming Webhook URL',
                'canal'       => 'Canal padrão (ex: #rh)',
            ],
        ],
        'IA' => [
            'nome'   => 'Inteligência Artificial',
            'desc'   => 'Recursos de IA (resumos, sugestões) via API.',
            'campos' => [
                'provider' => 'Provedor (anthropic / openai)',
                'api_key'  => 'API Key',
                'modelo'   => 'Modelo (ex: claude-sonnet-4-6)',
            ],
        ],
        'AUTENTIQUE' => [
            'nome'   => 'Autentique',
            'desc'   => 'Assinatura eletrônica de contratos (autentique.com.br).',
            'campos' => [
                'token'          => 'API Token',
                'sandbox'        => 'Ambiente sandbox? (sim / nao)',
                'webhook_secret' => 'Segredo do webhook (opcional)',
            ],
        ],
    ];

    public function nome(): string
    {
        return self::PROVIDERS[$this->provider]['nome'] ?? $this->provider;
    }

    public function valor(string $campo): ?string
    {
        return $this->credenciais[$campo] ?? null;
    }

    /** Retorna a configuração de um provider, criando o registro se necessário. */
    public static function para(string $provider): self
    {
        return static::firstOrCreate(['provider' => $provider], ['ativo' => false, 'credenciais' => []]);
    }
}
