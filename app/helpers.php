<?php

function fc($value) {
    $symbol = '$';

    if ($value < 0.0) {
        $symbol = '-$';
        $value *= -1.0;
    }

    return $symbol . number_format($value, 2);
}
