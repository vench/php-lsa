<?php

namespace PHPLsa;

/**
 * Class TransformTextWordCount
 * @package PHPLsa
 */
class TransformTextWordCount extends TransformTextWordBool
{

    /**
     * @param int $address
     * @param $value
     */
    protected function setValueToResult(int &$address, $value)
    {
        $address += $value;
    }
}
