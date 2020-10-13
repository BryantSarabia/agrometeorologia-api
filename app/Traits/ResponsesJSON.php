<?php
namespace App\Traits;

trait ResponsesJSON
{
    public function ResponseError($status, $title, $detail = ''){
        return response()->json([
            'errors' => [
                [
                    'status' => $status,
                    'title' => $title,
                    'detail' => $detail

                ]
            ],
        ],$status,['Content-Type' => 'application/vnd.api+json']);
    }

    /* Fare metodo per ritornare i dati */
}
