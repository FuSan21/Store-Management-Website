<?php

namespace App\Support;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Csv
{
    /**
     * Stream a CSV download row by row so large exports stay within memory.
     *
     * @param  array<int, string>  $header
     * @param  Closure(): iterable<array<int, mixed>>  $rows
     */
    public static function download(string $filename, array $header, Closure $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');

            // A byte-order mark makes Excel read the file as UTF-8 (৳, Bangla names).
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header, escape: '');

            foreach ($rows() as $row) {
                fputcsv($out, array_map(self::neutralise(...), $row), escape: '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Stop spreadsheet formula injection from customer-entered text.
     */
    private static function neutralise(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
