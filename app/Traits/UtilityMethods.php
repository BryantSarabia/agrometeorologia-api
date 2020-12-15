<?php

namespace App\Traits;

use App\Models\MetaApiConfiguration;
use DateTime;

trait UtilityMethods
{

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function validateID($id)
    {
        return preg_match('/[^0-9]/', $id);
    }

    public function validateCoordinate($coordinate)
    {
        return filter_var($coordinate, FILTER_VALIDATE_FLOAT);
    }

    public function validateString($string)
    {
        return !preg_match('/[^A-Za-z]+/', $string);
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }

    public function is_dir_empty($dir)
    {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }

}
