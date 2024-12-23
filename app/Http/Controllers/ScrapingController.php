<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class ScrapingController extends Controller
{
    protected const COOKIE_PATH = 'app/';

    public function showFormAndabFlush()
    {
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        for ($i = 0; $i < 20; $i++) {
            echo "<p>Chunk $i</p>";
            echo str_repeat(" ", 1024); // Padding
            flush();
            usleep(500000); // Delay
        }
    }
    public function showForm()
    {
        // Muestra la vista con el formulario
        return view('scraping.form');
    }

    public function performScraping(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);
        $url = "https://www.amazon.com/s?k=" . urlencode($query) . "&language=es_US&page={$page}";
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; ...'; // Tus cookies

        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        // Enviar HTML inicial
        echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Resultados de Amazon</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .result { border: 1px solid #ccc; margin: 10px 0; padding: 10px; }
    </style>
</head>
<body>
<h1 style='color: red'>Resultados de Amazon</h1>
<div>
    <a href='/'>Volver al formulario</a>
</div>
<div style='margin-top: 20px;'>";

        // Asegurarse de enviar los datos al cliente
        flush();

        // Añadir un padding para evitar buffering
        echo str_repeat(" ", 1024);
        flush();

        // Configurar cURL
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => self::getHeaders($cookie),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => false, // Deshabilitar retorno automático.
            CURLOPT_COOKIEFILE => $cookiePath,
            CURLOPT_COOKIEJAR => $cookiePath,
            CURLOPT_USERAGENT => self::getUserAgent(),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_ENCODING => '',
            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) {
                // Procesar cada fragmento del HTML
                $parsedChunk = AmazonSearchParser::parse($chunk);
                if ($parsedChunk) {
                    echo "<div class='result'>{$parsedChunk}</div>";
                    echo "<!-- chunk -->"; // Ayuda a forzar el rendering
                    echo str_repeat(" ", 1024); // Padding;
                    //retraso en milisegundos
                    //usleep(100000); // Delay de 0.5 segundos
                    flush(); // Enviar el chunk al navegador
                }
                return strlen($chunk);
            },
        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<p>Error: " . curl_error($curl) . "</p>";
        }

        curl_close($curl);

        // Finalizar la página HTML
        echo "</div></body></html>";
        flush(); // Asegurarse de enviar el contenido final
    }




    private function getHeaders(): array
    {
        return [
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Language: es-US',
            'User-Agent: ' . $this->getUserAgent(),
        ];
    }

    private function getUserAgent(): string
    {
        return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36";
    }
}
