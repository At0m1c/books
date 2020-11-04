<?php
function binSearch(array $array, int $num): bool
{
    if (!empty($array)) {
        $arrayCount = (count($array) / 2) - 1;
        list($arLeft, $arRight) = array_chunk($array, $arrayCount + 1);

        if ($arLeft[$arrayCount] == $num || $arRight[$arrayCount] == $num) {
            return true;
        } elseif ($arLeft[$arrayCount] < $num) {
            return binSearch($arRight, $num);
        } elseif ($arRight[$arrayCount] > $num) {
            return binSearch($arLeft, $num);
        }
    } else {
        return false;
    }
}