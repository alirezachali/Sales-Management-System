<?php

use Hekmatinasser\Verta\Verta;

if (! function_exists('jalaliDate')) {

    function jalaliDate($date, ?string $format = null): ?string
    {
        if (empty($date)) {
            return null;
        }

        $format ??= setting('date_format', 'Y/m/d');

        return Verta::instance($date)->format($format);
    }

}

if (! function_exists('jalaliDateTime')) {

    function jalaliDateTime($date, ?string $format = null): ?string
    {
        if (empty($date)) {
            return null;
        }

        $format ??= setting('date_format', 'Y/m/d') . ' H:i';

        return Verta::instance($date)->format($format);
    }

}

if (! function_exists('jalaliTime')) {

    function jalaliTime($date, string $format = 'H:i'): ?string
    {
        if (empty($date)) {
            return null;
        }

        return Verta::instance($date)->format($format);
    }

}