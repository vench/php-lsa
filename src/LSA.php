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
        list($U, $V, $S) = svd($M);

        $min = min($this->nFeatures, count($M), count($M[0]));

        //M 5x14
        //U 5x14 ~ 5x5
        //V 14x14
        //S 5x5


        trunc($U, count($M), $min);
        trunc($V, count($M[0]), $min);

       // print_r($V);
       // exit();

        $this->components = $V;
        //$VT = trans($V);
      //  print_r()

        $result = [];
        for ($i = 0; $i < count($U); $i ++) {
            for ($j = 0; $j < count($U[0]); $j ++) {
                $result[$i][$j] = $U[$i][$j] * $S[$i][$i];
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
        //$ct = $this->components;
       // print_r(count($this->components)); //4x14 | 1x14
        //exit();
        //return [];
        return mult($M, $this->components);
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