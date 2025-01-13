<?php

namespace App\Helpers;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\Cookie\Cookie;
class SymfonyPanther
{

    public static function run($url, $userAgent, $cookie){

        $client = Client::createChromeClient(
            null, // Deja la URL de chromedriver por defecto
            [
                $userAgent
            ]
        );

        $client->getCookieJar()->set(new Cookie(
            $cookie
        ));

        $crawler = $client->request('GET', $url);
        sleep(rand(5, 15)); // Esperar entre 1 y 3 segundos
        // Hacer click en un enlace
        /*$link = $crawler->selectLink('About')->link();
        $crawler = $client->click($link);

        // Llenar campos, hacer submit, etc.
        $form = $crawler->selectButton('Submit')->form();
        $form['username'] = 'tester';
        $form['password'] = 'secret';
        $crawler = $client->submit($form);

        // Captura de pantalla
        $client->takeScreenshot('screenshot.png');*/
        return true;
    }


    public static function getCookies($url){
        $client = Client::createChromeClient();
        $client->request('GET', $url);
        $cookies = $client->getCookieJar()->all();
        return $cookies;
    }


}

