<?php

declare(strict_types=1);

namespace App\Services\History;

use App\Models\History\BrandHistory;
use App\Models\History\ProductHistory;
use App\Models\History\UserCart;
use App\Models\History\UserCookie;
use Illuminate\Support\Facades\DB;

class HistoryProductService
{

    public function setHistoryProduct($request, $data, $vendor)
    {
        DB::beginTransaction();
        try {
            // Obtener datos del request
            $userCookieId = $request->header('user-cookie-id', 1);
            $cartId = $request->header('cart-id');
            $categories = $data['categories'];
            $productId = $data['productId'];
            $ip = $request->header('remote-ip');
            $userAgent = $request->header('user-agent');
            $customerData = $request->header('customer-data');

            // Obtener correo del cliente si está disponible
            $customerEmail = $this->getCustomerEmail($customerData);

            // Manejar UserCookie
            $userCookie = $this->handleUserCookie($userCookieId);
            //$cartId = "BXRp70yjRRO7kS0pEbY1ep4u2A6xoM3V";
            // Manejar UserCart
            $cart = $this->handleUserCart($cartId);

            if(!$cart || !$userCookie){
                return false;
            }

            // Manejar ProductHistory
            $this->handleProductHistory($productId, $vendor, $userAgent, $customerEmail, $customerData, $userCookie->id, $ip, $cart->id);

            // Manejar CategoryHistory
            $this->handleCategoryHistory($categories, $vendor, $userAgent, $customerEmail, $customerData, $userCookie->id, $ip, $cart->id);

            // Manejar BrandHistory
            $this->handleBrandHistory($data['brand'], $vendor, $userAgent, $customerEmail, $customerData, $userCookie->id, $ip, $cart->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function getCustomerEmail($customerData)
    {
        $customerEmail = null;

        if ($customerData) {
            $customer = @json_decode($customerData);
            if ($customer) {
                $customerEmail = $customer->email;
            }
        }

        return $customerEmail;
    }

    private function handleUserCookie($userCookieId)
    {
        if(!$userCookieId){
            return false;
        }

        $userCookie = UserCookie::where('cookie_id', $userCookieId)->first();

        if (!$userCookie) {
            $userCookie = new UserCookie();
            $userCookie->cookie_id = $userCookieId;
            $userCookie->save();
        }

        return $userCookie;
    }

    private function handleUserCart($cartId)
    {
        if(!$cartId){
            return false;
        }

        $userCart = UserCart::where('cart_id', $cartId)->first();

        if (!$userCart) {
            $userCart = new UserCart();
            $userCart->cart_id = $cartId;
            $userCart->save();
        }

        return $userCart;
    }

    private function handleCategoryHistory($categories, $vendor, $userAgent, $customerEmail, $customerData, $userCookieId, $ip, $cartId)
    {
        if (empty($categories)) {
            return false;
        }

        foreach ($categories as $category) {
            $categoryData = [
                'category' => $category['name'],
                'vendor' => $vendor,
                'user_cart_id' => $cartId,
                'user_cookie_id' => $userCookieId
            ];

            // Verificar si ya existe un registro con el user_cookie_id
            $existingRecord = DB::table('category_history')
                ->where('category', $category['name'])
                ->where('vendor', $vendor)
                ->where('user_cookie_id', $userCookieId)
                ->first();

            $updateData = [
                'category_url' => $category['link'],
                'user_agent' => $userAgent,
                'customer' => $customerEmail,
                'customer_data' => $customerData,
                'ip' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existingRecord) {
                // Si existe, solo actualiza sin incrementar el count
                DB::table('category_history')->updateOrInsert(
                    $categoryData,
                    array_merge($updateData, [
                        'updated_at' => date('Y-m-d H:i:s')
                    ])
                );
            } else {
                // Si no existe, inserta el registro e incrementa el count
                DB::table('category_history')->updateOrInsert(
                    $categoryData,
                    array_merge($updateData, [
                        'count' => DB::raw('count + 1'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ])
                );
            }
        }
    }


    private function handleBrandHistory($brand, $vendor, $userAgent, $customerEmail, $customerData, $userCookieId, $ip, $cartId)
    {
        if(!$brand){
            return false;
        }

        $brandHistory = BrandHistory::where('brand', $brand)
            ->where('vendor', $vendor)
            ->where('user_cookie_id', $userCookieId)
            ->first();

        if (!$brandHistory) {
            $brandHistory = new BrandHistory();
            $count = $brandHistory->count ?? 0;
            $brandHistory->brand = $brand;
            $brandHistory->count = $count + 1;
            $brandHistory->vendor = $vendor;
            $brandHistory->user_agent = $userAgent;
            $brandHistory->customer = $customerEmail;
            $brandHistory->customer_data = $customerData;
            $brandHistory->ip = $ip;
            $brandHistory->user_cart_id = $cartId;
            $brandHistory->user_cookie_id = $userCookieId;

            $brandHistory->save();
        }

    }

    private function handleProductHistory($productId, $vendor, $userAgent, $customerEmail, $customerData, $userCookieId, $ip, $cartId)
    {
        if(!$productId || !$vendor){
            return false;
        }

        $productHistory = ProductHistory::where('product_id', $productId)
            ->where('vendor', $vendor)
            ->where('user_cookie_id', $userCookieId)
            ->first();

        if (!$productHistory) {
            $productHistory = new ProductHistory();

            $count = $productHistory->count ?? 0;
            $productHistory->count = $count + 1;
            $productHistory->product_id = $productId;
            $productHistory->vendor = $vendor;
            $productHistory->user_agent = $userAgent;
            $productHistory->customer = $customerEmail;
            $productHistory->customer_data = $customerData;
            $productHistory->ip = $ip;

            $productHistory->user_cart_id = $cartId;
            $productHistory->user_cookie_id = $userCookieId;

            $productHistory->save();
        }



        return $productHistory;
    }
}
