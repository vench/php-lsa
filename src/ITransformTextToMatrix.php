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

    /**
     * @param IPersistent $persistent
     * @return mixed
     */
    public function save(IPersistent $persistent);

    /**
     * @param IPersistent $persistent
     * @return mixed
     */
    public function load(IPersistent $persistent);
}
