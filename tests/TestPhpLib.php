<?php

namespace PHPLsa\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class PhpLsaTestCase
 */
class TestPhpLib extends TestCase
{

    /**
     * @return void
     */
    public function testTrunc() {
        $A = [
                [1,2,3,4],
                [5,6,7,8],
            ];
        \PHPLsa\trunc($A, 2, 2);

        $this->assertCount(2, $A);
        $this->assertCount(2, $A[0]);

        $this->assertSame(1, $A[0][0]);
        $this->assertSame(2, $A[0][1]);
        $this->assertSame(5, $A[1][0]);
        $this->assertSame(6, $A[1][1]);
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

        $this->assertSame(count($M), count($U));
        $this->assertSame(count($M[0]), count($V));

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
                $this->assertLessThan(1, abs( $M0[$i][$j] - $M[$i][$j]));
            }
        }

        list($U, $V, $S) = \PHPLsa\svd($M);

        $this->assertNotEmpty($U);
        $this->assertNotEmpty($V);
        $this->assertNotEmpty($S);
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
