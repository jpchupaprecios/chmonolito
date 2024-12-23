<?php

declare(strict_types=1);

namespace App\Services\Chapi\Homedepot;

use App\Services\WebContentService;

final class ChapiHomedepotWebContentService extends WebContentService
{
	protected static string $seed;

	public static function scrape($url, $cookie = null, $clean = true)
	{
		$proxyHost = env('OXYLABS_PROXY');
		$proxyPort = env('OXULABS_PORT');
		$proxyUser = env('OXYLABS_USER_MX');
		$proxyPass = env('OXYLABS_PASS');
        $useProxy = env('USE_PROXY');

        $curl = curl_init();

        $headers = [
            'User-Agent: PostmanRuntime/7.28.4',
            'Accept: */*',
            'Postman-Token: 4caa6ec5-4949-45d1-9c3b-aec427ca0f25',
            'Host: www.homedepot.com',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Cookie: HD_DC=origin; _abck=C5DA6416425EE6CB20F9F985CC616B2D~-1~YAAQifkpF61vjy6RAQAACdUAOAyHtS/XU2oO8yWEjCBaXQu8INTt7w07xCZxAFNUeNXvH8EP14nX3syN/h1U4Fz3aXhLHMuW/pywAeyziXNq/02l+QGv90TyrjJ6AZlVIOqrNcT09CFEIiQLcu1CkBFTluorBYxZUtfoCad2iupYuNSYqlmpTXIQJJmKL4M8KXfDEc15h9AOj/PftmexFEPRowOdp0GMZXoRQwqiaoDQ2TfsIkRRMMHQj+JNtLcMPeqNkTiH5I8GooBhyKtsP2HiJP7/8hntq50kohJKTokHIaU4vDbUS/CwfUQw2I4orghDx7UjkFAxUCwMbamh/QbpB7ePs2Sk8sYnd9i/Frvc/zNYfrKA1r9x40w9sZYt0ulS1dPsLyizEg==~-1~-1~-1; ak_bmsc=A4BD0C752EB41A6EC33BC000710E16E1~000000000000000000000000000000~YAAQifkpF65vjy6RAQAACdUAOBg484x1tq3A9m3nJaVhBXzM7d03OHbXg8YYztxbvBh0Um78mnYqZnm6C9lwA6NjLa4ENkcJGloNffZzxbKhDO/X3qv73IKk6NmO5Sxw+XzoMDeQYkkWzmqccCcZQ+RDOVYlvHW0qYQd6jaeDn7/D79dKvmCWnDSMlUaW40fZz3Y1t2+nAufv/xUVlzTRNExE1ORofyjjQ6Fn0111H4NRk2ZbxJ6FArmpouEVnyJ84twVMfYz9VqigOYyWW7GwTwLcfgX8qMUQJZcSk4coimRaBF6fHe7oqUTWsQUHI8xeZ87u9FuHMcsjUsSAiXk1lb2l+3LpqfjhyU/A4SYYucS/xSsCGRYURWvw+nMdId; bm_mi=74096F694A0D904FEBBF743C1EC310CF~YAAQhPkpF5v6FDWRAQAAw4VEOBik+Ja4hbOBFv27ogUH4ar8qzXJ8y1QU1rC1oDg2Tfo53DocXoWqWavLUuYLav/vjEv1nlwpWVxJJcSGdwfs0wfOdIMaO+U7TmYqTRUT2EeZ5xcf3kfEbPdsqZuR4IZRp2j0Wmd1qfOla5Nz39urEZeG453j7kzVcLwllVSU8WKzrI1aHJMsOQtF1uAXSgPDdajHLgRd/AdxC1I+yStXiFXKIrSVFi2bQqv53cPbr4tMCtv6NPobg4dMQqqQfkLqOdW8b5XgP1As6jXwnSU1CfgODDLV9gPEMgZ4fZN26uDa2Qww5cIpZYFm/f7ERMHZk89hKKlMovUusIxKPq7C8A13P9YMcGfeQp/GOOocXMaywxXmTr/2zIK68oyEw==~1; bm_sz=9CD41D8D026D3303356306BA6FB77337~YAAQifkpF7Bvjy6RAQAACdUAOBhkI2bMLqNUjd6l0uXVrzMG5FzZQu3jRhWqsdiJTw8FVBjtBNPDmqnzW2efVd5yrw19IB6UO1vY1nz98deOB8+b3QB9LlJMoOwKro84Tiy7BDSlPmQZdKu8DTsQwRqaohIrPTEZiro966lIKZGfRYuOawxeVaSBRjYSN7yjx8VWaQ9wlWDLuMECcL5VsXZZW9ctajxLbXq0+IsGjkOHKEUPp9IKFhZVsmlgDAjJBrZYr/Y08k0zKd+tmXAs9XNV9PBDQ2WdBnCtyc82Zzx0qJVUMotgY+hoH+M9ydxL1yyhxFmia5FY5RQ612dbt++XYTzUZfI3jHUINm8gVLwn~3551536~4272947; akacd_usbeta=3900675615~rv=51~id=641a9e4239ebfd8ccf95d779ba044dfd; akavpau_prod=1723226200~id=4986f65f3da843c839631f737f0586bb',
        ];

        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($useProxy) {
            $opts[CURLOPT_PROXY] = $proxyHost . ':' . $proxyPort;
            $opts[CURLOPT_PROXYUSERPWD] = $proxyUser . ':' . $proxyPass;
        }

        curl_setopt_array($curl, $opts);

        $response = curl_exec($curl);

        curl_close($curl);

        if($response){
            $body = preg_replace('/\s\s+/', '', $response);
            $body = preg_replace('/\n/', '', $body);

            if ($body) {
                return $body;
            }
        }

        return false;
	}

	public static function getVariants($parentId)
	{
		$url = 'https://www.homedepot.com/federation-gateway/graphql?opname=metadata';

		$headers = [
			'Connection: keep-alive',
			'Accept: */*',
			'Content-Language: es-US',
			'User-Agent: ' . self::getUserAgent(),
		];

		$proxyHost = env('OXYLABS_PROXY');
		$proxyPort = env('OXULABS_PORT');
		$proxyUser = env('OXYLABS_USER_MX');
		$proxyPass = env('OXYLABS_PASS');

		$data = [
			'operationName' => 'metadata',
			'variables' => [
				'parentId' => $parentId,
			],
			'query' => 'query metadata($parentId: String!) {
      metadata(parentId: $parentId) {
        attributes {
          attributeValues {
            value
            swatchGuid
            __typename
          }
          attributeName
          isSwatch
          __typename
        }
        childItemsLookup {
          itemId
          attributeCombination
          canonicalUrl
          isItemBackOrdered
          __typename
        }
        sampleProductDetail {
          itemId
          sampleId
          __typename
        }
        sizeAndFitDetail {
          attributeGroups {
            attributes {
              attributeName
              dimensions
              __typename
            }
            dimensionLabel
            productType
            __typename
          }
          __typename
        }
        superDuperSku {
          attributes {
            attributeName
            attributeValues {
              selected
              superSkuUrl
              value
              __typename
            }
            __typename
          }
          __typename
        }
        __typename
      }
    }',
		];
        $useProxy = env('USE_PROXY');
		$proxyHost = env('OXYLABS_PROXY');
		$proxyPort = env('OXULABS_PORT');
		$proxyUser = env('OXYLABS_USER_MX');
		$proxyPass = env('OXYLABS_PASS');

		$headers = ['x-oxylabs-render: html'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);

        if($useProxy){
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost . ':' . $proxyPort);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);
        }

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		$decoded = json_decode('{}', true);
        curl_close($ch);

        if ($response) {
            try {
                $decoded = @json_decode($response, true);

                if(!$decoded){
                    $response = @gzdecode($response);
                    $decoded = @json_decode($response, true);
                }
            } catch (Exception $e) {
                return $decoded;
            }
		}

        if(isset($decoded["error"]) && $decoded["error"]){
            return json_decode('{}', true);
        }

		return $decoded;
	}
}
