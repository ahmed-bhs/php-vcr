<?php

declare(strict_types=1);

namespace VCR\Tests\Integration\Symfony;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Tests behaviour when an error occurs.
 */
class ErrorTest extends TestCase
{
    public const TEST_GET_URL = 'http://localhost:9959';

    protected function setUp(): void
    {
        vfsStream::setup('testDir');
        \VCR\VCR::configure()->setCassettePath(vfsStream::url('testDir'));
    }

    public function testConnectionExceptionHandling(): void
    {
        $this->expectException(TransportExceptionInterface::class);
        $client = HttpClient::create();
        $response = $client->request('GET', self::TEST_GET_URL);
        $response->getHeaders();
    }

    public function testConnectionWithPhpVcrExceptionHandling(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unexpected error: Unable to retrieve cURL information from response or error logs.');
        \VCR\VCR::turnOn();
        \VCR\VCR::insertCassette('test-cassette.yml');
        $client = HttpClient::create();
        $response = $client->request('GET', self::TEST_GET_URL);
        $response->getHeaders();

        $this->assertEmpty($response->getContent(false));

        \VCR\VCR::turnOff();
        \VCR\VCR::turnOff();
    }
}
