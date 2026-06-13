<?php

namespace Database\Seeders;

use App\Models\TemplateEmail;
use Illuminate\Database\Seeder;

class TemplateEmailSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'chave' => 'boas_vindas',
                'nome' => 'Boas-vindas ao colaborador',
                'assunto' => 'Bem-vindo(a) à {{ empresa }}',
                'variaveis' => 'nome, empresa, cargo, setor, email_login',
                'corpo' => "<p>Olá, <strong>{{ nome }}</strong>!</p>\n<p>É com prazer que damos as boas-vindas à equipe da {{ empresa }}.</p>\n<ul><li>Cargo: {{ cargo }}</li><li>Setor: {{ setor }}</li></ul>\n<p>Seja muito bem-vindo(a)!</p>",
            ],
            [
                'chave' => 'promocao',
                'nome' => 'Promoção registrada',
                'assunto' => 'Você foi promovido(a)! 🎉',
                'variaveis' => 'nome, cargo_novo, salario_novo, data',
                'corpo' => "<p>Olá, {{ nome }},</p>\n<p>Você foi promovido(a) em {{ data }}.</p>\n<ul><li>Novo cargo: {{ cargo_novo }}</li><li>Novo salário: {{ salario_novo }}</li></ul>\n<p>Parabéns!</p>",
            ],
            [
                'chave' => 'ocorrencia',
                'nome' => 'Registro de ocorrência',
                'assunto' => 'Registro de ocorrência',
                'variaveis' => 'nome, tipo, data',
                'corpo' => "<p>Olá, {{ nome }},</p>\n<p>Foi registrada uma ocorrência: <strong>{{ tipo }}</strong> em {{ data }}.</p>\n<p>Em caso de dúvidas, procure o RH.</p>",
            ],
            [
                'chave' => 'fechamento',
                'nome' => 'Demonstrativo de pagamento',
                'assunto' => 'Seu demonstrativo de pagamento',
                'variaveis' => 'nome, mes, ano, total',
                'corpo' => "<p>Olá, {{ nome }},</p>\n<p>Segue o resumo do seu pagamento de {{ mes }}/{{ ano }}.</p>\n<p>Total: <strong>{{ total }}</strong></p>\n<p>Em caso de divergência, procure o RH.</p>",
            ],
        ];

        foreach ($templates as $t) {
            TemplateEmail::firstOrCreate(['chave' => $t['chave']], $t + ['ativo' => true]);
        }
    }
}
