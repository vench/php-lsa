<?php

namespace PHPLsa;

/**
 * Class LSA
 * @package PHPLsa
 */
class LSA
{



    /**
     * @var int
     */
    private $nFeatures;

    /**
     * @var int
     */
    private $nMaxDocuments;

    /**
     * @var int
     */
    private $nMaxWords;


    /**
     * @var ITransformTextToMatrix
     */
    private $textTransformer= null;

    /**
     * @var array
     */
    private $components = [];

    /**
     * LSA constructor.
     * @param int $nFeatures
     * @param int $nMaxDocuments
     * @param int $nMaxWords
     * @param int $typeCount
     */
    function __construct($nFeatures = 5, $nMaxDocuments = 1000, $nMaxWords = 100)
    {
        $this->nFeatures = $nFeatures;
        $this->nMaxDocuments = $nMaxDocuments;
        $this->nMaxWords = $nMaxWords;
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function fitTransform(array $arDocuments):array {
        $M = $this->textTransform($arDocuments);
        $M = trans($M);
        list($U, $V, $S) = svd($M);
        $min = min($this->nFeatures, count($M), count($M[0]));
        trunc($U, count($M), $min);
        trunc($V, count($M[0]), $min);

        $this->components = $U;
        $VT = trans($V);

        $result = [];
        for ($i = 0; $i < count($VT); $i ++) {
            for ($j = 0; $j < count($VT[0]); $j ++) {
                $result[$i][$j] = $VT[$i][$j] * $S[$i][$i];
            }
        }

        return $result;
    }

    /**
     * @param array $arDocuments
     */
    public function fit(array $arDocuments) {
        $this->fitTransform($arDocuments);
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments):array {
        $M = $this->textTransform($arDocuments);
        $ct = trans($this->components);
        return mult($ct, trans($M));
    }

    /**
     *
     */
    public function save() {

    }

    /**
     *
     */
    public function load() {

    }

    /**
     * @return ITransformTextToMatrix
     */
    public function getTextTransformer() {
        if(is_null($this->textTransformer)) {
            $this->setTextTransformer(
                new TransformTextWordBool($this->nMaxWords) );
        }
        return $this->textTransformer;
    }

    /**
     * @param ITransformTextToMatrix $textTransformer
     */
    public function setTextTransformer(ITransformTextToMatrix $textTransformer) {
        $this->textTransformer = $textTransformer;

    }

    /**
     * @param array $arDocuments
     * @return array
     */
    private function textTransform(array $arDocuments):array {
        return $this->getTextTransformer()
            ->transform(array_slice($arDocuments, 0, $this->nMaxDocuments));
    }


}