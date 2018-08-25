<?php

namespace PHPLsa;

/**
 * Interface ITransformTextToMatrix
 * @package PHPLsa
 */
interface ITransformTextToMatrix
{
    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments):array;

}