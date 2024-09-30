<?php

declare(strict_types=1);

namespace App\Services\CountryCodeProvider;

use App\Exceptions\CountryCodeCanNotBeLoadedException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Client\ClientInterface;

readonly class CountryCodeProvider implements CountryCodeProviderInterface
{
    private const string GET_BIN_DATA_URL = 'https://lookup.binlist.net/';

    public function __construct(private ClientInterface $client)
    {
    }

    public function getCountryCodeByBin(int $bin): string
    {
        $request = new Request('GET', self::GET_BIN_DATA_URL . $bin);
        $request->withHeader('Accept-Version', 3);

        sleep(1);
        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (isset($body['country']['alpha2'])) {
                return $body['country']['alpha2'];
            }
        }

        throw new CountryCodeCanNotBeLoadedException('Couldn\'t get country code for BIN ' . $bin);
    }
}
