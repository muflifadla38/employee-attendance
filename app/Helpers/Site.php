<?php

if (! function_exists('initialLetter')) {
    /**
     * Return initial letter form $name
     *
     * @return string $initialLetter
     */
    function initialLetter($name)
    {
        return substr($name, 0, 1);
    }
}

if (! function_exists('roleColor')) {
    function roleColor($role)
    {
        switch ($role) {
            case 'admin':
                $color = 'danger';
                break;
            case 'employee':
                $color = 'info';
                break;
            default:
                $color = 'secondary';
                break;
        }

        return $color;
    }
}

if (! function_exists('encryptCheck')) {
    function encryptCheck($string)
    {
        return preg_match('#^eyJpd(.*)$#i', $string);
    }
}
