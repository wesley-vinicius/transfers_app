<?php

namespace App\Core\DataTransfers;

abstract class DataTransfer
{
    public function __get($key)
    {
        if (method_exists($this, 'get' . $this->camelCase($key))) {
            return $this->{'get' . $this->camelCase($key)}();
        }
        return $this->{$key};
    }

    public function camelCase($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
}
