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
        $keyWords = ['dog', 'lazy', 'brown', 'quick', 'cow', 'jumped', 'moon', 'little', 'see', 'fun', 'dish', 'spoon', 'fox', 'around'];
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
            //'the brown fox over around the cat',
            "and the dish ran away with the spoon brown",
        ];

        $trans = $lsa->fitTransform($documents);
        $this->assertTrue(count($trans) == count($documents));

        $query = "the brown fox ran around the dog";
        $qTrans = $lsa->transform([$query]);
        $qTrans = $qTrans[0];
       // print_r($qTrans);

        $wstr = ['w'=> -1, 'index'=> -1];
        foreach ($trans as $n => $vect) {
            $sum = 0.0;
	        $sum1 = 0.0;
	        $sum2 = 0.0;
            for($i = 0; $i < count($vect); $i++) {
                //print_r($vect[$i]); exit();
                $sum += $vect[$i] * $qTrans[$i];
                $sum1 += $vect[$i] * $vect[$i];
                $sum2 += $qTrans[$i] * $qTrans[$i];
            }
            $w = $sum / (sqrt($sum1) * sqrt($sum2));
            echo $w, PHP_EOL;
            if($wstr['index'] == -1 || $w > $wstr['w']) {
                $wstr['index'] = $n;
                $wstr['w'] = $w;
            }


        }


        print_r($wstr);



    }
}