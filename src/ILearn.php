<?php

namespace PHPLsa;

/**
 * Interface ILearn
 * @package PHPLsa
 */
interface ILearn
{
    /**
     * @param array $A
     * @return mixed
     */
    public function fit(array $A);

    /**
     * @param array $A
     * @return array
     */
    public function transform(array $A):array;

    /**
     * @param array $A
     * @return array
     */
    public function fitTransform(array $A):array;

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
