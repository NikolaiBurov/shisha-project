<?php

namespace App\Http\Constants;

class StatusCodes
{
    public function postRequests($data = null)
    {
        return (object)[
            '200' => [
                'OK' => 'OK',
                'non_existent_product' => 'There is no product with such id',
                'product_list_empty' => 'There are no products with such ids',
                'empty_categories' => 'Categories list is empty',
                'empty_flavours' => 'Flavours list is empty',
                'empty_users' => 'Users list is empty',
                'wrong_data_type' => 'Provided value is not of type array',
                'user_created' =>  isset($data) ? "User with id {$data['id']} created": "",
                'non_existent_user' => 'User with such username doesnt exist',
                'non_existent_user_id' =>   isset($data) ? "User with id {$data['id']} doesnt exist" : "",
                'incorrect_password' => 'Password doesnt match',
                'update_failed' => 'Failed to update',
                'success_update' => 'User updated successfully'
            ],
            '404' => 'NotFound',
            '400' => 'Bad request',
            '406' => [
                'incorrect_Data' => 'Data not provided'
            ],
            '204' => 'No content'
        ];
    }
}
