<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Str;

final class Caseify
{
    public static function handle(string $text): array
    {
        $singular = Str::singular($text);
        $plural = Str::plural($singular);

        return [
            'classCase' => Str::studly($singular),
            'camelCase' => Str::camel($singular),
            'underscoreCase' => Str::snake($singular),
            'titleCase' => Str::title(Str::snake($singular, ' ')),
            'kebabCase' => Str::kebab($singular),
            'dotCase' => self::toDotCase($singular),
            'camelCasePlural' => Str::camel($plural),
            'underscoreCasePlural' => Str::snake($plural),
            'classCasePlural' => Str::studly($plural),
        ];
    }

    private static function toDotCase(string $text): string
    {
        return mb_trim(mb_strtolower((string) preg_replace('/([A-Z])/', '.$1', $text)), '.');
    }
}
