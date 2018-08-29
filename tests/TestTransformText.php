<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPLsa\TransformTextByKeyWord;
use PHPLsa\TransformTextWordBool;
use PHPLsa\TransformTextWordCount;

/**
 * Class TestTransformTextByKeyWord
 */
class TestTransformText extends TestCase
{

    /**
     *
     */
    public function testTransformTextWordBool() {
        $t = new TransformTextWordBool(100);
        $trans = $t->transform(['What is it?', 'It is cat.']);

        $wordDictionary = $t->getWordDictionary();
        $this->assertTrue(count($wordDictionary) == 2);
        $this->assertTrue(isset($wordDictionary['what']));
        $this->assertTrue(isset($wordDictionary['cat']));

        $this->assertTrue($trans[0][0] == 1);
        $this->assertTrue($trans[0][1] == 0);

        $this->assertTrue($trans[1][0] == 0);
        $this->assertTrue($trans[1][1] == 1);
    }

    /**
     *
     */
    public function testTransformTextWordCount() {
        $t = new TransformTextWordCount(100);
        $trans = $t->transform(['test test eat', 'dog cat fish cat']);

        $wordDictionary = $t->getWordDictionary();
        $this->assertTrue(count($wordDictionary) == 5);

        $this->assertTrue($trans[0][0] == 2);
        $this->assertTrue($trans[0][1] == 0);

        $this->assertTrue($trans[1][0] == 1);
        $this->assertTrue($trans[1][1] == 0);

        $this->assertTrue($trans[2][0] == 0);
        $this->assertTrue($trans[2][1] == 1);

        $this->assertTrue($trans[3][0] == 0);
        $this->assertTrue($trans[3][1] == 2);

        $this->assertTrue($trans[4][0] == 0);
        $this->assertTrue($trans[4][1] == 1);
    }

    /**
     *
     */
    public function testTransformTextByKeyWord() {

        $t = new TransformTextByKeyWord(['and', 'class', 'PHP']);
        $documents = [
            'And whenever we instantiate a class it goes to ClassLoader::loadClass() to find the corresponding file and load it.',
            'All of them are different ways to map the namespaces with their corresponding paths, once you choose one of them, you have to follow their rules and at the end, composer knows how to find and load the file based on',
            'Welcome to PHPUnit! PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.',
            'We have a roadmap with details on what is planned for future releases of PHPUnit. Our release process is documented as well.',
            ];

        $trans = $t->transform($documents);

        $this->assertTrue(count($trans[0]) == count($documents));

        $tests = [
            [2,2,0,0],
            [3,0,0,0],
            [0,0,3,1],
        ];

        for ($i = 0; $i < count($tests); $i ++) {
            for ($j = 0; $j < count($tests[0]); $j ++) {
                $this->assertTrue($tests[$i][$j] ==$trans[$i][$j], "Not equals {$i}{$j}!={$i}{$j}");
            }
        }

    }

}