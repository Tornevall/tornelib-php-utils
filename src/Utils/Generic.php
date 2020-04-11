<?php

namespace TorneLIB\Utils;

/**
 * Class Generic Generic functions
 * @package TorneLIB\Utils
 * @version 6.1.0
 * @since 6.1.0
 */
class Generic
{

    /**
     * @return string
     */
    public function getVersionByClassDoc()
    {
        $return = '';

        $doc = @(new \ReflectionClass(__CLASS__))->getDocComment();
        if (!empty($doc)) {
            @preg_match_all('/@version\s(\w.+)\n/s', $doc, $version);
            if (isset($version[1]) && isset($version[1][0])) {
                $return = $version[1][0];

                // Strip stuff after line breaks
                if (preg_match('/\n|\r/', $return)) {
                    $multiRowData = preg_split('/\n|\r/', $return);
                    $return = $multiRowData[0];
                }
            }
        }
        
        return $return;
    }
}
