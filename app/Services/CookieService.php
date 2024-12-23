<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cookie;
class CookieService
{

    public function __construct(
        $vendor
    )
    {
        $this->vendor = $vendor;
    }


    public function getCookie(): string
    {
        $cookie = Cookie::where('vendor', $this->vendor)->first();
        if($cookie){
            $cookie->cookie = str_replace("; ", ";", $cookie->cookie);
            return $cookie->cookie;
        }
        return '';
    }


    public function setCookie($cookies): bool
    {
        $cookies = str_replace("; ", ";", $cookies);
        try {
            $cookie = Cookie::where('vendor', $this->vendor)->first();
            if($cookie){
                $cookie->cookie = $cookies;
                return $cookie->save();
            }else{
                $cookie = new Cookie();
                if($this->vendor && $cookies){
                    $cookie->vendor = $this->vendor;
                    $cookie->cookie = $cookies;
                    return $cookie->save();
                }

                return false;
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }

        return false;
    }

    public function deleteCookie(): bool
    {
        try {
            $cookie = Cookie::where('vendor', $this->vendor)->first();
            if($cookie){
                $cookie->delete();
                return true;
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }

        return false;
    }
}
