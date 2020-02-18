<?php

namespace TorneLIB\Utils;

/**
 * Class Security
 * @package TorneLIB\Utils
 * @version 6.1.0
 */
class Security
{

    /**
     * Security constructor.
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * Determine if PHP is in safe mode.
     *
     * @param bool $mockedSafeMode
     * @return bool
     * @since 6.1.0
     */
    public function getSafeMode()
    {
        // There is no safe mode in PHP 5.4.0 and above
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return false;
        }

        return (bool)(filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * @return bool
     * @since 6.1.0
     */
    public function getSecureMode()
    {
        // In netcurl 6.0 we also checked safe mode in this method. But since safe mode was removed from PHP 5.4.0
        // this check is also removed from this module.
        $currentBaseDir = trim(ini_get('open_basedir'));
        if ($currentBaseDir == '') {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     * @since 6.1
     */
    public static function getIsSafe()
    {
        return self::getIsSafe();
    }
}