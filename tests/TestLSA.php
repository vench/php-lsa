<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use  PHPLsa\LSA;

/**
 * Class TestLSA
 */
class TestLSA extends TestCase
{
    /**
     * @return LSA
     */
    public function testInitLsa() {
        $keyWords = ['brown', 'cow', 'ran', 'jumped', 'fiddle', 'moon', 'fun', 'away',
                     'spoon', 'quick', 'diddle', 'dish', 'fox', 'lazy', 'dog', 'hey',
                     'cat', 'little', 'laughed'];
        $transformText = new \PHPLsa\TransformTextByKeyWord($keyWords);
        $trans = $transformText->transform(['ABC']);
        $this->assertTrue(count($keyWords) == count($trans[0]));

        $lsa = new LSA(4);
        $lsa->setTextTransformer($transformText);

        return $lsa;
    }

    /**
     * @param LSA $lsa
     * @depends testInitLsa
     */
    public function testFit(LSA $lsa) {
        $documents = [
            "The quick brown fox jumped over the lazy dog",
            "hey diddle diddle, the cat and the fiddle",
            "the cow jumped over the moon",
            "the little dog laughed to see such fun",
            "and the dish ran away with the spoon",
        ];

        $trans = $lsa->fitTransform($documents);
      //  print_r(count($trans));
        $this->assertTrue(count($trans) > 0);

        $query = "the brown fox ran around the dog";
        $qTrans = $lsa->transform([$query]);
        //$qTrans = \PHPLsa\trans($qTrans)[0];
        //$trans = \PHPLsa\trans($trans);
        //print_r($qTrans); exit();
        echo PHP_EOL;
        $wstr = ['w'=> -1, 'index'=> -1];


        for($n = 0; $n < count($trans[0]); $n++) {
            $sum = 0.0;
            $sum1 = 0.0;
            $sum2 = 0.0;

            for($i = 0; $i < count($trans); $i++) {
                $sum += $trans[$i][$n] * $qTrans[$i][0];
                $sum1 += $trans[$i][$n] * $trans[$i][$n];
                $sum2 += $qTrans[$i][0] * $qTrans[$i][0];
            }

            $w = $sum / (sqrt($sum1) * sqrt($sum2) +0.00001);
            echo $w, PHP_EOL;
            if($wstr['index'] == -1 || $w > $wstr['w']) {
                $wstr['index'] = $n;
                $wstr['w'] = $w;
            }
        }




        print_r($wstr);



    }
}