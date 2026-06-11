<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
        'role',
        'empresa_id',
        'setor_id',
        'colaborador_id',
        'foto',
        'ativo',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'          => 'hashed',
        'ativo'             => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    /**
     * Alias em minúsculas do papel do usuário, usado pelas views (badges,
     * selects de "perfil"). Ex.: ADMIN → admin.
     */
    public function getPerfilAttribute(): string
    {
        return strtolower((string) $this->role);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    /** Rota do painel inicial conforme o papel do usuário. */
    public function painelRoute(): string
    {
        return $this->role === 'COLABORADOR' ? 'portal.index' : 'dashboard';
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'usuarios_empresas', 'user_id', 'empresa_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isRH(): bool
    {
        return $this->role === 'RH';
    }

    public function isGestor(): bool
    {
        return $this->role === 'GESTOR';
    }

    public function isColaborador(): bool
    {
        return $this->role === 'COLABORADOR';
    }

    public function canAccessEmpresa(int $empresaId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isRH()) {
            return $this->empresas()->where('empresas.id', $empresaId)->exists();
        }

        return $this->empresa_id == $empresaId;
    }
}
