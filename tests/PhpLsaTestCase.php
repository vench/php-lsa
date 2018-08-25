<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * Class PhpLsaTestCase
 */
class PhpLsaTestCase extends TestCase
{

    /**
     * @return array
     */
    public function testMult() {
        $U = [
            [0,0,1,0],
            [0,1,0,0],
            [0,0,0,-1],
            [1,0,0,0],
        ];
        $k = [
            [4,0,0,0,0],
            [0,3,0,0,0],
            [0,0,sqrt(5),0,0],
            [0,0,0,0,0],
        ];
        $V = [
            [0,1,0,0,0],
            [0,0,1,0,0],
            [sqrt(0.2),0,0,0,sqrt(0.8)],
            [0,0,0,1,0],
            [-sqrt(0.8),0,0,0,sqrt(0.2)],
        ];

        $M = \PHPLsa\mult(\PHPLsa\mult( $U,  $k), $V) ;

        $this->assertTrue(count($M) == count($U));
        $this->assertTrue(count($M[0]) == count($V));

        return $M;
    }


    /**
     * @depends testMult
     * @param $M0
     */
    public function testSVD(array $M0) {
        $M = [
            [1, 0, 0, 0, 2],
            [0, 0, 3, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 4, 0, 0, 0],
        ];

        for($i = 0; $i < count($M0); $i ++) {
            for($j = 0; $j < count($M0[0]); $j ++) {
                $this->assertTrue( abs( $M0[$i][$j] - $M[$i][$j]) < 1);
            }
        }

        list($U, $V, $S) = \PHPLsa\svd($M);

        $this->assertTrue(!empty($U));
        $this->assertTrue(!empty($V));
        $this->assertTrue(!empty($S));

        $this->assertTrue(true);
    }
}