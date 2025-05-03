<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Str;

final class Caseify
{
    public static function handel(string $text): array
    {
        $singular = Str::singular($text);
        $plural = Str::plural($singular);

        return [
            'classCase' => Str::studly($singular),
            'camelCase' => Str::camel($singular),
            'underscoreCase' => Str::snake($singular),
            'titleCase' => Str::title(Str::snake($singular, ' ')),
            'camelCasePlural' => Str::camel($plural),
            'underscoreCasePlural' => Str::snake($plural),
            'classCasePlural' => Str::studly($plural),
        ];
    }
}
