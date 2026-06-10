<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CnpjApiController extends Controller
{
    /**
     * Consulta dados de CNPJ na ReceitaWS e retorna formatado.
     */
    public function show(string $cnpj)
    {
        // Remove formatação para consulta
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpjLimpo) !== 14) {
            return response()->json(['error' => 'CNPJ inválido. Informe 14 dígitos.'], 422);
        }

        $response = Http::timeout(10)->get("https://receitaws.com.br/v1/cnpj/{$cnpjLimpo}");

        if ($response->failed()) {
            return response()->json(['error' => 'Não foi possível consultar o CNPJ. Tente novamente.'], 502);
        }

        $dados = $response->json();

        if (isset($dados['status']) && $dados['status'] === 'ERROR') {
            return response()->json(['error' => $dados['message'] ?? 'CNPJ não encontrado.'], 404);
        }

        $formatado = [
            'cnpj'          => $dados['cnpj'] ?? null,
            'nome'          => $dados['nome'] ?? null,
            'fantasia'      => $dados['fantasia'] ?? null,
            'email'         => $dados['email'] ?? null,
            'telefone'      => $dados['telefone'] ?? null,
            'situacao'      => $dados['situacao'] ?? null,
            'tipo'          => $dados['tipo'] ?? null,
            'porte'         => $dados['porte'] ?? null,
            'natureza_juridica' => $dados['natureza_juridica'] ?? null,
            'abertura'      => $dados['abertura'] ?? null,
            'endereco'      => implode(', ', array_filter([
                ($dados['logradouro'] ?? '') . ', ' . ($dados['numero'] ?? ''),
                $dados['complemento'] ?? '',
                $dados['bairro'] ?? '',
                $dados['municipio'] ?? '',
                $dados['uf'] ?? '',
                $dados['cep'] ?? '',
            ])),
            'logradouro'    => $dados['logradouro'] ?? null,
            'numero'        => $dados['numero'] ?? null,
            'complemento'   => $dados['complemento'] ?? null,
            'bairro'        => $dados['bairro'] ?? null,
            'municipio'     => $dados['municipio'] ?? null,
            'uf'            => $dados['uf'] ?? null,
            'cep'           => $dados['cep'] ?? null,
            'capital_social'=> $dados['capital_social'] ?? null,
            'atividade_principal' => $dados['atividade_principal'][0]['text'] ?? null,
        ];

        return response()->json($formatado);
    }
}
