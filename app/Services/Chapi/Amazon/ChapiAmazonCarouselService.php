<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Parsers\Chapi\Amazon\Results\ChapiAmazonResultParser;
use App\Services\CookieService;

final class ChapiAmazonCarouselService
{
	private $resultParser;

	public static $vendor = 'amazon';
    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct(

	) {
		$this->resultParser = new ChapiAmazonResultParser();
        $this->cookieService = new CookieService(self::$vendor);
	}

	public function fetchResults(string $query)
	{
		$cookie = $this->cookieService->getCookie();//'session-id=145-1461525-2445366; ubid-main=132-2815328-6927608; aws-target-data=%7B%22support%22%3A%221%22%7D; aws-target-visitor-id=1714592830499-313874.45_0; aws-ubid-main=768-3155673-0408488; regStatus=registered; x-main="bG7DJybfq2Gd5hjp0vFXcKqCf8bQQ83Mtw4c?1doQQaxtUk@cL5ohq5vPLSPIafm"; at-main=Atza|IwEBIMx12JkWIatXsDkB57H79C_wDofj0tihvMyCihThlt7_yhAhGwRUZnXMPDmEzu5KmveBwUijbeE-32mneAhckFZTKCReiH6d-HeA2G9rqBeiISseoNjRuph55Dv8CDMqEaa5CUFBhzQI4ze7moNOaPb4O_8FMVX_2bV4q36xDihRAY-9SgB9C1cPKu1D_iOgerscCRINaCsuQhvP2Y0JZ6GJt3ONL7cJR92Q6YrEtW6fNsXywKPw05vuAC3Gh3Zf5OEfXarqBp1zgjbP_97wyMh6PyMZqMdOqaMAap2tx7lFqQ; sess-at-main="7GQpf+7CLMkjvvgvYiGtxkY0mfyUIE6IqM9JFEFT50w="; sst-main=Sst1|PQHakfelr9XT1Q6R3tWuy2rQCZ3VaEue3i1d8ecshCZsOZL-IDa7xiTdOFQn9o6uSGgeLdw_ksACjSp3tHSgBAEMU3vR5hLNRgjaAexOFHboaCaBk4Tkh4B3C4B-VwaBPFP23EeFRzueCuX_YrXvqnJE3lRb8wcErxmRktNlXpKS3tYuM4z84nVdvdUbN7OR53a2KAithaD6G4Cs7AYMOQ29nHwTZAQUEtFu4q5nCndNWWBsqsEoOkXBime5KkWfiw1ukScnz63MYlV2mIKpocce00w1ztsffLLMqP1IbdswEo0; lc-main=en_US; i18n-prefs=USD; session-id-time=2082787201l; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C19864%7CMCMID%7C21069568127296296461806524275522493580%7CMCAAMLH-1716906165%7C9%7CMCAAMB-1716906165%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1716308565s%7CNONE%7CMCAID%7CNONE%7CvVersion%7C4.4.0%7CMCSYNCSOP%7C411-19871; s_cc=true; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws-userInfo-signed=eyJ0eXAiOiJKV1MiLCJrZXlSZWdpb24iOiJ1cy1lYXN0LTEiLCJhbGciOiJFUzM4NCIsImtpZCI6ImRmMDYyMjgyLTE4OGUtNDdmYi1hNjc1LThiYjllYWNhMzc3NCJ9.eyJzdWIiOiIiLCJzaWduaW5UeXBlIjoiUFVCTElDIiwiaXNzIjoiaHR0cDpcL1wvc2lnbmluLmF3cy5hbWF6b24uY29tXC9zaWduaW4iLCJrZXliYXNlIjoiM3VicGJPWVJNZGZudnRDdUFJM2gxZWh3RHUyekxGV1hiclQxbkZsVG9tYz0iLCJhcm4iOiJhcm46YXdzOmlhbTo6MjUwOTMzNDQwMjc1OnJvb3QiLCJ1c2VybmFtZSI6IkNodXBhcHJlY2lvcyJ9.8AUhpCF6nLxyQMhjCpMGru6ksQMl0saDxBxlR-Y8-YLfBIY6dV9Z3axrpa6BMq0YZnjxnmG9qZeNFr9jhNtBQgyKKZH6DI-LqehhlKoCqC5-QGO22GMPOiTJtoKSkOj0; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Aroot%22%2C%22alias%22%3A%22%22%2C%22username%22%3A%22Chupaprecios%22%2C%22keybase%22%3A%223ubpbOYRMdfnvtCuAI3h1ehwDu2zLFWXbrT1nFlTomc%5Cu003d%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; noflush_awsccs_sid=4c39881f0bc08b2d17ac2775c610d7c0ed438b3fed3fa37c505d621774791a1d; skin=noskin; csm-hit=tb:1PH9T0PSQ0CD3YQ9N0MB+s-5XJZ26TP9WHDSV2T76MA|1716919489752&t:1716919489752&adb:adblk_no; JSESSIONID=F91DDD00A9D5C788271D6B585418CAFD; session-token=kgZvmzb5rgK7J7/bXJI0cpsMxnhG0VTbP3UiAKGmlhuwxa3Q2L+yqRBiCmfoQjVsiiMtUmK11OkfbqcEDsvgAhOWyRejdYq14qNJdkXIJokRW8eK7bGlmwKcbRKNePv8KutGrQ3wx6uzEcLNqzDObqHuUfGvCjXBzXJWL/T3BvdX5a2Yu0O8mJmrqTmLSJOAFXyf9e0SQXYEChKoxYQcFlYYmkUiAj5f/lsm6Uiu5vvh/FeQXMTbGb2oFuGTwOQVq7k6kTg5FAr127uAX0ftKUlTwXK/uNOX5kt8tNdlDa3TJ5DSDXKblDczrpMpYTjZ/1KU2auwaBF3+mCTAufg6aAH0EW/IiDi528rqihnxBKcdFIRYaUkUCjHlZBWrV42';

		$url = 'https://www.amazon.com/s/query?k=' . urlencode($query);

		$result = ChapiAmazonWebContentService::scrape($url, $cookie);

		if (!$result) {
			return [];
		}

		return $this->resultParser->parse($result, self::$vendor, $query);
	}
}
