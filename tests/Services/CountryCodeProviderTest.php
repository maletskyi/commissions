<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Exceptions\CountryCodeCanNotBeLoadedException;
use App\Services\CountryCodeProvider\CountryCodeProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CountryCodeProviderTest extends TestCase
{
    public function testUserCanGetCurrencies(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn('{"country": {"alpha2": "AFN"}}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);

        $client->method('sendRequest')->willReturn($response);

        $provider = new CountryCodeProvider($client);

        self::assertEquals('AFN', $provider->getCountryCodeByBin(123));
    }

    public function testUserGetExceptionIfResponseCode200(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);

        $this->expectException(CountryCodeCanNotBeLoadedException::class);

        (new CountryCodeProvider($client))->getCountryCodeByBin(123);
    }

    public function testUserGetExceptionIfResponseFormatDifferent(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn('{"country": {"alpha3": "AFN"}}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);

        $this->expectException(CountryCodeCanNotBeLoadedException::class);

        (new CountryCodeProvider($client))->getCountryCodeByBin(123);
    }
}
