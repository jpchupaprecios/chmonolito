<?php

namespace App\Services;

class RestrictionGuardService
{
    //RestrictionGuardBrand
    //RestrictionGuardCategory
    //RestrictionGuardKeyword
    //RestrictionGuardAsin

    /*
     * Restricciones
     * Cookies
     * Bencharks
     *
     *
     * */

    public function __construct() {}

    public function checkRestriction($restriction, $value)
    {
        switch ($restriction) {
            case 'brand':
                return $this->checkBrandRestriction($value);
                break;
            case 'category':
                return $this->checkCategoryRestriction($value);
                break;
            case 'keyword':
                return $this->checkKeywordRestriction($value);
                break;
            case 'asin':
                return $this->checkAsinRestriction($value);
                break;
            default:
                return false;
        }
    }

    public function checkBrandRestriction($value)
    {
        //check if brand is restricted
        return false;
    }

    public function checkCategoryRestriction($value)
    {
        //check if category is restricted
        return false;
    }

    public function checkKeywordRestriction($value)
    {
        //check if keyword is restricted
        return false;
    }

    public function checkAsinRestriction($value)
    {
        //check if asin is restricted
        return false;
    }


}
