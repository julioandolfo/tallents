<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colaborador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colaboradores';

    protected $fillable = [
        'empresa_id',
        'setor_id',
        'cargo_id',
        'nivel_hierarquico_id',
        'lider_id',
        'nome',
        'cpf',
        'rg',
        'pis',
        'ctps',
        'matricula',
        'data_nascimento',
        'sexo',
        'estado_civil',
        'email',
        'email_pessoal',
        'email_login',
        'telefone',
        'celular',
        'tipo_contrato',
        'carga_horaria',
        'data_admissao',
        'data_demissao',
        'salario',
        'status',
        'foto',
        'senha_hash',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'pix',
        'cnpj',
    ];

    protected $hidden = [
        'senha_hash',
    ];

    protected $casts = [
        'salario'         => 'decimal:2',
        'data_nascimento' => 'date',
        'data_admissao'   => 'date',
        'data_demissao'   => 'date',
        'status'          => 'string',
    ];

    /**
     * Alias do regime de trabalho usado pelas views; mapeia para a coluna
     * tipo_contrato.
     */
    public function getRegimeTrabalhoAttribute(): ?string
    {
        return $this->tipo_contrato;
    }

    /**
     * Usuário do sistema vinculado ao colaborador (quando há conta de acesso),
     * resolvido pelo e-mail de login. Retorna null se não houver vínculo.
     */
    public function getUserAttribute(): ?Usuario
    {
        $email = $this->email_login ?: $this->email;

        return $email ? Usuario::where('email', $email)->first() : null;
    }

    /** Melhor e-mail para contato/notificações do colaborador. */
    public function emailContato(): ?string
    {
        return $this->email_pessoal ?: ($this->email_login ?: $this->email);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function nivelHierarquico(): BelongsTo
    {
        return $this->belongsTo(NivelHierarquico::class);
    }

    public function lider(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'lider_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(Colaborador::class, 'lider_id');
    }

    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class);
    }

    public function advertencias(): HasMany
    {
        return $this->hasMany(Advertencia::class)->latest('data');
    }

    public function movimentacoesBanco(): HasMany
    {
        return $this->hasMany(BancoHorasMovimentacao::class)->latest('data');
    }

    /** Saldo do banco de horas (créditos - débitos), em horas. */
    public function saldoBancoHoras(): float
    {
        $cred = (float) $this->movimentacoesBanco()->where('tipo', 'CREDITO')->sum('horas');
        $deb  = (float) $this->movimentacoesBanco()->where('tipo', 'DEBITO')->sum('horas');

        return round($cred - $deb, 2);
    }

    public function horasExtras(): HasMany
    {
        return $this->hasMany(HoraExtra::class);
    }

    public function promocoes(): HasMany
    {
        return $this->hasMany(Promocao::class);
    }

    public function bonus(): HasMany
    {
        return $this->hasMany(ColaboradorBonus::class);
    }

    public function demissoes(): HasMany
    {
        return $this->hasMany(Demissao::class)->latest('data_desligamento');
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(Onboarding::class)->latest('data_inicio');
    }

    public function dependentes(): HasMany
    {
        return $this->hasMany(Dependente::class);
    }

    public function formacoes(): HasMany
    {
        return $this->hasMany(Formacao::class)->orderByDesc('ano_conclusao');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class)->latest('data');
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class)->latest('id');
    }

    public function pdis(): HasMany
    {
        return $this->hasMany(Pdi::class)->latest('id');
    }

    public function pontosMovimentacoes(): HasMany
    {
        return $this->hasMany(PontoMovimentacao::class)->latest('id');
    }

    public function resgates(): HasMany
    {
        return $this->hasMany(Resgate::class)->latest('id');
    }

    /** Saldo de pontos de gamificação do colaborador. */
    public function saldoPontos(): int
    {
        return (int) $this->pontosMovimentacoes()->sum('pontos');
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class)->latest('id');
    }
}
