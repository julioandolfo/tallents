<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport
{
    /**
     * Gera um download CSV (UTF-8 com BOM, separador ;) a partir dos cabeçalhos
     * e de um iterable de linhas (cada linha = array de valores).
     */
    public static function download(string $arquivo, array $cabecalhos, iterable $linhas): StreamedResponse
    {
        return response()->streamDownload(function () use ($cabecalhos, $linhas) {
            $out = fopen('php://output', 'w');
            fprintf($out, "\xEF\xBB\xBF"); // BOM para Excel reconhecer UTF-8
            fputcsv($out, $cabecalhos, ';');
            foreach ($linhas as $linha) {
                fputcsv($out, $linha, ';');
            }
            fclose($out);
        }, $arquivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
