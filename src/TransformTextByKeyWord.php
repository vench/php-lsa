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
        for ($i = 0; $i < count($arDocuments); $i ++) {
            $M[$i] = [];
            for($j = 0; $j < count($this->keyWords); $j ++) {
                $maths = [];
                if(preg_match_all("/{$this->keyWords[$j]}/Ui", $arDocuments[$i], $maths)) {
                    $M[$i][$j] = count($maths[0]);
                } else {
                    $M[$i][$j] = 0;
                }
            }
        }
        return $M;
    }
}