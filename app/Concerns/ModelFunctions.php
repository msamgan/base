<?php

declare(strict_types=1);

namespace App\Concerns;

trait ModelFunctions
{
    public function saveKey(string $key, $value): void
    {
        $this->$key = $value;
        $this->save();
    }

    public function key(string $key): mixed
    {
        return $this->$key;
    }
}
