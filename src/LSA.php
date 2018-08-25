<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 25.08.18
 * Time: 15:16
 */

namespace PHPLsa;


/**
 * Class LSA
 * @package PHPLsa
 */
class LSA
{

    const TYPE_COUNT_BOOL = 1;

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
     * @var int
     */
    private $typeCount;

    /**
     * @var ITransformTextToMatrix
     */
    private $textTransformer= null;


    /**
     * LSA constructor.
     * @param int $nFeatures
     * @param int $nMaxDocuments
     * @param int $nMaxWords
     * @param int $typeCount
     */
    function __construct($nFeatures = 5, $nMaxDocuments = 1000, $nMaxWords = 100, $typeCount = self::TYPE_COUNT_BOOL)
    {
        $this->nFeatures = $nFeatures;
        $this->nMaxDocuments = $nMaxDocuments;
        $this->nMaxWords = $nMaxWords;
        $this->typeCount = $typeCount;
    }

    /**
     * @param array $arDocuments
     */
    public function fit(array $arDocuments) {

        $M = $this->getTextTransformer()
            ->transform(array_slice($arDocuments, 0, $this->nMaxDocuments));
        list($U, $V, $S) = svd($M);
        //TODO save matrix
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments):array {
        return [];
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
            $this->textTransformer = new TransformTextByKeyWord();
        }
        return $this->textTransformer;
    }


}