<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importa os dados do sistema antigo (rh-privus) a partir de um dump .sql,
 * mapeando o schema legado para o schema do app novo (tallents). Preserva os
 * IDs (mantendo os relacionamentos) e os hashes de senha (bcrypt), de modo que
 * os usuários continuam logando com a mesma senha.
 *
 * Uso:  php artisan migrar:legado /caminho/para/privus_rh.sql
 *
 * Só migra as tabelas do núcleo de RH que existem no app novo. Módulos do
 * sistema antigo sem equivalente aqui (LMS, chat, recrutamento, etc.) são
 * ignorados.
 */
class MigrarLegado extends Command
{
    protected $signature = 'migrar:legado {arquivo : Caminho do dump .sql do sistema antigo} {--force : Não pedir confirmação (uso em deploy automatizado)} {--apenas-vinculos : Apenas vincula users.colaborador_id a partir do dump (não reimporta nada)}';
    protected $description = 'Importa os dados do sistema antigo (rh-privus) para o app novo a partir de um dump .sql';

    /** @var array<int,int> mapa colaborador_id => empresa_id (para tabelas filhas sem empresa_id) */
    private array $colabEmpresa = [];

    private int $defaultEmpresaId = 1;

    public function handle(): int
    {
        $path = $this->argument('arquivo');
        if (! is_file($path)) {
            $this->error("Arquivo não encontrado: $path");
            return self::FAILURE;
        }

        $this->info('Lendo dump...');
        $sql = file_get_contents($path);
        if ($sql === false || $sql === '') {
            $this->error('Não foi possível ler o arquivo (vazio?).');
            return self::FAILURE;
        }

        // Modo leve: só (re)vincula users.colaborador_id, sem apagar/reimportar.
        if ($this->option('apenas-vinculos')) {
            $n = $this->vincularColaboradores($sql);
            $this->info("✅ Vínculos atualizados: {$n} usuário(s) ligado(s) ao colaborador.");
            return self::SUCCESS;
        }

        if (! $this->option('force')
            && ! $this->confirm('Isto vai APAGAR os dados atuais das tabelas de RH e substituir pelos do sistema antigo. Continuar?', true)) {
            return self::SUCCESS;
        }

        $driver = DB::connection()->getDriverName();
        $this->fkChecks($driver, false);

        try {
            DB::transaction(function () use ($sql) {
                $this->limparDestino();

                $this->migrarEmpresas($sql);
                $this->migrarNiveis($sql);
                $this->migrarSetores($sql);
                $this->migrarCargos($sql);
                $this->migrarTiposBonus($sql);
                $this->migrarTiposOcorrencias($sql);
                $this->migrarColaboradores($sql);
                $this->migrarUsuarios($sql);
                $this->migrarUsuariosEmpresas($sql);
                $this->migrarOcorrencias($sql);
                $this->migrarHorasExtras($sql);
                $this->migrarPromocoes($sql);
                $this->migrarColaboradoresBonus($sql);
            });
        } finally {
            $this->fkChecks($driver, true);
        }

        $this->newLine();
        $this->info('✅ Migração concluída.');
        $this->warn('Os usuários logam com o mesmo e-mail e senha do sistema antigo (ex.: admin@privus.com.br).');

        return self::SUCCESS;
    }

    // ───────────────────────── Tabelas ─────────────────────────

    private function migrarEmpresas(string $sql): void
    {
        $rows = $this->extract($sql, 'empresas');
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'                    => (int) $r['id'],
                'nome'                  => $r['nome_fantasia'] ?: $r['razao_social'],
                'razao_social'          => $r['razao_social'],
                'cnpj'                  => $r['cnpj'],
                'telefone'              => $r['telefone'],
                'email'                 => $r['email'],
                'endereco'              => $r['endereco'],
                'bairro'                => $r['bairro'],
                'cep'                   => $r['cep'],
                'cidade'                => $r['cidade'],
                'estado'                => $r['estado'],
                'percentual_hora_extra' => $r['percentual_hora_extra'] ?? 50,
                'ativa'                 => $this->ativo($r['status']),
                'created_at'            => $r['created_at'],
                'updated_at'            => $r['updated_at'],
            ];
        }
        $this->inserir('empresas', $out);
        $this->defaultEmpresaId = $out ? min(array_column($out, 'id')) : 1;
    }

    private function migrarNiveis(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'niveis_hierarquicos') as $r) {
            $out[] = [
                'id'         => (int) $r['id'],
                'empresa_id' => $this->defaultEmpresaId, // legado é global
                'nome'       => $r['nome'],
                'nivel'      => (int) ($r['nivel'] ?? 1),
                'descricao'  => $r['descricao'],
                'created_at' => $r['created_at'],
                'updated_at' => $r['updated_at'],
            ];
        }
        $this->inserir('niveis_hierarquicos', $out);
    }

    private function migrarSetores(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'setores') as $r) {
            $out[] = [
                'id'         => (int) $r['id'],
                'empresa_id' => (int) $r['empresa_id'],
                'nome'       => $r['nome_setor'],
                'descricao'  => $r['descricao'] ?: null,
                'ativo'      => $this->ativo($r['status']),
                'created_at' => $r['created_at'],
                'updated_at' => $r['updated_at'],
            ];
        }
        $this->inserir('setores', $out);
    }

    private function migrarCargos(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'cargos') as $r) {
            $out[] = [
                'id'           => (int) $r['id'],
                'empresa_id'   => (int) $r['empresa_id'],
                'nome'         => $r['nome_cargo'],
                'descricao'    => $r['descricao'] ?: null,
                'salario_base' => $r['salario_base'] ?? 0,
                'ativo'        => $this->ativo($r['status']),
                'created_at'   => $r['created_at'],
                'updated_at'   => $r['updated_at'],
            ];
        }
        $this->inserir('cargos', $out);
    }

    private function migrarTiposBonus(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'tipos_bonus') as $r) {
            $out[] = [
                'id'           => (int) $r['id'],
                'empresa_id'   => $this->defaultEmpresaId, // legado é global
                'nome'         => $r['nome'],
                'descricao'    => $r['descricao'] ?: null,
                'tipo_calculo' => $r['tipo_valor'] ?? null,
                'fixo'         => $r['valor_fixo'] ?? null,
                'ativo'        => $this->ativo($r['status']),
                'created_at'   => $r['created_at'],
                'updated_at'   => $r['updated_at'],
            ];
        }
        $this->inserir('tipos_bonus', $out);
    }

    private function migrarTiposOcorrencias(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'tipos_ocorrencias') as $r) {
            $out[] = [
                'id'         => (int) $r['id'],
                'empresa_id' => $this->defaultEmpresaId, // legado é global
                'nome'       => $r['nome'],
                'descricao'  => null,
                'gravidade'  => $r['severidade'] ?? null,
                'ativo'      => $this->ativo($r['status']),
                'created_at' => $r['created_at'],
                'updated_at' => $r['updated_at'],
            ];
        }
        $this->inserir('tipos_ocorrencias', $out);
    }

    private function migrarColaboradores(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'colaboradores') as $r) {
            $id = (int) $r['id'];
            $empresaId = (int) $r['empresa_id'];
            $this->colabEmpresa[$id] = $empresaId;

            $out[] = [
                'id'                   => $id,
                'empresa_id'           => $empresaId,
                'setor_id'             => $this->intOrNull($r['setor_id']),
                'cargo_id'             => $this->intOrNull($r['cargo_id']),
                'nivel_hierarquico_id' => $this->intOrNull($r['nivel_hierarquico_id']),
                'lider_id'             => $this->intOrNull($r['lider_id']),
                'nome'                 => $r['nome_completo'],
                'cpf'                  => $r['cpf'],
                'rg'                   => $r['rg'],
                'data_nascimento'      => $r['data_nascimento'] ?: null,
                'estado_civil'         => $r['estado_civil'] ?: null,
                'telefone'             => $r['telefone'],
                'email'                => $r['email_pessoal'],
                'email_pessoal'        => $r['email_pessoal'],
                'tipo_contrato'        => $this->tipoContrato($r['tipo_contrato']),
                'carga_horaria'        => $r['jornada_diaria_horas'] ?? null,
                'data_admissao'        => $r['data_inicio'] ?: null,
                'salario'              => $r['salario'] ?? 0,
                'status'               => $this->statusColab($r['status']),
                'senha_hash'           => $r['senha_hash'],
                'cep'                  => $r['cep'],
                'logradouro'           => $r['logradouro'],
                'numero'               => $r['numero'],
                'complemento'          => $r['complemento'],
                'bairro'               => $r['bairro'],
                'cidade'               => $r['cidade_endereco'],
                'estado'               => $r['estado_endereco'],
                'observacoes'          => $r['observacoes'] ?: null,
                'foto'                 => $r['foto'],
                'created_at'           => $r['created_at'],
                'updated_at'           => $r['updated_at'],
            ];
        }
        $this->inserir('colaboradores', $out);
    }

    private function migrarUsuarios(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'usuarios') as $r) {
            $out[] = [
                'id'            => (int) $r['id'],
                'name'          => $r['nome'],
                'email'         => $r['email'],
                'password'      => $r['senha_hash'],
                'role'          => $r['role'] ?: 'COLABORADOR',
                'empresa_id'     => $this->intOrNull($r['empresa_id']),
                'setor_id'       => $this->intOrNull($r['setor_id']),
                'colaborador_id' => $this->intOrNull($r['colaborador_id'] ?? null),
                'foto'           => $r['foto'],
                'ativo'          => $this->ativo($r['status']),
                'last_login_at'  => $r['ultimo_login'] ?: null,
                'created_at'     => $r['created_at'],
                'updated_at'     => $r['updated_at'],
            ];
        }
        $this->inserir('users', $out);
    }

    private function migrarUsuariosEmpresas(string $sql): void
    {
        $out = [];
        $seen = [];
        foreach ($this->extract($sql, 'usuarios_empresas') as $r) {
            $userId = (int) $r['usuario_id'];
            $empId  = (int) $r['empresa_id'];
            $key = "$userId-$empId";
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = [
                'user_id'    => $userId,
                'empresa_id' => $empId,
                'created_at' => $r['created_at'] ?? null,
                'updated_at' => $r['created_at'] ?? null,
            ];
        }
        $this->inserir('usuarios_empresas', $out);
    }

    private function migrarOcorrencias(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'ocorrencias') as $r) {
            $colab = (int) $r['colaborador_id'];
            $out[] = [
                'id'                    => (int) $r['id'],
                'empresa_id'            => $this->empresaDoColab($colab),
                'colaborador_id'        => $colab,
                'tipo_ocorrencia_id'    => $this->intOrNull($r['tipo_ocorrencia_id']),
                'registrado_por'        => $this->intOrNull($r['usuario_id']),
                'titulo'                => $r['tipo'] ?: null,
                'gravidade'             => $r['severidade'] ?? null,
                'descricao'             => $r['descricao'] ?: null,
                'data_ocorrencia'       => $r['data_ocorrencia'],
                'notificar_colaborador' => 0,
                'created_at'            => $r['created_at'],
                'updated_at'            => $r['updated_at'],
            ];
        }
        $this->inserir('ocorrencias', $out);
    }

    private function migrarHorasExtras(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'horas_extras') as $r) {
            $colab = (int) $r['colaborador_id'];
            $out[] = [
                'id'             => (int) $r['id'],
                'empresa_id'     => $this->empresaDoColab($colab),
                'colaborador_id' => $colab,
                'registrado_por' => $this->intOrNull($r['usuario_id']),
                'data'           => $r['data_trabalho'],
                'horas'          => $r['quantidade_horas'] ?? 0,
                'percentual'     => $r['percentual_adicional'] ?? 0,
                'valor'          => $r['valor_total'] ?? 0,
                'observacao'     => $r['observacoes'] ?: null,
                'status'         => 'pendente',
                'aprovado'       => 0,
                'created_at'     => $r['created_at'],
                'updated_at'     => $r['updated_at'],
            ];
        }
        $this->inserir('horas_extras', $out);
    }

    private function migrarPromocoes(string $sql): void
    {
        $out = [];
        foreach ($this->extract($sql, 'promocoes') as $r) {
            $colab = (int) $r['colaborador_id'];
            $out[] = [
                'id'               => (int) $r['id'],
                'empresa_id'       => $this->empresaDoColab($colab),
                'colaborador_id'   => $colab,
                'registrado_por'   => $this->intOrNull($r['usuario_id']),
                'tipo'             => 'PROMOCAO',
                'salario_anterior' => $r['salario_anterior'] ?? 0,
                'salario_novo'     => $r['salario_novo'] ?? 0,
                'data_promocao'    => $r['data_promocao'],
                'motivo'           => $r['motivo'] ?: null,
                'created_at'       => $r['created_at'],
                'updated_at'       => $r['updated_at'],
            ];
        }
        $this->inserir('promocoes', $out);
    }

    private function migrarColaboradoresBonus(string $sql): void
    {
        $out = [];
        $seen = [];
        foreach ($this->extract($sql, 'colaboradores_bonus') as $r) {
            $colab = (int) $r['colaborador_id'];
            $tipo  = (int) $r['tipo_bonus_id'];
            $key = "$colab-$tipo";
            if (isset($seen[$key])) {
                continue; // unique(colaborador_id, tipo_bonus_id)
            }
            $seen[$key] = true;
            $out[] = [
                'id'             => (int) $r['id'],
                'colaborador_id' => $colab,
                'tipo_bonus_id'  => $tipo,
                'valor'          => $r['valor'] ?? 0,
                'ativo'          => 1,
                'created_at'     => $r['created_at'],
                'updated_at'     => $r['updated_at'],
            ];
        }
        $this->inserir('colaboradores_bonus', $out);
    }

    // ───────────────────────── Helpers ─────────────────────────

    /**
     * (Re)vincula users.colaborador_id a partir do dump, sem reimportar dados.
     * Só liga quando o colaborador correspondente existe.
     */
    private function vincularColaboradores(string $sql): int
    {
        $colabIds = DB::table('colaboradores')->pluck('id')->flip();
        $count = 0;
        foreach ($this->extract($sql, 'usuarios') as $r) {
            $userId  = (int) ($r['id'] ?? 0);
            $colabId = $this->intOrNull($r['colaborador_id'] ?? null);
            if (! $userId || ! $colabId || ! $colabIds->has($colabId)) {
                continue;
            }
            $count += DB::table('users')->where('id', $userId)->update(['colaborador_id' => $colabId]);
        }
        return $count;
    }

    private function limparDestino(): void
    {
        foreach ([
            'colaboradores_bonus', 'promocoes', 'horas_extras', 'ocorrencias',
            'usuarios_empresas', 'colaboradores', 'cargos', 'setores',
            'niveis_hierarquicos', 'tipos_ocorrencias', 'tipos_bonus',
            'users', 'empresas',
        ] as $t) {
            DB::table($t)->delete();
        }
    }

    private function inserir(string $tabela, array $linhas): void
    {
        if (! $linhas) {
            $this->line("  - $tabela: 0 registros");
            return;
        }
        foreach (array_chunk($linhas, 200) as $chunk) {
            DB::table($tabela)->insert($chunk);
        }
        $this->line("  ✓ $tabela: " . count($linhas) . ' registros');
    }

    private function empresaDoColab(int $colaboradorId): int
    {
        return $this->colabEmpresa[$colaboradorId] ?? $this->defaultEmpresaId;
    }

    private function ativo(?string $status): int
    {
        return strtolower((string) $status) === 'ativo' ? 1 : 0;
    }

    private function statusColab(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'ativo'     => 'ATIVO',
            'pausado'   => 'LICENCA',
            'desligado' => 'INATIVO',
            default     => 'ATIVO',
        };
    }

    private function tipoContrato(?string $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        return match (strtolower($v)) {
            'estágio', 'estagio' => 'ESTAGIO',
            'terceirizado'       => 'TERCEIRIZADO',
            default              => strtoupper($v),
        };
    }

    private function intOrNull($v): ?int
    {
        return ($v === null || $v === '') ? null : (int) $v;
    }

    private function fkChecks(string $driver, bool $on): void
    {
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=' . ($on ? '1' : '0'));
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ' . ($on ? 'ON' : 'OFF'));
        }
    }

    // ───────────────────── Parser do dump .sql ─────────────────────

    /**
     * Extrai todas as linhas de INSERT de uma tabela do dump como arrays
     * associativos [coluna => valor], lidando com strings com escapes e NULL.
     *
     * @return array<int,array<string,?string>>
     */
    private function extract(string $sql, string $table): array
    {
        $rows = [];
        $needle = "INSERT INTO `{$table}` (";
        $offset = 0;

        while (($pos = strpos($sql, $needle, $offset)) !== false) {
            $colStart = $pos + strlen($needle) - 1; // posição do '('
            // lista de colunas: entre '(' e ') VALUES'
            $colEnd = strpos($sql, ') VALUES', $colStart);
            if ($colEnd === false) {
                break;
            }
            $colList = substr($sql, $colStart + 1, $colEnd - $colStart - 1);
            $cols = array_map(
                fn ($c) => trim(trim($c), '`'),
                explode(',', $colList)
            );

            // valores: a partir de 'VALUES'
            [$tuples, $endPos] = $this->parseTuples($sql, $colEnd + strlen(') VALUES'));
            foreach ($tuples as $tuple) {
                if (count($tuple) === count($cols)) {
                    $rows[] = array_combine($cols, $tuple);
                }
            }
            $offset = $endPos;
        }

        return $rows;
    }

    /**
     * Faz o parse de "(v,v,...),(v,...);" a partir de $pos.
     * Retorna [array de tuplas, posição após o ';'].
     */
    private function parseTuples(string $s, int $pos): array
    {
        $len = strlen($s);
        $tuples = [];
        $i = $pos;

        while ($i < $len) {
            // pula espaços/virgulas entre tuplas
            while ($i < $len && ($s[$i] === ' ' || $s[$i] === "\n" || $s[$i] === "\r" || $s[$i] === "\t" || $s[$i] === ',')) {
                $i++;
            }
            if ($i >= $len || $s[$i] === ';') {
                $i++; // consome ';'
                break;
            }
            if ($s[$i] !== '(') {
                break; // formato inesperado
            }
            $i++; // entra na tupla
            $values = [];
            while ($i < $len) {
                // pula espaços
                while ($i < $len && ($s[$i] === ' ' || $s[$i] === "\n" || $s[$i] === "\r" || $s[$i] === "\t")) {
                    $i++;
                }
                $ch = $s[$i];
                if ($ch === "'") {
                    // string com escapes
                    $i++;
                    $buf = '';
                    while ($i < $len) {
                        $c = $s[$i];
                        if ($c === '\\' && $i + 1 < $len) {
                            $n = $s[$i + 1];
                            $buf .= match ($n) {
                                'n' => "\n", 'r' => "\r", 't' => "\t",
                                '0' => "\0", 'b' => "\x08", 'Z' => "\x1a",
                                default => $n,
                            };
                            $i += 2;
                            continue;
                        }
                        if ($c === "'") {
                            // '' = aspas escapada
                            if ($i + 1 < $len && $s[$i + 1] === "'") {
                                $buf .= "'";
                                $i += 2;
                                continue;
                            }
                            $i++; // fim da string
                            break;
                        }
                        $buf .= $c;
                        $i++;
                    }
                    $values[] = $buf;
                } else {
                    // número, NULL, etc., até ',' ou ')'
                    $buf = '';
                    while ($i < $len && $s[$i] !== ',' && $s[$i] !== ')') {
                        $buf .= $s[$i];
                        $i++;
                    }
                    $buf = trim($buf);
                    $values[] = (strtoupper($buf) === 'NULL') ? null : $buf;
                }

                // pula espaços
                while ($i < $len && ($s[$i] === ' ' || $s[$i] === "\n" || $s[$i] === "\r" || $s[$i] === "\t")) {
                    $i++;
                }
                if ($i < $len && $s[$i] === ',') {
                    $i++; // próximo valor
                    continue;
                }
                if ($i < $len && $s[$i] === ')') {
                    $i++; // fim da tupla
                    break;
                }
            }
            $tuples[] = $values;
        }

        return [$tuples, $i];
    }
}
