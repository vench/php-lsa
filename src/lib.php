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
function mult(array $A, array $B):array
{
    if (count($A[0]) != count($B)) {
        throw new \Exception("Error matrix dimension");
    }
    $C = [];
    for ($i = 0; $i < count($A); $i ++) {
        for ($j = 0; $j < count($B[0]); $j ++) {
            if (!isset($C[$i][$j])) {
                $C[$i][$j] = DF_ZERO;
            }

            for ($n = 0; $n < count($A[0]); $n ++) {
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
function trans(array $A):array
{
    $C = [];
    for ($i = 0; $i < count($A); $i ++) {
        for ($j = 0; $j < count($A[0]); $j ++) {
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
function constr(array $A, int $rows, int $cols):array
{
    $C = [];
    for ($i = 0; $i < $rows; $i ++) {
        for ($j = 0; $j < $cols; $j ++) {
            $C[$i][$j] = isset($A[$i][$j]) ? $A[$i][$j] : DF_ZERO;
        }
    }
    return $C;
}

/**
 * @param array $A
 * @param int $rows
 * @param int $cols
 */
function trunc(array &$A, int $rows, int $cols)
{
    for ($i = 0; $i < count($A); $i ++) {
        if ($i > $rows) {
            array_splice($A, $rows);
            break;
        } else {
            array_splice($A[$i], $cols);
        }
    }
}

/**
 * @param float $a
 * @param float $b
 * @return float
 */
function sameSign(float $a, float $b):float
{
    if ($b >= 0) {
        return abs($a);
    }
    return - abs($a);
}

/**
 * @param array $A
 */
function show(array $A)
{
    print "\n";
    for ($i = 0; $i < count($A); $i ++) {
        print join(", ", array_map(function ($x) {
            return round($x, 6);
        }, $A[$i]));
        print "\n";
    }
}

/**
 * @param float $a
 * @param float $b
 * @return float
 */
function _pythag(float $a, float $b):float
{

    $absa = abs($a);
    $absb = abs($b);

    if ($absa > $absb) {
        return $absa * sqrt(1.0 + pow($absb / $absa, 2));
    }

    if ($absb > 0.0) {
        return $absb * sqrt(1.0 + pow($absa / $absb, 2));
    }
    return 0.0;
}

/**
 * @param float $a
 * @param float $b
 * @return float
 */
function PYTHAG(float $a, float $b): float
{
    $at = abs($a);
    $bt = abs($b);

    if ($at > $bt) {
        $ct = $bt / $at;
        return $at * sqrt(1.0 + $ct * $ct);
    } elseif ($bt > 0.0) {
        $ct = $at / $bt;
        return $bt * sqrt(1.0 + $ct * $ct);
    }

    return 0.0;
}

/**
 * @param $a
 * @param $b
 * @return number
 */
function SIGN($a, $b)
{
    return (($b) >= 0.0 ? abs($a) : -abs($a));
}

/**
 * @param array $a
 * @param int $m
 * @param int $n
 * @param array $w
 * @param array $v
 * @return int
 */
function dsvd(array &$a, int $m, int $n, array &$w, array &$v)
{
    $anorm = 0.0;
    $g = 0.0;
    $scale = 0.0;
    $rv1 = [];

    if ($m < $n) {
        for ($i = 0; $i < $n - $m; $i ++) {
            $a[] = array_fill(0, $n, 0.0);
        }
    }

    /* Householder reduction to bidiagonal form */
    for ($i = 0; $i < $n; $i++) {
        /* left-hand reduction */
        $l = $i + 1;
        $rv1[$i] = $scale * $g;
        $g = $s = $scale = 0.0;
        if ($i < $m) {
            for ($k = $i; $k < $m; $k++) {
                $scale += abs((double)$a[$k][$i]);
            }
            if ($scale) {
                for ($k = $i; $k < $m; $k++) {
                    $a[$k][$i] = (double)((double)$a[$k][$i] / $scale);
                    $s += ((double)$a[$k][$i] * (double)$a[$k][$i]);
                }
                $f = (double)$a[$i][$i];
                $g = -SIGN(sqrt($s), $f);
                $h = $f * $g - $s;
                $a[$i][$i] = (double)($f - $g);
                if ($i != $n - 1) {
                    for ($j = $l; $j < $n; $j++) {
                        for ($s = 0.0, $k = $i; $k < $m; $k++) {
                            $s += ((double)$a[$k][$i] * (double)$a[$k][$j]);
                        }
                        $f = $s / $h;
                        for ($k = $i; $k < $m; $k++) {
                            $a[$k][$j] += (double)($f * (double)$a[$k][$i]);
                        }
                    }
                }
                for ($k = $i; $k < $m; $k++) {
                    $a[$k][$i] = (double)((double)$a[$k][$i] * $scale);
                }
            }
        }
        $w[$i] = (double)($scale * $g);

        /* right-hand reduction */
        $g = $s = $scale = 0.0;
        if ($i < $m && $i != $n - 1) {
            for ($k = $l; $k < $n; $k++) {
                $scale += abs((double)$a[$i][$k]);
            }
            if ($scale) {
                for ($k = $l; $k < $n; $k++) {
                    $a[$i][$k] = (double)((double)$a[$i][$k] / $scale);
                    $s += ((double)$a[$i][$k] * (double)$a[$i][$k]);
                }
                $f = (double)$a[$i][$l];
                $g = -SIGN(sqrt($s), $f);
                $h = $f * $g - $s;
                $a[$i][$l] = (double)($f - $g);
                for ($k = $l; $k < $n; $k++) {
                    $rv1[$k] = (double)$a[$i][$k] / $h;
                }
                if ($i != $m - 1) {
                    for ($j = $l; $j < $m; $j++) {
                        for ($s = 0.0, $k = $l; $k < $n; $k++) {
                            $s += ((double)$a[$j][$k] * (double)$a[$i][$k]);
                        }
                        for ($k = $l; $k < $n; $k++) {
                            $a[$j][$k] += (double)($s * $rv1[$k]);
                        }
                    }
                }
                for ($k = $l; $k < $n; $k++) {
                    $a[$i][$k] = (double)((double)$a[$i][$k] * $scale);
                }
            }
        }
        $anorm = max($anorm, (abs((double)$w[$i]) + abs($rv1[$i])));
    }

    /* accumulate the right-hand transformation */
    for ($i = $n - 1; $i >= 0; $i--) {
        if ($i < $n - 1) {
            if ($g) {
                for ($j = $l; $j < $n; $j++) {
                    $v[$j][$i] = (double)(((double)$a[$i][$j] / (double)$a[$i][$l]) / $g);
                }
                /* double division to avoid underflow */
                for ($j = $l; $j < $n; $j++) {
                    for ($s = 0.0, $k = $l; $k < $n; $k++) {
                        $s += ((double)$a[$i][$k] * (double)$v[$k][$j]);
                    }
                    for ($k = $l; $k < $n; $k++) {
                        $v[$k][$j] += (double)($s * (double)$v[$k][$i]);
                    }
                }
            }
            for ($j = $l; $j < $n; $j++) {
                $v[$i][$j] = $v[$j][$i] = 0.0;
            }
        }
        $v[$i][$i] = 1.0;
        $g = $rv1[$i];
        $l = $i;
    }

    /* accumulate the left-hand transformation */
    for ($i = $n - 1; $i >= 0; $i--) {
        $l = $i + 1;
        $g = (double)$w[$i];
        if ($i < $n - 1) {
            for ($j = $l; $j < $n; $j++) {
                $a[$i][$j] = 0.0;
            }
        }
        if ($g) {
            $g = 1.0 / $g;
            if ($i != $n - 1) {
                for ($j = $l; $j < $n; $j++) {
                    for ($s = 0.0, $k = $l; $k < $m; $k++) {
                        $s += ((double)$a[$k][$i] * (double)$a[$k][$j]);
                    }
                    $f = ($s / (double)$a[$i][$i]) * $g;
                    for ($k = $i; $k < $m; $k++) {
                        $a[$k][$j] += (double)($f * (double)$a[$k][$i]);
                    }
                }
            }
            for ($j = $i; $j < $m; $j++) {
                $a[$j][$i] = (double)((double)$a[$j][$i] * $g);
            }
        } else {
            for ($j = $i; $j < $m; $j++) {
                $a[$j][$i] = 0.0;
            }
        }
        ++$a[$i][$i];
    }

    /* diagonalize the bidiagonal form */
    for ($k = $n - 1; $k >= 0; $k--) {                             /* loop over singular values */
        for ($its = 0; $its < 30; $its++) {                         /* loop over allowed iterations */
            $flag = 1;
            for ($l = $k; $l >= 0; $l--) {                     /* test for splitting */
                $nm = $l - 1;
                if (abs($rv1[$l]) + $anorm == $anorm) {
                    $flag = 0;
                    break;
                }
                if (abs((double)$w[$nm]) + $anorm == $anorm) {
                    break;
                }
            }
            if ($flag) {
                $c = 0.0;
                $s = 1.0;
                for ($i = $l; $i <= $k; $i++) {
                    $f = $s * $rv1[$i];
                    if (abs($f) + $anorm != $anorm) {
                        $g = (double)$w[$i];
                        $h = PYTHAG($f, $g);
                        $w[$i] = (double)$h;
                        $h = 1.0 / $h;
                        $c = $g * $h;
                        $s = (-$f * $h);
                        for ($j = 0; $j < $m; $j++) {
                            $y = (double)$a[$j][$nm];
                            $z = (double)$a[$j][$i];
                            $a[$j][$nm] = (double)($y * $c + $z * $s);
                            $a[$j][$i] = (double)($z * $c - $y * $s);
                        }
                    }
                }
            }
            $z = (double)$w[$k];
            if ($l == $k) {                  /* convergence */
                if ($z < 0.0) {              /* make singular value nonnegative */
                    $w[$k] = (double)(-$z);
                    for ($j = 0; $j < $n; $j++) {
                        $v[$j][$k] = (-$v[$j][$k]);
                    }
                }
                break;
            }
            if ($its >= 30) {
                print("No convergence after 30,000! iterations \n");
                return (0);
            }

            /* shift from bottom 2 x 2 minor */
            $x = (double)$w[$l];
            $nm = $k - 1;
            $y = (double)$w[$nm];
            $g = $rv1[$nm];
            $h = $rv1[$k];
            $f = (($y - $z) * ($y + $z) + ($g - $h) * ($g + $h)) / (2.0 * $h * $y);
            $g = PYTHAG($f, 1.0);
            $f = (($x - $z) * ($x + $z) + $h * (($y / ($f + SIGN($g, $f))) - $h)) / $x;

            /* next QR transformation */
            $c = $s = 1.0;
            for ($j = $l; $j <= $nm; $j++) {
                $i = $j + 1;
                $g = $rv1[$i];
                $y = (double)$w[$i];
                $h = $s * $g;
                $g = $c * $g;
                $z = PYTHAG($f, $h);
                $rv1[$j] = $z;
                $c = $f / $z;
                $s = $h / $z;
                $f = $x * $c + $g * $s;
                $g = $g * $c - $x * $s;
                $h = $y * $s;
                $y = $y * $c;
                for ($jj = 0; $jj < $n; $jj++) {
                    $x = (double)$v[$jj][$j];
                    $z = (double)$v[$jj][$i];
                    $v[$jj][$j] = (double)($x * $c + $z * $s);
                    $v[$jj][$i] = (double)($z * $c - $x * $s);
                }
                $z = PYTHAG($f, $h);
                $w[$j] = (double)$z;
                if ($z) {
                    $z = 1.0 / $z;
                    $c = $f * $z;
                    $s = $h * $z;
                }
                $f = ($c * $g) + ($s * $y);
                $x = ($c * $y) - ($s * $g);
                for ($jj = 0; $jj < $m; $jj++) {
                    $y = (double)$a[$jj][$j];
                    $z = (double)$a[$jj][$i];
                    $a[$jj][$j] = (double)($y * $c + $z * $s);
                    $a[$jj][$i] = (double)($z * $c - $y * $s);
                }
            }
            $rv1[$l] = 0.0;
            $rv1[$k] = $f;
            $w[$k] = (double)$x;
        }
    }

    return (1);
}

//-------------------------------------------



/**
 * @param array $a
 * @return array [$U, $V, $S]
 */
function svd(array $a):array
{
    $s = [];
    $v = [];
    dsvd($a, count($a), count($a[0]), $s, $v);
    return [$a, $v, $s];
}


$stopWords = null;

/**
 * @param string $word
 * @return bool
 */
function isStopWords(string $word):bool
{
    global $stopWords;
    if (is_null($stopWords)) {
        $stopWords = require_once 'stop_words.php';
    }
    if ($word == 'они') {
       // var_dump($stopWords[$word]);
       // exit();
    }
    return isset($stopWords[$word]);
}
