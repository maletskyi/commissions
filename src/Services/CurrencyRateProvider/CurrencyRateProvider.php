<?php

declare(strict_types=1);

namespace App\Services\CurrencyRateProvider;

use App\Exceptions\CurrenciesCanNotBeLoadedException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class CurrencyRateProvider implements CurrencyRateProviderInterface
{
    private array $simpleCache = [];

    private const string RATES_URL = 'https://api.apilayer.com/exchangerates_data/latest';

    public function __construct(private readonly ClientInterface $client, private readonly string $apiKey)
    {
    }

    public function getAllRates(): array
    {
        if (isset($this->simpleCache['rates'])) {
            return $this->simpleCache['rates'];
        }

        $request = new Request('GET', self::RATES_URL, ['apikey' => $this->apiKey]);

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if ($body['success'] === true) {
                return $this->simpleCache['rates'] = $body['rates'];
            }
        }

        throw new CurrenciesCanNotBeLoadedException('Couldn\'t load currencies');
    }
}
