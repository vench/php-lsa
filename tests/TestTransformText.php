<?php

namespace PHPLsa\Tests;

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
     * @return void
     */
    public function testTransformTextWordBool()
    {
        $t = new TransformTextWordBool(100);
        $trans = $t->transform(['What is it?', 'It is cat.']);

        $wordDictionary = $t->getWordDictionary();
        $this->assertCount(2, $wordDictionary);
        $this->assertSame(0, $wordDictionary['what']);
        $this->assertSame(1, $wordDictionary['cat']);

        $this->assertSame(1, $trans[0][0]);
        $this->assertSame(0, $trans[0][1]);

        $this->assertSame(0, $trans[1][0]);
        $this->assertSame(1, $trans[1][1]);
    }

    /**
     * @return void
     */
    public function testTransformTextWordCount()
    {
        $t = new TransformTextWordCount(100);
        $trans = $t->transform(['test test eat', 'dog cat fish cat']);

        $wordDictionary = $t->getWordDictionary();
        $this->assertCount(5, $wordDictionary);

        $this->assertSame(2, $trans[0][0]);
        $this->assertSame(0, $trans[0][1]);

        $this->assertSame(1, $trans[1][0]);
        $this->assertSame(0, $trans[1][1]);

        $this->assertSame(0, $trans[2][0]);
        $this->assertSame(1, $trans[2][1]);

        $this->assertSame(0, $trans[3][0]);
        $this->assertSame(2, $trans[3][1]);

        $this->assertSame(0, $trans[4][0]);
        $this->assertSame(1, $trans[4][1]);
    }

    /**
     * @return void
     */
    public function testTransformTextByKeyWord()
    {

        $t = new TransformTextByKeyWord(['and', 'class', 'PHP']);
        $documents = [
            'And whenever we instantiate a class it goes to ClassLoader::loadClass() to find the corresponding file and load it.',
            'All of them are different ways to map the namespaces with their corresponding paths, once you choose one of them, you have to follow their rules and at the end, composer knows how to find and load the file based on',
            'Welcome to PHPUnit! PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.',
            'We have a roadmap with details on what is planned for future releases of PHPUnit. Our release process is documented as well.',
            ];

        $trans = $t->transform($documents);

        $this->assertSame(count($trans[0]), count($documents));

        $tests = [
            [2,2,0,0],
            [3,0,0,0],
            [0,0,3,1],
        ];

        for ($i = 0; $i < count($tests); $i ++) {
            for ($j = 0; $j < count($tests[0]); $j ++) {
                $this->assertSame($tests[$i][$j], $trans[$i][$j], "Not equals {$i}{$j}!={$i}{$j}");
            }
        }
    }
}
