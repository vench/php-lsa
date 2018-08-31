<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * Class PhpLsaTestCase
 */
class TestPhpLib extends TestCase
{


    public function testTrunc() {
        $A = [
                [1,2,3,4],
                [5,6,7,8],
            ];
        PHPLsa\trunc($A, 2, 2);

        $this->assertTrue(count($A) == 2);
        $this->assertTrue(count($A[0]) == 2);

        $this->assertTrue($A[0][0] == 1);
        $this->assertTrue($A[0][1] == 2);
        $this->assertTrue($A[1][0] == 5);
        $this->assertTrue($A[1][1] == 6);
    }

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

    /*
    public function testTime() {
        $start = microtime(true);
        $M = [
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
            [0,1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1,0],
        ];
        for($i = 0; $i < 1000; $i ++) {
            \PHPLsa\svd($M);
        }

        $diff = (microtime(true) - $start);

        $start1 = microtime(true);
        for($i = 0; $i < 1000; $i ++) {
            \PHPLsa\_svd($M);
        }

        $diff1 = (microtime(true) - $start1);
        //echo 't1: ', $diff, PHP_EOL;
        //echo 't2: ', $diff1, PHP_EOL;

        $this->assertTrue($diff1 > $diff, 'svd slowly _svd');
    }*/
}