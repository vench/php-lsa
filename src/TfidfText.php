<?php

namespace PHPLsa;

/**
 * Class TfidfText
 * @package PHPLsa
 */
class TfidfText
{

    /**
     * @var array
     */
    private $transform;

    /**
     * @param array $M
     * @return array
     */
    public function fitTransform(array $M) {
        $this->fit($M);
        return $this->transform($M);
    }

    /**
     * @param array $M
     */
    public function fit(array $M) {
        $rows = count($M);
        $cols = count($M[0]);
        $this->transform = [];

        for ($i = 0; $i < $rows; $i ++) {
            $this->transform[$i] = array_fill(0, $rows, DF_ZERO);
            $df = array_sum($M[$i]);
            $this->transform[$i][$i] = log((1+$cols) / (1 + $df));
        }
    }

    /**
     * @param array $M
     * @return array
     */
    public function transform(array $M): array {
        return mult($this->transform, $M);
    }
}

