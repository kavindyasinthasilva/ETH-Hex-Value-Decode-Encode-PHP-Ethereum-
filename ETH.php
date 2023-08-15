<?php

class ETH
{

    public static  function hexValue($value) {
        $trimmed = self::hexStripZeros(self::hexlify($value, array("hexPad" => "left")));
        if ($trimmed === "0x") {
            return "0x0";
        }
        return $trimmed;
    }

    public static  function hexStripZeros($value) {
        if (!is_string($value)) {
            $value = self::hexlify($value);
        }

        if (!preg_match('/^0x[0-9A-Fa-f]*$/', $value)) {
            throw new InvalidArgumentException("invalid hex string");
        }

        $value = substr($value, 2);
        $offset = 0;
        while ($offset < strlen($value) && $value[$offset] === "0") {
            $offset++;
        }
        return "0x" . substr($value, $offset);
    }

    public static  function isHexString($value, $length = null) {
        if (!is_string($value) || !preg_match('/^0x[0-9A-Fa-f]*$/', $value)) {
            return false;
        }
        if ($length && strlen($value) !== 2 + 2 * $length) {
            return false;
        }
        return true;
    }

    public static  function hexlify($value, $options = null) {
        if (!$options) {
            $options = array();
        }

        if (is_numeric($value)) {
            $value = (string)$value;
            $hex = "";
            while ($value > 0) {
                $hex = dechex($value & 0xf) . $hex;
                $value = intdiv($value, 16);
            }

            if (!empty($hex)) {
                if (strlen($hex) % 2 !== 0) {
                    $hex = "0" . $hex;
                }
                return "0x" . $hex;
            }

            return "0x00";
        }

        if (is_string($value)) {
            if ($options["allowMissingPrefix"] && substr($value, 0, 2) !== "0x") {
                $value = "0x" . $value;
            }

            if (self::isHexString($value)) {
                if (strlen($value) % 2 !== 0) {
                    if ($options["hexPad"] === "left") {
                        $value = "0x0" . substr($value, 2);
                    } elseif ($options["hexPad"] === "right") {
                        $value .= "0";
                    } else {
                        throw new InvalidArgumentException("hex data is odd-length");
                    }
                }
                return strtolower($value);
            }
        }

        throw new InvalidArgumentException("invalid hexlify value");
    }

}