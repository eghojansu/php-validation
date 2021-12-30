<?php

namespace Ekok\Validation;

class Helper
{
    public function cast(string $value)
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

            if ('*' === $part) {
                $exists = true;

                if (is_array($var)) {
                    $last = $add ? count($var) : count($var) - 1;
                    $exists = $last > -1 && isset($var[$last]);
                    $var = &$var[max(0, $last)];
                }

                continue;
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
