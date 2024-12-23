<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Exchange;
final class Price
{
    public static function pesosAdolar($price, $dolar = null){
        if(!$price){
            return (float) $price;
        }

        if(!$dolar){
            $dolar = env('DOLAR_PRICE', 0);
        }

        if(!is_numeric($dolar) || !is_numeric($price)){
            return (float) $price;
        }
        $currentPrice =  $price / $dolar;
        $currentPrice = (float) number_format($currentPrice, 2, '.', '');

        if($currentPrice < 1){
            return 0.0;
        }

        return (float) $currentPrice;
    }
	public static function cotizarDolar($price)
    {
        return $price;

        if (!$price) {
            return 0;
        }

        $config = [];
        $total = 0;
        $config['ranges'] = [
            ['from' => '1', 'to' => '100', 'precio' => '60', 'utility' => '60'],
            ['from' => '101', 'to' => '200', 'precio' => '40', 'utility' => '80'],
            ['from' => '201', 'to' => '300', 'precio' => '40', 'utility' => '120'],
            ['from' => '301', 'to' => '500', 'precio' => '40', 'utility' => '200'],
            ['from' => '501', 'to' => '800', 'precio' => '40', 'utility' => '320'],
            ['from' => '801', 'to' => '1100', 'precio' => '40', 'utility' => '440'],
            ['from' => '1101', 'to' => '1500', 'precio' => '40', 'utility' => '600'],
            ['from' => '1501', 'to' => '1800', 'precio' => '40', 'utility' => '720'],
            ['from' => '1801', 'to' => '2100', 'precio' => '40', 'utility' => '840'],
            ['from' => '2101', 'to' => '2500', 'precio' => '40', 'utility' => '1000'],
            ['from' => '2501', 'to' => '3000', 'precio' => '40', 'utility' => '1200'],
            ['from' => '3001', 'to' => '4000', 'precio' => '40', 'utility' => '1400'],
            ['from' => '4001', 'to' => '5000', 'precio' => '40', 'utility' => '1750'],
            ['from' => '5001', 'to' => '6000', 'precio' => '40', 'utility' => '2100'],
            ['from' => '6001', 'to' => '7000', 'precio' => '40', 'utility' => '2450'],
            ['from' => '7001', 'to' => '8000', 'precio' => '40', 'utility' => '2800'],
            ['from' => '8001', 'to' => '9000', 'precio' => '40', 'utility' => '3150'],
            ['from' => '9001', 'to' => '10000', 'precio' => '40', 'utility' => '3300'],
            ['from' => '10001', 'to' => '11000', 'precio' => '40', 'utility' => '3500'],
            ['from' => '11001', 'to' => '12000', 'precio' => '40', 'utility' => '3500'],
            ['from' => '12001', 'to' => '13000', 'precio' => '40', 'utility' => '3900'],
            ['from' => '13001', 'to' => '14000', 'precio' => '40', 'utility' => '4200'],
            ['from' => '14001', 'to' => '15000', 'precio' => '40', 'utility' => '4500'],
            ['from' => '15001', 'to' => '16000', 'precio' => '40', 'utility' => '4800'],
            ['from' => '16001', 'to' => '17000', 'precio' => '40', 'utility' => '5100'],
            ['from' => '17001', 'to' => '18000', 'precio' => '40', 'utility' => '5400'],
            ['from' => '18001', 'to' => '19000', 'precio' => '40', 'utility' => '5700'],
            ['from' => '19001', 'to' => '20000', 'precio' => '40', 'utility' => '6000'],
            ['from' => '20001', 'to' => '21000', 'precio' => '40', 'utility' => '6300'],
            ['from' => '21001', 'to' => '22000', 'precio' => '40', 'utility' => '6600'],
            ['from' => '22001', 'to' => '23000', 'precio' => '40', 'utility' => '6900'],
            ['from' => '23001', 'to' => '24000', 'precio' => '40', 'utility' => '7200'],
            ['from' => '24001', 'to' => '25000', 'precio' => '40', 'utility' => '7500'],
            ['from' => '25001', 'to' => '26000', 'precio' => '40', 'utility' => '7800'],
            ['from' => '26001', 'to' => '27000', 'precio' => '40', 'utility' => '8100'],
            ['from' => '27001', 'to' => '28000', 'precio' => '40', 'utility' => '8400'],
            ['from' => '28001', 'to' => '29000', 'precio' => '40', 'utility' => '8700'],
            ['from' => '29001', 'to' => '30000', 'precio' => '40', 'utility' => '9000'],
            ['from' => '30001', 'to' => '31000', 'precio' => '40', 'utility' => '9300'],
            ['from' => '31001', 'to' => '32000', 'precio' => '40', 'utility' => '9600'],
            ['from' => '32001', 'to' => '33000', 'precio' => '40', 'utility' => '9900'],
            ['from' => '33001', 'to' => '34000', 'precio' => '40', 'utility' => '10200'],
            ['from' => '34001', 'to' => '35000', 'precio' => '40', 'utility' => '10500'],
            ['from' => '35001', 'to' => '36000', 'precio' => '40', 'utility' => '10800'],
            ['from' => '36001', 'to' => '37000', 'precio' => '40', 'utility' => '11100'],
            ['from' => '37001', 'to' => '38000', 'precio' => '40', 'utility' => '11400'],
            ['from' => '38001', 'to' => '39000', 'precio' => '40', 'utility' => '11700'],
            ['from' => '39001', 'to' => '40000', 'precio' => '40', 'utility' => '12000'],
            ['from' => '40001', 'to' => '41000', 'precio' => '40', 'utility' => '13500'],
            ['from' => '41001', 'to' => '45000', 'precio' => '40', 'utility' => '15000'],
            ['from' => '45001', 'to' => '50000', 'precio' => '40', 'utility' => '16500'],
            ['from' => '50001', 'to' => '55000', 'precio' => '40', 'utility' => '18000'],
            ['from' => '55001', 'to' => '60000', 'precio' => '40', 'utility' => '19500'],
            ['from' => '60001', 'to' => '65000', 'precio' => '40', 'utility' => '21000'],
            ['from' => '65001', 'to' => '70000', 'precio' => '40', 'utility' => '22500'],
            ['from' => '70001', 'to' => '75000', 'precio' => '40', 'utility' => '24000'],
            ['from' => '75001', 'to' => '100000', 'precio' => '40', 'utility' => '25500'],
            ['from' => '100001', 'to' => '10000000', 'precio' => '40', 'utility' => '30000'],
        ];

        $config['suministros'] = '140';
        $config['tax'] = '9';
        $config['comision'] = '13';

        $dolar = env('DOLAR_PRICE', 0);

        if ($dolar) {
            $price = $price * $dolar;
            $total_cost = (($config['tax'] / 100) * $price) + $price;
            $suministros = $config['suministros'];
            $comision = (($config['comision'] / 100) * $total_cost);

            $total_cost = number_format((float) $total_cost, 2, '.', '');
            $suministros = number_format((float) $suministros, 2, '.', '');
            $comision = number_format((float) $comision, 2, '.', '');

            $utility = 0;

            foreach ($config['ranges'] as $range) {
                if (($price >= $range['from']) && ($price <= $range['to'])) {
                    $utility = $range['utility'];
                    break;
                }
            }

            $total = $total_cost + $suministros + $comision + $utility;
            $total = number_format((float) $total, 2, '.', '');
        }

        return $total;
    }

    public static function getDolarPrice(){
        $exchange = Exchange::first();
        $dolar = 0;

        if($exchange){
            $dolar = $exchange->exchange_rate;
        }

        if(!$dolar){
            self::syncExchange();
            $exchange = Exchange::first();
            if(!$exchange || $exchange->exchange_rate < 1){
                return env("DOLAR_PRICE", 17.5);
            }

            return $exchange->exchange_rate;
        }

        return $dolar;
    }


    public static function syncExchange(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.apilayer.com/fixer/latest?symbols=MXN&base=USD",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "apikey: 9yX48ez50I88ejTkY3qUGJUwmOdfTawL"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);
        $MXN = false;
        $parserito_response = @json_decode($response, true);
        if(isset($parserito_response['rates'])){
            $MXN = $parserito_response['rates']['MXN'];
        }

        if($MXN){
            $currentMXN = $MXN;

            $exchangeModel = Exchange::where("currency", "USD")->first();

            if(!$exchangeModel){
                $exchangeModel = new Exchange();
            }

            $exchangeModel->exchange_rate = $currentMXN;
            $exchangeModel->currency = "USD";
            $exchangeModel->save();
        }

    }

}
