<?php

namespace Ekok\Validation;

class Helper
{
    public static function toBool($value): bool|null
    {
        return filter_var($value, FILTER_VALIDATE_BOOL|FILTER_NULL_ON_FAILURE);
    }

    public static function toDate($datetime): \DateTime|null
    {
        try {
            return is_string($datetime) ? new \DateTime($datetime) : null;
        } catch (\Throwable $error) {
            return null;
        }
    }

    public static function snakeCase(string $str): string
    {
        return preg_replace('/\p{Lu}/', '_$0', lcfirst($str));
    }

    public static function each(iterable $data, callable $fn, bool $key = true, bool $null = true): array
    {
        $result = array();
        $seed = new \stdClass();

        foreach ($data as $name => $value) {
            $update = $fn($value, $name, array(
                'seed' => $seed,
                'old' => $data,
                'new' => $result,
            ));

            if (null === $update && false === $null) {
                continue;
            }

            if ($key) {
                $result[$seed->key ?? $name] = $update;
            } else {
                $result[] = $update;
            }
        }

        return $result;
    }

    public static function cast(string $value)
    {
        $val = trim($value);

        if (preg_match('/^(?:0x[0-9a-f]+|0[0-7]+|0b[01]+)$/i', $val)) {
			return intval($val, 0);
        }

		if (is_numeric($val)) {
			return $val * 1;
        }

		if (preg_match('/^\w+$/i', $val) && defined($val)) {
			return constant($val);
        }

		return $val;
    }

    public static function isWild(string $key, int|bool &$pos = null): bool
    {
        return !!($pos = strpos($key, '*'));
    }

    public static function replaceWild(string $key, int $pos, string $replacement): string
    {
        return substr($key, 0, $pos) . $replacement . (substr($key, $pos + 1) ?: '');
    }

    public static function &ref(string $key, array &$ref, bool $add = true, bool &$exists = null)
    {
        if ($add) {
            $var = &$ref;
        } else {
            $var = $ref;
        }

        if (
            ($exists = isset($var[$key]) || array_key_exists($key, $var))
            || !is_string($key)
            || false === strpos($key, '.')
        ) {
            $parts = array($key);
            $var = &$var[$key];

            return $var;
        }

        $parts = explode('.', $key);
        $nulls = null;

        foreach ($parts as $part) {
            if (null === $var || is_scalar($var)) {
                $var = array();
            }

            if (($arr = is_array($var)) || $var instanceof \ArrayAccess) {
                $exists = isset($var[$part]) || ($arr && array_key_exists($part, $var));
                $var = &$var[$part];
            } elseif (is_object($var) && is_callable($get = array($var, 'get' . $part))) {
                $exists = true;
                $var = $get();
            } elseif (is_object($var)) {
                $exists = isset($var->$part);
                $var = &$var->$part;
            } else {
                $exists = false;
                $var = $nulls;

                break;
            }
        }

        return $var;
    }
}
