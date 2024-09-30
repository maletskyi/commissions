<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Exceptions\CurrenciesCanNotBeLoadedException;
use App\Services\CurrencyRateProvider\CurrencyRateProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CurrencyRateProviderTest extends TestCase
{
    public function testUserCanGetCurrencies(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn(
            '{"success": true, "rates": { "AED": 3.939815, "AFN": 97.607784, "ALL": 116.432049 }}'
        );

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);

        $client->method('sendRequest')->willReturn($response);

        $provider = new CurrencyRateProvider($client, '');

        $rates = $provider->getAllRates();

        self::assertEquals([
            'AED' => 3.939815,
            'AFN' => 97.607784,
            'ALL' => 116.432049,
        ], $rates);
    }

    public function testUserCanGetCurrenciesFromCache(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn(
            '{"success": true, "rates": { "AED": 3.939815, "AFN": 97.607784, "ALL": 116.432049 }}'
        );

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);

        $client->expects(self::once())->method('sendRequest')->willReturn($response);

        $provider = new CurrencyRateProvider($client, '');

        $provider->getAllRates();
        $provider->getAllRates();

        $rates = $provider->getAllRates();

        self::assertEquals([
            'AED' => 3.939815,
            'AFN' => 97.607784,
            'ALL' => 116.432049,
        ], $rates);
    }

    public function testUserGetExceptionIfResponseCode200(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);

        $this->expectException(CurrenciesCanNotBeLoadedException::class);

        (new CurrencyRateProvider($client, ''))->getAllRates();
    }

    public function testUserGetExceptionIfResponseNotSuccess(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn(
            '{"success": false, "rates": { "AED": 3.939815, "AFN": 97.607784, "ALL": 116.432049 }}'
        );

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);

        $client->method('sendRequest')->willReturn($response);

        $this->expectException(CurrenciesCanNotBeLoadedException::class);

        (new CurrencyRateProvider($client, ''))->getAllRates();
    }
}
