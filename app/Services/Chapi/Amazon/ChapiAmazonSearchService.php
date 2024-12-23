<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Models\AwsSession;
use App\Models\Search\Result;
use App\Parsers\Chapi\Amazon\Results\ChapiAmazonResultParser;
use App\Services\CookieService;
use App\Services\Interfaces\SearchServiceInterface;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;

final class ChapiAmazonSearchService implements SearchServiceInterface
{
    private $resultParser;

    public static $vendor = 'amazon';
    /**
     * @var CookieService
     */
    private $cookieService;

    /**
     * @const AMAZON_COOKIE
     */
    const AMAZON_COOKIE = "amazon";

    public function __construct(

    ) {
        $this->resultParser = new ChapiAmazonResultParser();
        $this->cookieService = new CookieService(self::AMAZON_COOKIE);
    }

    public function searchProducts(Request $request, $facets, string $query, int $page, $debug = false): Result|array
    {
        $result = $this->fetchSearchResults($request, $query, $page, $facets, $debug);

        $resultParse = $this->resultParser->parse($result, self::$vendor, $query, $debug);


        $ret['result'] = $resultParse['result'];

        if ($debug) {
            $ret['debugging'] = $resultParse['debugging'];
        }

        return $ret;
    }

    public function fetchSearchResults(
        Request $request,
        string $query,
        int $page,
                $facets = null,
                $debug = false
    ): \DOMXPath|array {
        $query = urldecode($query);
        $url = 'https://www.amazon.com/s?k=' . urlencode($query) . '&language=es_US&page=' . $page;
        if ($facets) {
            $facets = str_replace('/', ':', $facets);
            $url .= '&rh=' . $facets;
        }

        $cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; skin=noskin; ubid-main=134-0166731-7376829; lc-main=es_US; session-id-time=2082787201l; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws_lang=en; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; s_cc=true; aws-target-visitor-id=1731609730515-285914.44_0; remember-account=false; regStatus=registered; aws-account-alias=chupaprecios; x-main=yjeQAQ6NDzU34D2fmwGjokl9rRstG?3ABU1uqzvT7?9SH1MuWvXw7?mAqRGVyM3i; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C20042%7CMCMID%7C64029980295631160031792846785696741143%7CMCAAMLH-1732297343%7C4%7CMCAAMB-1732297343%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1731699743s%7CNONE%7CMCAID%7CNONE%7CMCSYNCSOP%7C411-20049%7CvVersion%7C4.4.0; aws-target-data=%7B%22support%22%3A%221%22%7D; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Auser%2Fjotapey%22%2C%22alias%22%3A%22chupaprecios%22%2C%22username%22%3A%22jotapey%22%2C%22keybase%22%3A%22G3w6E3KvhbSjSOSJTD%2BN1RDpvnFhY5fEwylvZMFtDTE%5Cu003d%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; aws-userInfo-signed=eyJ0eXAiOiJKV1MiLCJrZXlSZWdpb24iOiJ1cy1lYXN0LTIiLCJhbGciOiJFUzM4NCIsImtpZCI6IjkzNTA3OGZiLTY2NzYtNDlhMC1iN2YxLTBjZDAzOTBkMDNjMCJ9.eyJzdWIiOiJjaHVwYXByZWNpb3MiLCJzaWduaW5UeXBlIjoiUFVCTElDIiwiaXNzIjoiaHR0cDpcL1wvc2lnbmluLmF3cy5hbWF6b24uY29tXC9zaWduaW4iLCJrZXliYXNlIjoiRzN3NkUzS3ZoYlNqU09TSlREK04xUkRwdm5GaFk1ZkV3eWx2Wk1GdERURT0iLCJhcm4iOiJhcm46YXdzOmlhbTo6MjUwOTMzNDQwMjc1OnVzZXJcL2pvdGFwZXkiLCJ1c2VybmFtZSI6ImpvdGFwZXkifQ.COQzljVSE_9ggEu2PQbMZJQR5OCNRSXT0PWP0ZlOirECgTzp-GdNYajBYluBOstpCgytoq5Cs-ZyJGoRlWdDqYQmNcCFbl0BjoVuneQHhHLPCRzmiRsk1y36AgHbvWCZ; session-token=XkYnA9vmfVTlwnLoYJXeYu05PGvJyD7AKnJWkagDdvVJ38t1QGwpRYYZWKAeFqMouzYk++xSZHOjgDRYq4NfIL2KxN5df06cBZejrS6sm26Ddmgxs0Ln+/w/fkwpDiZJaawB79htspsUTBCS2h/oKGIMgyEWh1gVZHnZgX1uGF44bapb6wW2KU4rosGKE7wkDgTBZVyyHc83OzEFgrCxPARQiK6T0B2JQUi36iOunkE5jWQu9Hoo/WO07zkHivUtwwsy40YbwNx+jbshoDBe/GqODPwXk51VA/p3bcKl/IqcxspXrUPBbugvhfKo7R8zhlV2lc+w718kGcqTq5O74BE3PUEnFUxO/becm2Zr43q4xC4WgN+OE5ZlbkAT7BWc; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1idm379su.1idm379su.0.0.0; csm-hit=tb:WF37FTDZG8A4MW6TGGTA+s-WF37FTDZG8A4MW6TGGTA|1732687145255&t:1732687145255&adb:adblk_no';//$this->cookieService->getCookie();

        $result = ChapiAmazonWebContentService::scrape($url, $cookie, true, $debug);

        if (!$result) {
            return [];
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($result);
        return new DOMXPath($dom);
    }

}
