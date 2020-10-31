<?php

namespace App\Traits;

use DateTime;

trait UtilityMethods{

    public function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function validateID($id){
        return preg_match('/[^0-9]/',$id);
    }

    public function validateCoordinate($coordinate){
        return filter_var($coordinate, FILTER_VALIDATE_FLOAT);
    }
}
