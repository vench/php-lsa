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
    public function savePartData(array $data);

    public function loadPartData():array;
}