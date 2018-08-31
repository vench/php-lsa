<?php

namespace PHPLsa;

/**
 * Class LSA
 * @package PHPLsa
 */
class LSA implements ILearn
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
    private $textTransformer = null;

    /**
     * @var ILearn[]
     */
    private $textMatrixTransformers = [];

    /**
     * @var array
     */
    private $components = [];

    /**
     * LSA constructor.
     * @param int $nFeatures
     * @param int $nMaxDocuments
     * @param int $nMaxWords
     */
    function __construct($nFeatures = 5, $nMaxDocuments = 1000, $nMaxWords = 100)
    {
        $this->nFeatures = $nFeatures;
        $this->nMaxDocuments = $nMaxDocuments;
        $this->nMaxWords = $nMaxWords;
    }

    /**
     * @param ILearn $matrixTransformer
     */
    public function addTextMatrixTransformer(ILearn $matrixTransformer) {
        $this->textMatrixTransformers[] = $matrixTransformer;
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function fitTransform(array $arDocuments):array {
        $M = $this->textTransform($arDocuments);

        foreach ($this->textMatrixTransformers as $textMatrixTransformer) {
            $M = $textMatrixTransformer->fitTransform($M);
        }

        list($U, $V, $S) = svd($M);
        $min = min($this->nFeatures, count($M), count($M[0]));
        trunc($U, count($M), $min);

        $V = trans($V);
        trunc($V, count($M[0]), $min);
        $V = trans($V);

        $this->components = trans($U);
        $VT = $V;

        $result = [];
        for ($i = 0; $i < count($VT); $i ++) {
            for ($j = 0; $j < count($VT[0]); $j ++) {
                $result[$i][$j] = $VT[$i][$j] * $S[$i];
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
        foreach ($this->textMatrixTransformers as $textMatrixTransformer) {
            $M = $textMatrixTransformer->transform($M);
        }
        return mult($this->components,  $M);
    }

    /**
     * @param IPersistent $persistent
     */
    public function save(IPersistent $persistent) {
        $persistent->save('components', $this->components);

        $this->getTextTransformer()->save($persistent);

        foreach ($this->textMatrixTransformers as $textMatrixTransformer) {
            $textMatrixTransformer->save($persistent);
        }
    }

    /**
     * @param IPersistent $persistent
     */
    public function load(IPersistent $persistent) {
        $this->components = $persistent->load('components', $this->components);

        $this->getTextTransformer()->load($persistent);

        foreach ($this->textMatrixTransformers as $textMatrixTransformer) {
            $textMatrixTransformer->load($persistent);
        }
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
     * @param $query
     * @param array $trans
     * @return int
     */
    public function query($query, array $trans) {
        $qTrans = $this->transform([$query]);
        $index = -1;
        $weight = 0;
        $alpha = 0.0001;
        for($n = 0; $n < count($trans[0]); $n++) {
            $sum = 0.0;
            $sum1 = 0.0;
            $sum2 = 0.0;

            for($i = 0; $i < count($trans); $i++) {
                $sum += $trans[$i][$n] * $qTrans[$i][0];
                $sum1 += $trans[$i][$n] * $trans[$i][$n];
                $sum2 += $qTrans[$i][0] * $qTrans[$i][0];
            }

            $w = abs(  $sum / (sqrt($sum1  + $alpha) * sqrt($sum2  + $alpha) ));
            if($index == -1 || $w > $weight) {
                $index = $n;
                $weight = $w;
            }
        }

        return $index;
    }


    /**
     * @param ITransformTextToMatrix $textTransformer
     */
    public function setTextTransformer(ITransformTextToMatrix $textTransformer) {
        $this->textTransformer = $textTransformer;
    }


    /**
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
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