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
    private $pattern = '/\b[^0-9\s-\.\,]{3,}\b/ui';

    /**
     * TransformTextWordBool constructor.
     * @param int $nMaxWords
     * @param array $wordDict
     */
    public function __construct($nMaxWords = 100, $wordDict = [])
    {
        $this->nMaxWords = $nMaxWords;
        $this->wordDict = $wordDict;
    }

    /**
     * @param array $M
     */
    public function fit(array $M)
    {
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments): array
    {

        $M = array_fill(
            0,
            count($this->wordDict),
            array_fill(0, count($arDocuments), 0)
        );

        for ($i = 0; $i < count($arDocuments); $i ++) {
            $maths = [];
            preg_match_all($this->pattern, $arDocuments[$i], $maths);
            if (isset($maths[0])) {
                foreach ($maths[0] as $word) {
                    $word = $this->processedWord($word);
                    if (isStopWords($word)) {
                        continue;
                    }

                    if (isset($this->wordDict[$word])) {
                        if (!isset($M[$this->wordDict[$word]][$i])) {
                            $M[$this->wordDict[$word]] = array_fill(0, count($arDocuments), 0);
                        }
                        $this->setValueToResult($M[$this->wordDict[$word]][$i], 1);
                    } elseif ($this->nMaxWords > count($this->wordDict)) {
                        $this->wordDict[$word] = count($this->wordDict);
                        $M[$this->wordDict[$word]] = array_fill(0, count($arDocuments), 0);
                        $this->setValueToResult($M[$this->wordDict[$word]][$i], 1);
                    }
                }
            }
        }

        return $M;
    }

    /**
     * @return array
     */
    public function getWordDictionary():array
    {
        return $this->wordDict;
    }

    /**
     * @param string $word
     * @return string
     */
    public function processedWord($word)
    {
        return mb_strtolower($word);
    }


    /**
     * @param int $address
     * @param $value
     */
    protected function setValueToResult(int &$address, $value)
    {
        $address = $value;
    }

    /**
     * @param IPersistent $persistent
     * @return mixed
     */
    public function save(IPersistent $persistent)
    {
        $persistent->save('worddict', $this->wordDict);
    }

    /**
     * @param IPersistent $persistent
     * @return mixed
     */
    public function load(IPersistent $persistent)
    {
        $this->wordDict = $persistent->load('worddict');
    }
}
