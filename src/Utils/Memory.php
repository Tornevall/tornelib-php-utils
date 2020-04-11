<?php

namespace TorneLIB\Utils;

use Exception;
use TorneLIB\Exception\Constants;
use TorneLIB\Exception\ExceptionHandler;
use TorneLIB\IO\Data\Strings;

/**
 * Class Memory
 * @package TorneLIB\Utils
 * @version 6.1.0
 * @since 6.1.0
 */
class Memory
{
    private $IO;
    private $INI;

    /**
     * Memory constructor.
     * @since 6.1.0
     */
    public function __construct()
    {
        $this->IO = new Strings();
        $this->INI = new Ini();
    }

    private $haltOnLowMemory = false;

    /**
     * Set new memory limit for PHP.
     *
     * @param string $newLimitValue
     * @return bool
     * @since 6.1.0
     */
    public function setMemoryLimit($newLimitValue = '512M')
    {
        $return = false;

        $oldMemoryValue = $this->IO->getBytes(ini_get('memory_limit'));
        if ($this->INI->getIniSettable('memory_limit')) {
            $blindIniSet = ini_set('memory_limit', $newLimitValue) !== false ? true : false;
            $newMemoryValue = $this->IO->getBytes(ini_get('memory_limit'));
            $return = $blindIniSet && $oldMemoryValue !== $newMemoryValue ? true : false;
        }

        return $return;
    }

    /**
     * Enforce automatic adjustment if memory limit is set too low (or your defined value).
     *
     * @param string $minLimit
     * @param string $maxLimit
     * @return bool
     * @throws Exception
     * @since 6.1.0
     */
    public function getMemoryLimitAdjusted($minLimit = '256M', $maxLimit = '-1')
    {
        $return = false;
        $currentLimit = $this->IO->getBytes(ini_get('memory_limit'));
        $myLimit = $this->IO->getBytes($minLimit);
        if ($currentLimit <= $myLimit) {
            $return = $this->setMemoryLimit($maxLimit);

            if (!$return && $this->getHaltOnLowMemory()) {
                throw new ExceptionHandler(
                    'Your server is running on too low memory, and I am not allowed to adjust this.',
                    Constants::LIB_UTILS_MEMORY_FAILSET
                );
            }
        }

        return $return;
    }

    /**
     * @return bool
     * @since 6.1.0
     */
    public function getHaltOnLowMemory()
    {
        return $this->haltOnLowMemory;
    }

    /**
     * @param bool $haltOnLowMemory
     * @since 6.1.0
     */
    public function setHaltOnLowMemory($haltOnLowMemory)
    {
        $this->haltOnLowMemory = $haltOnLowMemory;
    }
}
