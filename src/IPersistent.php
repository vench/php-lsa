<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 30.08.18
 * Time: 16:01
 */

namespace PHPLsa;


interface IPersistent
{
    /**
     * @param $key
     * @param array $data
     * @return mixed
     */
    public function save($key, array $data);

    /**
     * @param $key
     * @return array
     */
    public function load($key):array;
}