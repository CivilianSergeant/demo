<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/13/2016
 * Time: 12:31 PM
 */

namespace App\Utils;


class DetectTerritory
{

    public static function detect($x,$y,$resultSet)
    {
        if(!empty($resultSet)) {
            foreach ($resultSet as $result) {

                $dblat = DetectTerritory::getCSVValues($result->co_ordinate_x);
                $dblon = DetectTerritory::getCSVValues($result->co_ordinate_y);
                $z = count($dblat);

                if (DetectTerritory::pointInPolygon($x, $y, $z, $dblat, $dblon) == null) {
                    return $result;
                }
            }

            return null;

        }

        return null;
    }

    public static function getCSVValues($string, $separator = ",")
    {
        $elements = explode($separator, $string);
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes % 2 == 1) {
                for ($j = $i + 1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') % 2 == 1) { // Look for an odd-number of quotes
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j - $i + 1,
                            implode($separator, array_slice($elements, $i, $j - $i + 1)));
                        break;
                    }
                }
            }
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr =& $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
        }
        return $elements;
    }

    public static function pointInPolygon($x, $y, $z, $polyX, $polyY)
    {

        $i = 0;
        $polySides = $z;
        $j = $polySides - 1;
        $oddNodes = "NO";


        for ($i = 0; $i < $polySides; $i++) {
            if ($polyY[$i] < $y && $polyY[$j] >= $y || $polyY[$j] < $y && $polyY[$i] >= $y) {
                if ($polyX[$i] + ($y - $polyY[$i]) / ($polyY[$j] - $polyY[$i]) * ($polyX[$j] - $polyX[$i]) < $x) {
                    $oddNodes = !$oddNodes;
                }
            }
            $j = $i;
        }

        return $oddNodes;
    }

}