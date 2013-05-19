<?php

namespace Bytes\Docraptor\Tests\Docraptor;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Bytes\Docraptor\Http\ClientInterface;
use Mockery;
use Bytes\Docraptor\Client;
use Bytes\Docraptor\Document\PdfDocument;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the object gets initialized correctly
     */
    public function testInitialization()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');

        $this->assertInstanceOf('Bytes\Docraptor\Client', $client);
    }

    /**
     * Test that a correct HTTP client object gets passed
     */
    public function testHttpClient()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');

        $this->assertInstanceOf('Bytes\Docraptor\Http\ClientInterface', $client->getHttpClient());
    }

    /**
     * Test intializing without a valid (nonzero) api-key
     *
     * @expectedException InvalidArgumentException
     */
    public function testInitializationWithoutApiKey()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '');
    }

    /**
     * Test that the API key gets set correctly
     */
    public function testSettingValidApiKey()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');

        $this->assertEquals('4pik3y', $client->getApiKey());

        $client->setApiKey('y3kip4');

        $this->assertEquals('y3kip4', $client->getApiKey());
    }

    /**
     * Test setting an invalid API key (i.e. non-string)
     *
     * @expectedException InvalidArgumentException
     */
    public function testSettingInvalidApiKey()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');
        $client->setApiKey(012345);
    }

    /**
     * Assert that by default the test mode is enabled
     */
    public function testDefaultTestmode()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');

        $this->assertEquals(false, $client->getTestMode());
    }

    /**
     * Test setting the test mode to a valid value
     */
    public function testSettingValidTestMode()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');

        $this->assertEquals(false, $client->getTestMode());

        $client->setTestMode(true);

        $this->assertEquals(true, $client->getTestMode());
    }

    /**
     * Test setting an invalid testmode (i.e. non-boolean)
     *
     * @expectedException InvalidArgumentException
     */
    public function testSettingInvalidTestMode()
    {
        $httpClient = $this->_getHttpClientMock(null);

        $client = new Client($httpClient, '4pik3y');
        $client->setTestMode('true');
    }

    /**
     * Test interactiosn between docraptor client and http client
     */
    public function testSendDocument()
    {
        $httpClientMock = $this->_getHttpClientMock(
            $this->_getRequestMock(
                $this->_getResponseMock()
            )
        );

        $client = new Client($httpClientMock, '4pik3y');

        $documentMock = $this->_getDocumentMock();

        $this->assertEquals('SUCCESS', $client->convert($documentMock));
    }

    /**
     * Data provider for testing exceptions
     */
    public static function exceptionProvider()
    {
        return array(
            array(400, 'Bytes\Docraptor\Exception\BadRequestException'),
            array(401, 'Bytes\Docraptor\Exception\UnauthorizedException'),
            array(403, 'Bytes\Docraptor\Exception\ForbiddenException'),
            array(422, 'Bytes\Docraptor\Exception\UnprocessableEntityException'),
            array(500, 'Bytes\Docraptor\Exception\UnexpectedValueException'),
        );
    }

    /**
     * Test exceptions
     *
     * @dataProvider exceptionProvider
     */
    public function testInvalidResponse($responseCode, $expectedException)
    {
        $this->setExpectedException($expectedException);

        $httpClientMock = $this->_getHttpClientMock(
            $this->_getRequestMock(
                $this->_getInvalidResponseMock($responseCode)
            )
        );

        $client = new Client($httpClientMock, '4pik3y');
        $client->convert($this->_getDocumentMock());
    }

    /**
     * Creates a mock for the Bytes\Docraptor\Document\AbstractDocument class
     */
    private function _getDocumentMock()
    {
        $documentMock = Mockery::mock('Bytes\Docraptor\Document\AbstractDocument');
        $documentMock
            ->shouldReceive('getParameters')->once()
            ->andReturn(array(
                'name' => 'Mock document',
            ))
        ;

        return $documentMock;
    }

    /**
     * Creates a mock for the Guzzle\Http\Message\Response class
     */
    private function _getInvalidResponseMock($returnValue)
    {
        $responseMock = Mockery::mock('Guzzle\Http\Message\Response');
        $responseMock
            ->shouldReceive('getStatusCode')->once()
            ->andReturn($returnValue)
        ;

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn('ERROR')
        ;

        return $responseMock;
    }

    /**
     * Creates a mock for the Guzzle\Http\Message\Response class
     */
    private function _getResponseMock()
    {
        $responseMock = Mockery::mock('Guzzle\Http\Message\Response');
        $responseMock
            ->shouldReceive('getStatusCode')->once()
            ->andReturn(200)
        ;

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn('SUCCESS')
        ;

        return $responseMock;
    }

    /**
     * Creates a mock for the Guzzle\Http\Message\Request class
     */
    private function _getRequestMock($responseMock)
    {
        $requestMock = Mockery::mock('Guzzle\Http\Message\Request');
        $requestMock
            ->shouldReceive('send')->once()
            ->andReturn($responseMock)
        ;

        return $requestMock;
    }

    /**
     * Creates a mock for the Bytes\Docraptor\Http\ClientInterface class
     */
    private function _getHttpClientMock($requestMock)
    {
        $httpClientMock = Mockery::mock('Bytes\Docraptor\Http\ClientInterface');
        $httpClientMock
            ->shouldReceive('post')->once()
            ->andReturn($requestMock)
        ;

        $httpClientMock
            ->shouldReceive('setBaseUrl')->once()
            ->andReturn($httpClientMock)
        ;

        $httpClientMock
            ->shouldReceive('setDefaultHeaders')->once()
            ->andReturn($httpClientMock)
        ;

        return $httpClientMock;
    }
}