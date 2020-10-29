<?php
namespace App\Traits;

trait ResponsesJSON
{
    public function ResponseError($status, $title, $detail = ''){
        return response()->json([
            "code"=> $status,
            "title" => $title,
            "details" => $detail
        ],$status,['Content-Type' => 'application/json']);
    }

    /* Fare metodo per ritornare i dati */
}
