<?php

namespace TorneLIB\Utils;

use ReflectionClass;
use ReflectionException;

/**
 * Class Generic Generic functions
 * @package TorneLIB\Utils
 * @version 6.1.0
 * @since 6.1.0
 */
class Generic
{

    /**
     * Generic constructor.
     * @since 6.1.0
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * @param $item
     * @param $functionName
     * @return false|string
     * @throws ReflectionException
     * @since 6.1.0
     */
    private function getExtractedDocBlock(
        $item,
        $functionName
    ) {
        $doc = new ReflectionClass(__CLASS__);

        if (empty($functionName)) {
            $return = $doc->getDocComment();
        } else {
            $return = $doc->getMethod($functionName)->getDocComment();
        }

        return $return;
    }

    /**
     * @param $item
     * @param $doc
     * @return mixed|string
     * @since 6.1.0
     */
    private function getExtractedDocBlockItem($item, $doc)
    {
        $return = '';

        if (!empty($doc)) {
            preg_match_all(sprintf('/%s\s(\w.+)\n/s', $item), $doc, $docBlock);

            if (isset($docBlock[1]) && isset($docBlock[1][0])) {
                $return = $docBlock[1][0];

                // Strip stuff after line breaks
                if (preg_match('/[\n\r]/', $return)) {
                    $multiRowData = preg_split('/[\n\r]/', $return);
                    $return = isset($multiRowData[0]) ? $multiRowData[0] : '';
                }
            }
        }

        return $return;
    }

    /**
     * @param $item
     * @param string $functionName
     * @return mixed|string
     * @throws ReflectionException
     * @since 6.1.0
     */
    public function getDocBlockItem($item, $functionName = '')
    {
        return $this->getExtractedDocBlockItem(
            $item,
            $this->getExtractedDocBlock(
                $item,
                $functionName
            )
        );
    }

    /**
     * @return string
     * @throws ReflectionException
     * @since 6.1.0
     */
    public function getVersionByClassDoc()
    {
        return $this->getDocBlockItem('@version');
    }
}
