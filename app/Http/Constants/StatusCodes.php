<?php

namespace App\Http\Constants;

class StatusCodes
{
    public function postRequests(){
            return (object)[
                '200' => 'Okay',
                '404' => 'NotFound',
                '400' => 'Bad request',
                '406' => [
                    'incorrect_Data' => 'Data not provided'
                ]
            ];
    }
}
