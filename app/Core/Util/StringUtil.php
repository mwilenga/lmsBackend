<?php

namespace App\Core\Util;

class StringUtil
{

    public static function isEmpty($string)
    {
        if ($string == null) {
            return true;
        }
        if ($string == ' ') {
            return true;
        }
        if ($string == '') {
            return true;
        }
        if ($string == " ") {
            return true;
        }
        if ($string == "") {
            return true;
        }
        return false;
    }

    public static function isNotEmpty($string)
    {
        return !self::isEmpty($string);
    }

    public static function compareSimilarity($old, $new)
    {
        $perc = 0;

        $old = strtolower($old);
        $new = strtolower($new);

        similar_text($old, $new, $perc);

        return $perc;
    }

    public static function splitAlphaNumeric($alphaNumeric)
    {
        $alpha = $numbers = array();

        if (preg_match_all('/([a-z ]+[0-9]+)/i', $alphaNumeric, $mt)) {
            $nrmt = count($mt[0]);
            for ($i = 0; $i < $nrmt; $i++) {
                if (preg_match('/([a-z ]+)([0-9]+)/i', $mt[0][$i], $mt2)) {
                    $alpha[$i] = trim($mt2[1]);
                    $numbers[$i] = $mt2[2];
                }
            }
        }

        return array('alpha' => $alpha, 'numberic' => $numbers);
    }

    public static function getPersonNames($namestrs)
    {

        $allnames = array();
        $namestrs = trim($namestrs);
        $namestrs = str_replace(array(',', ' and ', ' & ', '&amp;', '/'), '|', $namestrs);

        $namestrs = explode('|', $namestrs);

        foreach ($namestrs as $key => $namestr) {
            $namestr = explode(' ', trim($namestr));

            if (count($namestr) == 1 || (count($namestr) == 2 && strlen(trim($namestr[1])) < 3)) {
                $firstname = $namestr[0];
                if (isset($namestr[1])) {
                    $middlename = $namestr[1];
                } else {
                    $middlename = '';
                }
                $lastname = '';
                $thenames = $namestrs; //print_r($thenames); //echo $key;
                $thenames = array_slice($thenames, $key + 1, NULL, TRUE);  //print_r($thenames);

                foreach ($thenames as $c => $a) {
                    $a = explode(' ', trim($a)); // print_r( $a);

                    if (count($a) > 1 && trim($lastname) == '') {
                        $lastname = $a[count($a) - 1];
                    }
                }
            } else if (count($namestr) == 2) {
                $firstname = $namestr[0];
                $middlename = '';
                $lastname = $namestr[1];
            } else if (count($namestr) == 3) {
                $firstname = $namestr[0];
                $middlename = $namestr[1];
                $lastname = $namestr[2];
            } else if (count($namestr) > 3) {
                $firstname = $namestr[0];
                if (strlen($firstname) > 3) {
                    $middlename = $namestr[1];
                } else {
                    $firstname = $namestr[0] . " " . $namestr[1];
                    $middlename = $namestr[2];
                }
                $lastname = str_replace(array($firstname, $middlename), "", implode(' ', $namestr));
                $lastname = trim($lastname);
            }

            if ($lastname == '3rd') {
                $lastname = trim($middlename) . " "  . trim($lastname);
                $middlename = '';
            }

            $allnames = array('first_name' => $firstname, 'middle_name' => $middlename, 'last_name' => $lastname);
        }

        return $allnames;
    }

    public static function phoneNumber($givenPhoneNumber)
    {
        $phoneNumber = str_replace(" ", "", $givenPhoneNumber);
        $phoneNumber = str_replace("+255", "0", $phoneNumber);
        $phoneNumber = preg_replace('/^255/', '0', $phoneNumber, -1);
        return $phoneNumber;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function findString($needle, $haystack, $i, $word)
    {   // $i should be "" or "i" for case insensitive
        if (strtoupper($word) == "W") {   // if $word is "W" then word search instead of string in string search.
            if (preg_match("/\b{$needle}\b/{$i}", $haystack)) {
                return true;
            }
        } else {
            if (preg_match("/{$needle}/{$i}", $haystack)) {
                return true;
            }
        }
        return false;
        // Put quotes around true and false above to return them as strings instead of as bools/ints.
    }

    public static function containsNumber($givenString)
    {
        return preg_match('/\\d/', $givenString) > 0;
    }

    public static function notContainsNumber($givenString)
    {
        return !self::containsNumber($givenString);
    }

    public static function containsSpecialCharacter($givenString)
    {
        return preg_match('/[^a-zA-Z\d]/', $givenString) > 0;
    }

    public static function notContainsSpecialCharacter($givenString)
    {
        return !self::containsSpecialCharacter($givenString);
    }

    public static function ContainsUpperCase($givenString)
    {
        return preg_match('/[A-Z]/', $givenString) > 0;
    }

    public static function notContainsUpperCase($givenString)
    {
        return !self::ContainsUpperCase($givenString);
    }

    public static function pad($num, $size)
    {
        $s = $num . "";
        while (strlen($s) < $size) $s = "0" . $s;
        return $s;
    }

    public static function helpLike($likeVal)
    {
        $likeVal = str_replace("+", " ", $likeVal);
        $likeVal = str_replace("-", " ", $likeVal);
        $returnLikeVal = "%";
        $listOfLikeVal = preg_split('/\s+/', $likeVal);
        foreach ($listOfLikeVal as $likeValItem) {
            $returnLikeVal .= "{$likeValItem}%";
        }
        return $returnLikeVal;
    }
}
