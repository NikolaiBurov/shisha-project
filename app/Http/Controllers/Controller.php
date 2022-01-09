<?php

namespace App\Http\Controllers;

use App\Http\Constants\StatusCodes;
use App\Http\Services\CartHelper;
use App\Http\Services\ErrorService;
use App\Http\Services\ImageService;
use App\Http\Services\TranslationsHelper;
use App\Models\Flavour;
use App\Models\FlavourVariation;
use App\Models\PublicUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @property PublicUser $users
 * @property Flavour $flavours
 * @property FlavourVariation $flavour_variations
 * @property StatusCodes $status_codes
 * @property ErrorService $error_service
 * @property CartHelper $cart_helper
 * @property TranslationsHelper $translation_helper
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param PublicUser $users
     * @param Flavour $flavour
     * @param FlavourVariation $flavour_variations
     * @param StatusCodes $status_codes
     * @param ErrorService $errorService
     * @param CartHelper $cart_helper
     * @param TranslationsHelper $translationsHelper
     */
    public function __construct(PublicUser         $users,
                                Flavour            $flavour,
                                FlavourVariation   $flavour_variations,
                                StatusCodes        $status_codes,
                                ErrorService       $errorService,
                                CartHelper         $cart_helper,
                                TranslationsHelper $translationsHelper
    )
    {
        $this->users = $users;
        $this->flavours = $flavour;
        $this->flavour_variations = $flavour_variations;
        $this->status_codes = $status_codes;
        $this->error_service = $errorService;
        $this->cart_helper = $cart_helper;
        $this->translation_helper = $translationsHelper;
    }
}
