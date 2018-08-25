<?php

namespace PHPLsa;


/**
 *
 */
define('DF_ZERO', 0.0);

/**
 * @param array $A
 * @param array $B
 * @return array
 * @throws \Exception
 */
function mult(array $A, array $B):array {
    if(count($A[0]) != count($B)) {
        throw new \Exception("Error matrix dimension");
    }
    $C = [];
    for($i = 0; $i < count($A); $i ++) {
        for($j = 0; $j < count($B[0]); $j ++) {
            if(!isset($C[$i][$j])) {
                $C[$i][$j] = DF_ZERO;
            }

            for($n = 0; $n < count($A[0]); $n ++) {
                $C[$i][$j] += $A[$i][$n] * $B[$n][$j];
            }
        }
    }
    return $C;
}

/**
 * @param array $A
 * @return array
 */
function trans(array $A):array {
    $C = [];
    for($i = 0; $i < count($A); $i ++) {
        for($j = 0; $j < count($A[0]); $j ++) {
            $C[$j][$i] = $A[$i][$j];
        }
    }
    return $C;
}

/**
 * @param array $A
 * @param int $rows
 * @param int $cols
 * @return array
 */
function constr(array $A, int $rows, int $cols):array{
    $C = [];
    for($i = 0; $i < $rows; $i ++) {
        for($j = 0; $j < $cols; $j ++) {
            $C[$i][$j] = isset($A[$i][$j]) ? $A[$i][$j] : DF_ZERO;
        }
    }
    return $C;
}

/**
 * @param float $a
 * @param float $b
 * @return float
 */
function sameSign(float $a, float $b):float {
    if($b >= 0){
        return abs($a);
    }
    return - abs($a);
}

/**
 * @param float $a
 * @param float $b
 * @return float
 */
function pythag(float $a, float $b):float{

    $absa = abs($a);
    $absb = abs($b);

    if( $absa > $absb ){
        return $absa * sqrt( 1.0 + pow( $absb / $absa , 2) );
    }

    if( $absb > 0.0 ){
        return $absb * sqrt( 1.0 + pow( $absa / $absb, 2 ) );
    }
    return 0.0;

}

/**
 * @param array $A
 * @return array [U, V^T, S]
 */
function svd(array $A) {
    $rows = count($A); // $m
    $cols = count($A[0]); // $n

    $k = 3; //TODO ????
    $U  = constr($A, $rows, $cols);
    $V  = constr($A, $cols, $cols);

    $eps = 2.22045e-016;

    //
    $g = $scale = $anorm = 0.0;
    for($i = 0; $i < $cols; $i++){
        $l = $i + 2;
        $rv1[$i] = $scale * $g;
        $g = $s = $scale = 0.0;
        if($i < $rows){
            for($k = $i; $k < $rows; $k++) $scale += abs($U[$k][$i]);
            if($scale != 0.0) {
                for($k = $i; $k < $rows; $k++) {
                    $U[$k][$i] /= $scale;
                    $s += $U[$k][$i] * $U[$k][$i];
                }
                $f = $U[$i][$i];
                $g = - sameSign(sqrt($s), $f);
                $h = $f * $g - $s;
                $U[$i][$i] = $f - $g;
                for($j = $l - 1; $j < $cols; $j++){
                    for($s = 0.0, $k = $i; $k < $rows; $k++) $s += $U[$k][$i] * $U[$k][$j];
                    $f = $s / $h;
                    for($k = $i; $k < $rows; $k++) $U[$k][$j] += $f * $U[$k][$i];
                }
                for($k = $i; $k < $rows; $k++) $U[$k][$i] *= $scale;
            }
        }
        $W[$i] = $scale * $g;
        $g = $s = $scale = 0.0;
        if($i + 1 <= $rows && $i + 1 != $cols){
            for ($k= $l - 1; $k < $cols; $k++) $scale += abs($U[$i][$k]);
            if($scale != 0.0){
                for ($k= $l - 1; $k < $cols; $k++){
                    $U[$i][$k] /= $scale;
                    $s += $U[$i][$k] * $U[$i][$k];
                }
                $f = $U[$i][$l - 1];
                $g = - sameSign(sqrt($s), $f);
                $h = $f * $g - $s;
                $U[$i][$l - 1] = $f - $g;
                for($k = $l - 1; $k < $cols; $k++) $rv1[$k] = $U[$i][$k] / $h;
                for($j = $l - 1; $j < $rows; $j++){
                    for($s = 0.0, $k = $l - 1; $k < $cols; $k++) $s += $U[$j][$k] * $U[$i][$k];
                    for($k = $l - 1; $k < $cols; $k++) $U[$j][$k] += $s * $rv1[$k];
                }
                for($k= $l - 1; $k < $cols; $k++) $U[$i][$k] *= $scale;
            }
        }
        $anorm = max($anorm, (abs($W[$i]) + abs($rv1[$i])));
    }
    //

    //
    for($i = $cols - 1; $i >= 0; $i--){
        if($i < $cols - 1){
            if($g != 0.0){
                for($j = $l; $j < $cols; $j++) // Double division to avoid possible underflow.
                    $V[$j][$i] = ($U[$i][$j] / $U[$i][$l]) / $g;
                for($j = $l; $j < $cols; $j++){
                    for($s = 0.0, $k = $l; $k < $cols; $k++) $s += ($U[$i][$k] * $V[$k][$j]);
                    for($k = $l; $k < $cols; $k++) $V[$k][$j] += $s * $V[$k][$i];
                }
            }
            for($j = $l; $j < $cols; $j++) $V[$i][$j] = $V[$j][$i] = DF_ZERO;
        }
        $V[$i][$i] = 1.0;
        $g = $rv1[$i];
        $l = $i;
    }
    //

    //
    for($i = min($rows, $cols) - 1; $i >= 0; $i--){
        $l = $i + 1;
        $g = $W[$i];
        for($j = $l; $j < $cols; $j++) $U[$i][$j] = DF_ZERO;
        if($g != 0.0){
            $g = 1.0 / $g;
            for($j = $l; $j < $cols; $j++){
                for($s = 0.0, $k = $l; $k < $rows; $k++) $s += $U[$k][$i] * $U[$k][$j];
                $f = ($s / $U[$i][$i]) * $g;
                for($k = $i; $k < $rows; $k++) $U[$k][$j] += $f * $U[$k][$i];
            }
            for($j = $i; $j < $rows; $j++) $U[$j][$i] *= $g;
        }else {
            for($j = $i; $j < $rows; $j++) $U[$j][$i] = DF_ZERO;
        }
        ++$U[$i][$i];
    }
    //

    //
    for($k = $cols - 1; $k >= 0; $k--){
        for($its = 0; $its < 30; $its++){
            $flag = true;
            for($l = $k; $l >= 0; $l--){
                $nm = $l - 1;
                if( $l == 0 || abs($rv1[$l]) <= $eps*$anorm){
                    $flag = false;
                    break;
                }
                if(abs($W[$nm]) <= $eps*$anorm) break;
            }
            if($flag){
                $c = 0.0;  // Cancellation of rv1[l], if l > 0.
                $s = 1.0;
                for($i = $l; $i < $k + 1; $i++){
                    $f = $s * $rv1[$i];
                    $rv1[$i] = $c * $rv1[$i];
                    if(abs($f) <= $eps*$anorm) break;
                    $g = $W[$i];
                    $h = pythag($f,$g);
                    $W[$i] = $h;
                    $h = 1.0 / $h;
                    $c = $g * $h;
                    $s = -$f * $h;
                    for($j = 0; $j < $rows; $j++){
                        $y = $U[$j][$nm];
                        $z = $U[$j][$i];
                        $U[$j][$nm] = $y * $c + $z * $s;
                        $U[$j][$i] = $z * $c - $y * $s;
                    }
                }
            }
            $z = $W[$k];
            if($l == $k){
                if($z < 0.0){
                    $W[$k] = -$z; // Singular value is made nonnegative.
                    for($j = 0; $j < $cols; $j++) $V[$j][$k] = -$V[$j][$k];
                }
                break;
            }
            if($its == 29) print("no convergence in 30 svd iterations");
            $x = $W[$l]; // Shift from bottom 2-by-2 minor.
            $nm = $k - 1;
            $y = $W[$nm];
            $g = $rv1[$nm];
            $h = $rv1[$k];
            $f = (($y - $z) * ($y + $z) + ($g - $h) * ($g + $h)) / (2.0 * $h * $y);
            $g = pythag($f,1.0);
            $f = (($x - $z) * ($x + $z) + $h * (($y / ($f + sameSign($g,$f))) - $h)) / $x;
            $c = $s = 1.0;
            for($j = $l; $j <= $nm; $j++){
                $i = $j + 1;
                $g = $rv1[$i];
                $y = $W[$i];
                $h = $s * $g;
                $g = $c * $g;
                $z = pythag($f,$h);
                $rv1[$j] = $z;
                $c = $f / $z;
                $s = $h / $z;
                $f = $x * $c + $g * $s;
                $g = $g * $c - $x * $s;
                $h = $y * $s;
                $y *= $c;
                for($jj = 0; $jj < $cols; $jj++){
                    $x = $V[$jj][$j];
                    $z = $V[$jj][$i];
                    $V[$jj][$j] = $x * $c + $z * $s;
                    $V[$jj][$i] = $z * $c - $x * $s;
                }
                $z = pythag($f,$h);
                $W[$j] = $z;  // Rotation can be arbitrary if z = 0.
                if($z){
                    $z = 1.0 / $z;
                    $c = $f * $z;
                    $s = $h * $z;
                }
                $f = $c * $g + $s * $y;
                $x = $c * $y - $s * $g;
                for($jj = 0; $jj < $rows; $jj++){
                    $y = $U[$jj][$j];
                    $z = $U[$jj][$i];
                    $U[$jj][$j] = $y * $c + $z * $s;
                    $U[$jj][$i] = $z * $c - $y * $s;
                }
            }
            $rv1[$l] = 0.0;
            $rv1[$k] = $f;
            $W[$k] = $x;
        }
    }
    //

    //
    $inc = 1;
    do {
        $inc *= 3;
        $inc++;
    }   while($inc <= $cols);

    do {
        $inc /= 3;
        for($i = $inc; $i < $cols; $i++){
            $sw = $W[$i];
            for($k = 0; $k < $rows; $k++) $su[$k] = $U[$k][$i];
            for($k = 0; $k < $cols; $k++) $sv[$k] = $V[$k][$i];
            $j = $i;
            while($W[$j - $inc] < $sw){
                $W[$j] = $W[$j - $inc];
                for($k = 0; $k < $rows; $k++) $U[$k][$j] = $U[$k][$j - $inc];
                for($k = 0; $k < $cols; $k++) $V[$k][$j] = $V[$k][$j - $inc];
                $j -= $inc;
                if($j < $inc) break;
            }
            $W[$j] = $sw;
            for($k = 0; $k < $rows; $k++) $U[$k][$j] = $su[$k];
            for($k = 0; $k < $cols; $k++) $V[$k][$j] = $sv[$k];
        }
    }  while($inc > 1);

    //
    for($k = 0; $k < $cols; $k++){
        $s = 0;
        for($i = 0; $i < $rows; $i++) if ($U[$i][$k] < DF_ZERO) $s++;
        for($j = 0; $j < $cols; $j++) if ($V[$j][$k] < DF_ZERO) $s++;
        if($s > ($cols + $rows)/2) {
            for($i = 0; $i < $rows; $i++) $U[$i][$k] = - $U[$i][$k];
            for($j = 0; $j < $cols; $j++) $V[$j][$k] = - $V[$j][$k];
        }
    }
    //
    //


    $S = [];
    // prepare S matrix as n*n daigonal matrix of singular values
    for($i = 0; $i < $cols; $i++){
        $S[$i] = array_fill(0, $cols, DF_ZERO);
        $S[$i][$i] = $W[$i];
    }
    //

    return [
        $U,
        trans($V),
        $S,
    ];
}