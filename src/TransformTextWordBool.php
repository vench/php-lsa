<?php

namespace PHPLsa;

/**
 * Class TransformTextWordBool
 * @package PHPLsa
 */
class TransformTextWordBool implements ITransformTextToMatrix
{

    /**
     * @var int
     */
    private $nMaxWords;

    /**
     * @var array
     */
    private $wordDict;

    /**
     * @var string
     */
    private $pattern = '/\b\w+\b/Ui';

    /**
     * TransformTextWordBool constructor.
     * @param int $nMaxWords
     * @param array $wordDict
     */
    function __construct($nMaxWords = 100, $wordDict = [])
    {
        $this->nMaxWords = $nMaxWords;
        $this->wordDict = $wordDict;
    }


    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments): array
    {

        $result = [];
        for($i = 0; $i < count($arDocuments); $i ++) {
            $maths = [];
            $result[$i] = array_fill(0, $this->nMaxWords, 0);
            preg_match_all($this->pattern, $arDocuments[$i], $maths);
            if(isset($maths[0])) {
                foreach ($maths[0] as $word) {
                    $word = $this->processedWord($word);
                    if(isset($this->wordDict[$word])) {
                        $this->setValueToResult($result[$this->wordDict[$word]][$i], 1);
                    } else if($this->nMaxWords > count($this->wordDict)) {
                        $this->wordDict[$word] = count($this->wordDict);
                        $this->setValueToResult($result[$this->wordDict[$word]][$i], 1);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getWordDictionary():array {
        return $this->wordDict;
    }

    /**
     * @param string $word
     * @return string
     */
    public function processedWord($word) {
        return mb_strtolower($word);
    }


    /**
     * @param int $address
     * @param $value
     */
    protected function setValueToResult(int &$address, $value) {
        $address = $value;
    }
}