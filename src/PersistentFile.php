<?php

namespace PHPLsa;

/**
 * Class PersistentFile
 * @package PHPLsa
 */
class PersistentFile implements IPersistent
{
    /**
     * @var string
     */
    private $baseDirectory;

    /**
     * PersistentFile constructor.
     * @param $baseDirectory
     */
    function __construct($baseDirectory = './')
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * @param $key
     * @param array $data
     * @return mixed
     */
    public function save($key, array $data)
    {
        $path = rtrim($this->baseDirectory , '/\/'). DIRECTORY_SEPARATOR . $key;
        file_put_contents($path, serialize($data));
    }

    /**
     * @param $key
     * @return array
     */
    public function load($key): array
    {
        $path = rtrim($this->baseDirectory , '/\/'). DIRECTORY_SEPARATOR . $key;
        $data = file_get_contents($path);
        return $data ? unserialize($data) : [];
    }

    /**
     * @return string
     */
    public function getBaseDirectory() {
        return $this->baseDirectory;
    }
}