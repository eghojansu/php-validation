<?php

namespace Ekok\Validation;

class Helper
{
    public static function toBool($value): bool|null
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }

    public static function toDate($datetime): \DateTime|null
    {
        try {
            return is_string($datetime) ? new \DateTime($datetime) : null;
        } catch (\Throwable $error) {
            return null;
        }
    }

    public static function toDateFromFormat(string $format, $datetime): \DateTime|null
    {
        return is_string($datetime) ? (\DateTime::createFromFormat($format, $datetime) ?: null) : null;
    }

    public static function toSize($value, string $type = null): int|float
    {
        return match($type ?? gettype($value)) {
            'int', 'integer', 'float', 'double' => $value,
            'string' => strlen($value),
            'array' => count($value),
            'file' => ($value['size'] ?? 0) / 1024,
            default => $value instanceof \Countable ? count($value) : 0,
        };
    }

    public static function isWild(string $key, int|bool &$pos = null): bool
    {
        return !!($pos = strpos($key, '*'));
    }

    public static function replaceWild(string $key, int $pos, string $replacement): string
    {
        return substr($key, 0, $pos) . $replacement . (substr($key, $pos + 1) ?: '');
    }
}
