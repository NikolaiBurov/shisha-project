<?php

namespace App\Http\Constants;

class StatusCodes
{
    public function postRequests($data = null)
    {
        return (object)[
            '200' => [
                'OK' => 'OK',
                'non_existent_product' => 'There is no product with such id or  its not translatable',
                'product_list_empty' => 'There are no products with such ids',
                'empty_categories' => 'Categories list is empty',
                'empty_flavours' => 'Flavours list is empty',
                'empty_users' => 'Users list is empty',
                'wrong_data_type' => 'Provided value is not of type array',
                'user_created' => isset($data) ? "User with id {$data['id']} created" : "",
                'non_existent_user' => 'User with such username doesnt exist',
                'non_existent_user_email' => 'User with such email or username doesnt exist',
                'non_existent_user_id' => isset($data) ? "User with id {$data['id']} doesnt exist" : "",
                'incorrect_password' => 'Password doesnt match',
                'update_failed' => 'Failed to update',
                'success_update' => 'User updated successfully',
                'filters_none' => 'Searched filters returned empty list',
                'empty_cart' => 'Cart is empty',
                'user_not_exists' => 'User does not exists',
                'flavour_not_exists' => 'Flavour with such id does not exist',
                'flavour_variation_exists' => 'Flavour variation with such id does not exist',
                'wrong_variation' => 'Given variation id  is not matching given flavour',
                'cart_exists' => 'Cart does not exists',
                'cart_entity_deleted' =>  isset($data) ? "Cart record with id {$data->id}  was deleted" : "",
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
