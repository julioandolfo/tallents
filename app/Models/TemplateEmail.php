<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateEmail extends Model
{
    protected $table = 'templates_email';

    protected $fillable = ['chave', 'nome', 'assunto', 'corpo', 'variaveis', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    /** Substitui {{ variaveis }} no assunto e corpo a partir do array de dados. */
    public function render(array $dados): array
    {
        return [
            'assunto' => $this->substituir($this->assunto, $dados),
            'corpo'   => $this->substituir($this->corpo, $dados),
        ];
    }

    private function substituir(string $texto, array $dados): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($dados) {
            return array_key_exists($m[1], $dados) ? (string) $dados[$m[1]] : $m[0];
        }, $texto);
    }

    public static function paraChave(string $chave): ?self
    {
        return static::where('chave', $chave)->where('ativo', true)->first();
    }
}
