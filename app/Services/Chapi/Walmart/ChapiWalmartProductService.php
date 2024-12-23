<?php

declare(strict_types=1);

namespace App\Services\Chapi\Walmart;

use App\Models\Product\ProductDetails;
use App\Services\Interfaces\ProductServiceInterface;
use Exception;
use Illuminate\Http\Request;
use App\Parsers\Chapi\Walmart\Product\ChapiWalmartProductDetailParser;
use DOMDocument;
use DOMXPath;

final class ChapiWalmartProductService implements ProductServiceInterface
{
    private $productDetailParser;
    private array $cookie;

    public function __construct()
    {
        $this->productDetailParser = new ChapiWalmartProductDetailParser();
    }

    /**
     * @throws Exception
     */
    public function getProductDetails(
        Request $request,
        string  $productId,
        string  $vendor,
                $getRelatedProducts = false,
                $getHtml = false
    ): ProductDetails {
        $this->cookie = [
            'TS01fc8e13' => '012b2f70689bb6b6a7c9dae7924c8ae45759c48a8b8687512e1ea7ddf2e2dc2f5c1694d865b451ad2bcb8e0554cd1b1041e236a1bf',
            'TS014bbe1d' => '012b2f70689bb6b6a7c9dae7924c8ae45759c48a8b8687512e1ea7ddf2e2dc2f5c1694d865b451ad2bcb8e0554cd1b1041e236a1bf',
            'TS9003c8fa027' => '08e53f0268ab2000aa8a123e25d77d1eb81386373be820617b9d76673898c5c34052602745a14c7808abb1dcd811300010e31b7a51464886e404051a280a51c54512c7cfba0be3d245960a1a7487380dbdd3ec0a8b373fd3f8579190dae76464',
            'TS01290114' => '012b2f70689bb6b6a7c9dae7924c8ae45759c48a8b8687512e1ea7ddf2e2dc2f5c1694d865b451ad2bcb8e0554cd1b1041e236a1bf',
            'TS01782124' => '012b2f70689bb6b6a7c9dae7924c8ae45759c48a8b8687512e1ea7ddf2e2dc2f5c1694d865b451ad2bcb8e0554cd1b1041e236a1bf',
            'adblocked' => 'false',
            'ak-origin-route' => 'legacy',
            'bm_sz' => '01074171387EA40B1627E3EDA0B5F9CE~YAAQ1NB4aGX37OOQAQAA+pus5hhCfGILv4FPJ3ST/nI1mfzuyMpRzfPEYvjhhwyMVO5WnoN0vaPi5dEqibeoaYtklhmFdOm37TvQXFjmEhNE/7s8xU3ZHACB4/o8Wv5gvtWuSus/e3FZNvTHMGsFd0loGqH9OQtZBY0Lpkk+0dq3WM780OA/iogdrQWYDcD8Zqju5Hpaq/PsxryjgEr53DHpKlVQRHpazNRDTHSTMbSgyZs/7kIJopdkzVi6+mviZBKM/E1+J8pUrCLsmFWQZ1kkXjzYw2ZGb10pinaPocqYUobZDDtQOQe61d4rRK11QDu6SSTNzg5WnaDqRGAnK9N4WMrDdwRvglZrBxKCLYwNniXuSifbXGbS6i9H5qR+wyHsJB7jpag5H2MjDFJ2ipcM5PnoUnGpRMrINSkQulknVZ6l1BZkcIcIm77Pc7hSBHX8kS/AYU9ISijn0mtUXr8LlqZuPcUvWCXwbiQUMwOtBMhmlgWZhZ+JDQ7NnjqHvkXSh0Aad3RwWekBCN35c0XtnaO0o9aCO7PltmCZtB1Nr3yP74Q3Fp8tNs3pqpWzXCT/u4UriOedlAuCEw==~4600374~3748407',
            'bstc' => 'esbkKSWZA2HEyNb6Ibu7R4',
            'cartId' => '8f7e8a90-45a5-11ef-8aaa-11bc24dfd286',
            'criteo_thirdpartyuserid' => 'pKfANyQN2z0itw3SsyT99RhfrTDuLff3',
            'dimensionData' => '1100',
            'exp-ck' => 'M-lGW5T4k7N1f5QdA1yHcCB1',
            'hasLocData' => '1',
            'kampyleSessionPageCounter' => '1',
            'kampyleUserSession' => '1721847524043',
            'kampyleUserSessionsCount' => '4',
            'kampyle_userid' => 'fe67-7a0e-073e-ee7e-451d-2f07-ba77-9bb3',
            'mdLogger' => 'false',
            'postalCode' => '07840',
            'pxcts' => '30045bc4-49e4-11ef-b5fa-73b5fc71734d',
            'userAppVersion' => 'main-1.155.2-ccaa333-0717T2211',
            'vtc' => 'VEFqwTGf48I9rUplAGmKEw',
            'wm-ea-w2' => 'true',
            'wmt.c' => '0',
            'xpa' => 'M-lGW|T4k7N|VxXiA|f5QdA|iWb0n|wjyYk|yHcCB',
            'xpm' => '0%2B1721856992%2BVEFqwTGf48I9rUplAGmKEw~%2B1',
            'xpth' => 'x-o-vertical%2BEA',
            '__eoi' => 'ID=c80a4e6567e56c52:T=1721376292:RT=1721856993:S=AA-AfjZl7Uw6M7eb2wU9-UpCdj3Y',
            '__gads' => 'ID=6bae20f97b26ab87:T=1721376292:RT=1721856993:S=ALNI_MbBbNO_PD1YdjN3ZAywPg7U5u97kQ',
            '__gpi' => 'UID=00000e88a83dc5b7:T=1721376292:RT=1721856993:S=ALNI_MZFlOEBCYkOWA_r1yPWL5cuJL5Cxw',
            '_abck' => '97CDC5B23DAD5A22BFB7A46E45434E6D~0~YAAQRWgBF/cL5r2QAQAAKvDW5Qy/5R847x0nUhn0uUyhyZwIIDYwHKiO+X0QCXsfK4Z9M05rhFOzcR5v1zWyBKroMaOMvN8uvchy/n38KdqB02r0Wqp2uTJ9rjtwlYEFRIecB68aVfFLj1x6sxf1nEDkNOHZk01SoD0OpN1Kzscg0nZ+AyM6B8vwJzdlp1tzIS31v9Qv355bsxFc7qVB/Wq4Pb5aasRDEGuMyUO2KfwzzqIuhIQlT+qSSUb8CU8XtKmqxEtfsBwPXAva+BSwhcDtX6kWUClQv1sUcFoaumCxGSesbyayFOuO5QCTdxGlY861S5h2p7EPZRg5Dr4TDm4atX+A5I99ZRDMzUjwzTCaFVbG4D9go81/8LFHvF9A3XWLmRLovY/BG9aLlB2/TOkL//3OxeKAesp/~-1~-1~-1',
            '_astc' => 'c1638af5f89c25374c65891219833a4d',
            '_px3' => 'dc2fb99c57f918d6dadacdf97dcd08e3299443191da9b94983a16a83e7fac19a:u/02ztM65M9RTG3Q/vs3Y4pjFX2JWnuqRHpzH0pH9PrDMnTU1wpNco+a/QoHYgcSnlN5J9kqmiZ4g4B/ZI7Zsw==:1000:l8lJ2ydFh8qfIAkXmRX2rvZHxrq9HDx5zKl1QBTK/NDR4WBgipK991W1sGIlBVE1HTrXVGkdpNIdF+vwzWiZwMG4X72ZbckV29SmuBWh3TMCOB312RQwnyY5/hifx2eAoG5hBMLRioZJ7te95St7te1LPzB8dpS/JR8rsaowZb7bjmknzs+EcM+4JnYB8w4UL66Mce0ObZt6q2KZHhZxOiuRob7cfA+dERrz1ubDWn8=',
            '_pxde' => '9c635ffe822c1281575e56c809cbe5c990a444a9bfb4743ba244219511e23134:eyJ0aW1lc3RhbXAiOjE3MjE4NTY5OTMyNzZ9',
            '_pxvid' => '8f08acbb-45a5-11ef-83f6-41f6575e2dbb',
        ];
        /*
                 $this->cookie = [
                    'adblocked' => 'false',
                    'vtc' => 'VEFqwTGf48I9rUplAGmKEw',
                    '_pxvid' => '8f08acbb-45a5-11ef-83f6-41f6575e2dbb',
                    'cartId' => '8f7e8a90-45a5-11ef-8aaa-11bc24dfd286',
                    'mdLogger' => 'false',
                    'kampyle_userid' => 'fe67-7a0e-073e-ee7e-451d-2f07-ba77-9bb3',
                    'wmt.c' => '0',
                    'kampyleUserSession' => '1722367026759',
                    'kampyleUserSessionsCount' => '14',
                    'kampyleSessionPageCounter' => '1',
                    'postalCode' => '07840',
                    'hasLocData' => '1',
                    'ak-origin-route' => 'legacy',
                    '_abck' => '97CDC5B23DAD5A22BFB7A46E45434E6D~0~YAAQApXAF62UUQu…YcQqheR3URaFga4aSSxOUn/FReLXf5qXEk8XBqHm~-1~-1~-1',
                    '_astc' => '655ef063312ee64109759e3ea8d4f6e2',
                    'pxcts' => 'fc521ef8-4fbb-11ef-a1e7-8c324da52fcf',
                    'criteo_thirdpartyuserid' => 'WcBIut3zO9yM043Ceuiu8uBvH02qFfC0',
                    'dimensionData' => '980',
                    'userAppVersion' => 'main-1.157.1-780d727-0730T1930',
                    'xpa' => 'M-lGW|VxXiA|iWb0n|pwQHP|wjyYk|yHcCB',
                    'exp-ck' => 'M-lGW5yHcCB1',
                    'wm-ea-w2' => 'true',
                    'bm_sz' => 'F15CFBDB02E55B40F126041973A1CEF4~YAAQApXAFwitUQuRA…Veiy97Z4W87CkdL9qKzM2xUSRJUEaPQ==~3619127~4273734',
                    'xpth' => 'x-o-vertical%2BEA',
                    'xpm' => '0%2B1722488253%2BVEFqwTGf48I9rUplAGmKEw~%2B1',
                    'TS01782124' => '012b2f7068fc300ed211c4bab3e3e9a4eb88fbbbdba32fe72b…2b4cfc0e81cfd91bc44a57da26cc7b29e176b39bce3d80939',
                    'TS014bbe1d' => '012b2f7068fc300ed211c4bab3e3e9a4eb88fbbbdba32fe72b…2b4cfc0e81cfd91bc44a57da26cc7b29e176b39bce3d80939',
                    'TS01290114' => '012b2f7068fc300ed211c4bab3e3e9a4eb88fbbbdba32fe72b…2b4cfc0e81cfd91bc44a57da26cc7b29e176b39bce3d80939',
                    'TS01fc8e13' => '012b2f7068fc300ed211c4bab3e3e9a4eb88fbbbdba32fe72b…2b4cfc0e81cfd91bc44a57da26cc7b29e176b39bce3d80939',
                    'bstc' => 'WrjgTaTqsUtffsltaJv2hc',
                    '__gads' => 'ID=6bae20f97b26ab87:T=1721376292:RT=1722488254:S=ALNI_MbBbNO_PD1YdjN3ZAywPg7U5u97kQ',
                    '__gpi' => 'UID=00000e88a83dc5b7:T=1721376292:RT=1722488254:S=ALNI_MZFlOEBCYkOWA_r1yPWL5cuJL5Cxw',
                    '__eoi' => 'ID=c80a4e6567e56c52:T=1721376292:RT=1722488254:S=AA-AfjZl7Uw6M7eb2wU9-UpCdj3Y',
                    '_px3' => 'b15c50a9881485a490fab8309a17e14afcb024dad5a8f278ef…KIzF2qMmRkPUTHLs4BTBKNjVF/jINt76aii55mjZqBHYvS5g=',
                    'TS9003c8fa027' => '0842fb9b35ab200098f20127caadd5f63bbdc409182e0a29a9…3b451393b597e5296ee4b4e9e6bd7661eae603ea3f7ad4219',
                    '_pxde' => 'ef2e7499f4b4197052fb0c71697c04b6de67a871e54e4826ce…b5540f382b9b:eyJ0aW1lc3RhbXAiOjE3MjI0ODgyNTc1NzV9'
                ];
        */
        $dom = $this->fetchProductDetails($request, $productId);
        $xpath = new DOMXPath($dom);
        return $this->productDetailParser->parse($dom, $xpath, $vendor, $productId, $this->cookie, $getRelatedProducts, $getHtml);
    }

    public function fetchProductDetails(Request $request, string $productId): \DOMDocument
    {
        $url = 'https://www.walmart.com.mx/ip/' . $productId;

        $response = ChapiWalmartWebContentService::scrape($url, $this->cookie, false);

        $body = preg_replace('/\s\s+/', '', $response);
        $body = preg_replace('/\n/', '', $body);

        $dom = new DOMDocument();
        @$dom->loadHTML(utf8_encode($body));
        return $dom;
    }
}
