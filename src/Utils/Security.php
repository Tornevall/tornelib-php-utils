<?php

namespace TorneLIB\Utils;

use TorneLIB\Exception\Constants;
use TorneLIB\Exception\ExceptionHandler;

/**
 * Class Security
 * @package TorneLIB\Utils
 * @version 6.1.0
 */
class Security
{

    /**
     * Security constructor.
     * @since 6.1.0
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * Get trimmed data from ini file
     * @param $key
     * @return string
     * @since 6.1.0
     */
    public function getIni($key)
    {
        return trim(ini_get($key));
    }

    /**
     * @param $key
     * @return bool
     * @since 6.1.0
     */
    public function getDisabledFunction($key)
    {
        $return = false;

        if (is_array($key)) {
            foreach ($key as $functionName) {
                $functionList = array_map("strtolower", $this->getIniArray('disable_functions'));
                if (in_array($functionName, $functionList)) {
                    $return = true;
                    break;
                }
            }
        } else {
            $functionList = array_map("strtolower", $this->getIniArray('disable_functions'));
            if (in_array($key, $functionList)) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * @param $key
     * @return bool
     * @since 6.1.0
     */
    public function getDisabledClass($key)
    {
        $return = false;

        if (is_array($key)) {
            foreach ($key as $className) {
                $classList = array_map("strtolower", $this->getIniArray('disable_classes'));
                if (in_array(strtolower($className), $classList)) {
                    $return = true;
                    break;
                }
            }
        } else {
            $classList = array_map("strtolower", $this->getIniArray('disable_classes'));
            if (in_array(strtolower($key), $classList)) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * @param $className
     * @return string
     */
    public function getClassState($className, $throw = true)
    {
        $code = Constants::LIB_NO_ERROR;

        if (!class_exists($className)) {
            $code = Constants::LIB_CLASS_UNAVAILABLE;
        }
        if ($this->getDisabledClass($className)) {
            $code = Constants::LIB_CLASS_DISABLED;
        }

        $errorMessage = "";
        switch ($code) {
            case Constants::LIB_CLASS_UNAVAILABLE:
                $errorMessage = sprintf(
                    'Class %s is %s: Not available on this platform.',
                    $className,
                    'missing'
                );
                break;
            case Constants::LIB_CLASS_DISABLED:
                $errorMessage = sprintf(
                    'Class %s is %s: It has been disabled on this platform.',
                    $className,
                    'disabled'
                );
            default:
                // Proceed.
                break;
        }

        if (!empty($errorMessage) && $throw) {
            throw new ExceptionHandler(
                sprintf(
                    'ClassTestException: %s',
                    $errorMessage
                ),
                $code
            );
        }

        return $code;
    }

    /**
     * @param $className
     * @return string
     */
    public static function getCurrentClassState($className, $throwable = true)
    {
        return (new Security())->getClassState($className, $throwable);
    }

    /**
     * @param $className
     * @return bool
     */
    public static function getIsDisabledClass($className)
    {
        return (new Security())->getDisabledClass($className);
    }

    /**
     * @param $functionName
     * @return bool
     */
    public static function getIsDisabledFunction($functionName)
    {
        return (new Security())->getDisabledFunction($functionName);
    }

    /**
     * Get proper boolean value from php.ini.
     *
     * @param $key
     * @return bool
     * @since 6.1.0
     */
    public function getIniBoolean($key)
    {
        return (bool)(filter_var($this->getIni($key), FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * @param $key
     * @param array $delimiter
     * @return array
     * @since 6.1.0
     */
    public function getIniArray($key, $delimiter = [','])
    {
        if (is_string($delimiter)) {
            $delimiter = (array)$delimiter;
        }

        return array_map(
            'trim', preg_split(
                sprintf(
                    '/[%s]/',
                    implode('', $delimiter)
                ),
                $this->getIni($key))
        );
    }

    /**
     * Determine if PHP is in safe mode.
     *
     * @return bool
     * @since 6.1.0
     */
    public function getSafeMode()
    {
        // There is no safe mode in PHP 5.4.0 and above
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return false;
        }

        return (bool)(filter_var($this->getIniBoolean('safe_mode'), FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * @return bool
     * @since 6.1.0
     */
    public function getSecureMode()
    {
        // In netcurl 6.0 we also checked safe mode in this method. But since safe mode was removed from PHP 5.4.0
        // this check is also removed from this module.
        $currentBaseDir = $this->getIni('open_basedir');
        if ($currentBaseDir == '') {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     * @since 6.1.0
     */
    public static function getIsSafe()
    {
        return self::getIsSafe();
    }
}
