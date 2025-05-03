<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Str;

final class Caseify
{
    public static function handel(string $text): array
    {
        return [
            'classCase' => Str::studly($text),
            'camelCase' => Str::camel($text),
            'underscoreCase' => Str::snake($text),
            'camelCasePlural' => Str::camel(Str::plural($text)),
            'underscoreCasePlural' => Str::snake(Str::plural($text)),
            'classCasePlural' => Str::studly(Str::plural($text)),
        ];
    }
}
