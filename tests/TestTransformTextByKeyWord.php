<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPLsa\TransformTextByKeyWord;

/**
 * Class TestTransformTextByKeyWord
 */
class TestTransformTextByKeyWord extends TestCase
{
    /**
     *
     */
    public function testTransform() {

        $t = new TransformTextByKeyWord(['and', 'class', 'PHP']);
        $documents = [
            'And whenever we instantiate a class it goes to ClassLoader::loadClass() to find the corresponding file and load it.',
            'All of them are different ways to map the namespaces with their corresponding paths, once you choose one of them, you have to follow their rules and at the end, composer knows how to find and load the file based on',
            'Welcome to PHPUnit! PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.',
            'We have a roadmap with details on what is planned for future releases of PHPUnit. Our release process is documented as well.',
            ];

        $trans = $t->transform($documents);

        $this->assertTrue(count($trans) == count($documents));

        $tests = [
            [2,3,0],
            [2,0,0],
            [0,0,3],
            [0,0,1],
        ];


        for ($i = 0; $i < count($tests); $i ++) {
            for ($j = 0; $j < count($tests[0]); $j ++) {
                $this->assertTrue($tests[$i][$j] ==$trans[$i][$j], "Not equals {$i}{$j}!={$i}{$j}");
            }
        }

    }

}