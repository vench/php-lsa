<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 25.08.18
 * Time: 15:30
 */

namespace PHPLsa;

/**
 * Class TransformTextByKeyWord
 * @package PHPLsa
 */
class TransformTextByKeyWord implements ITransformTextToMatrix
{

    /**
     * @var array
     */
    private $keyWords;

    function __construct(array $keyWords)
    {
        $this->keyWords = $keyWords;
    }

    /**
     * @param array $arDocuments
     * @return array
     */
    public function transform(array $arDocuments): array
    {

        $M = [];
        $maths = [];
        for ($i = 0; $i < count($arDocuments); $i ++) {
            for($j = 0; $j < count($this->keyWords); $j ++) {

                if(!isset($M[$j])) {
                    $M[$j] = [];
                }

                if(preg_match_all("/{$this->keyWords[$j]}/Ui", $arDocuments[$i], $maths)) {
                    $M[$j][$i] = count($maths[0]);
                } else {
                    $M[$j][$i] = 0;
                }
            }
        }
        return $M;
    }
}