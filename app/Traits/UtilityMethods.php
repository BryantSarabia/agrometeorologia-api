<?php

namespace App\Traits;

use DateTime;

trait UtilityMethods{

    public function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function validateID($id){
        return preg_match('/[^1-9][0-9]*$/',$id);
    }
}
