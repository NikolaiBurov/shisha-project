<?php

namespace App\Http\Constants;

class StatusCodes
{
    public function postRequests()
    {
        return (object)[
            '200' => [
                'OK' => 'OK',
                'non_existent_product' => 'There is no product with such id',
                'product_list_empty' => 'There are no products with such ids',
                'empty_categories' => 'Categories list is empty',
                'empty_flavours' => 'Flavours list is empty',
                'wrong_data_type' => 'Provided value is not of type array'
            ],
            '404' => 'NotFound',
            '400' => 'Bad request',
            '406' => [
                'incorrect_Data' => 'Data not provided'
            ]
        ];
    }
}
