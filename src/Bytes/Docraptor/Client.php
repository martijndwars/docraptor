<?php

namespace Bytes\Docraptor;

use Bytes\Docraptor\Http\ClientInterface as HttpClientInterface;
use Bytes\Docraptor\Document\DocumentInterface;

/**
 * Docraptor client
 */
class Client implements ClientInterface
{
    /**
     * An HTTP client
     *
     * @var ClientInterface
     */
    private $_httpClient;

    /**
     * Docraptor API key
     *
     * @var string
     */
    private $_apiKey;

    /**
     * Flag indicating testmode
     *
     * @var boolean
     */
    private $_testMode;


    /**
     * Create new client
     */
    public function __construct(HttpClientInterface $httpClient, $apiKey)
    {
        $this->setHttpClient($httpClient);
        $this->setApiKey($apiKey);
        $this->setTestMode(false);

        $httpClient
            ->setBaseUrl('https://docraptor.com')
            ->setDefaultHeaders(array(
                'Authorization' => 'Basic '.base64_encode($apiKey.':'),
                'User-Agent' => 'Bytes Docraptor client',
            ))
        ;
    }

    /**
     * Set HTTP client
     *
     * @param HttpClientInterface $httpClient
     * @return Client
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }

    /**
     * Get HTTP client
     *
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * Set API key
     *
     * @param string $apiKey Docraptor API key
     * @return Client
     */
    public function setApiKey($apiKey)
    {
        if (!is_string($apiKey)) {
            throw new \InvalidArgumentException('The API key must be a string, '.gettype($apiKey).' given.');
        }

        if (empty($apiKey)) {
            throw new \InvalidArgumentException('The API key may not be empty.');
        }

        $this->_apiKey = $apiKey;

        return $this;
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Set test mode
     *
     * @param boolean $testMode Flag indicating the test mode
     * @return Client
     */
    public function setTestMode($testMode)
    {
        if (!is_bool($testMode)) {
            throw new \InvalidArgumentException('The testmode must be a boolean, '.gettype($testMode).' given.');
        }

        $this->_testMode = $testMode;

        return $this;
    }

    /**
     * Get test mode
     *
     * @return boolean
     */
    public function getTestMode()
    {
        return $this->_testMode;
    }

    /**
     * Convert document using docraptor API
     *
     * @param DocumentInterface $document The document to be converted
     * @return string The binary contents of the result after conversion
     */
    public function convert(DocumentInterface $document)
    {
        // Merge parameters
        $params = $this->_buildParameters($document->getParameters());

        // Create request
        $request = $this->getHttpClient()->post('/docs/', null, $params);

        // Perform request
        $response = $request->send();

        // Throw exception based on status code
        if ($response->getStatusCode() != 200) {
            switch ($response->getStatusCode()) {
                case 400:
                    throw new Exception\BadRequestException();
                case 401:
                    throw new Exception\UnauthorizedException();
                case 403:
                    throw new Exception\ForbiddenException();
                case 422:
                    throw new Exception\UnprocessableEntityException();
                default:
                    throw new Exception\UnexpectedValueException($response->getStatusCode());
            }
        }

        return $response->getBody(true);
    }

    /**
     * Build a complete parameter list by merging document-specific parameters
     * with client-specific parameters. Client-specific parameters are given
     * precedence over document-specific ones.
     *
     * @param array $parameters Document-specific parameters
     * @return array Merged parameters, giving precedence to client-parameters
     */
    private function _buildParameters($parameters)
    {
        return array_replace_recursive($parameters, array(
            'doc' => array(
                'test' => var_export($this->getTestMode(), true)
            )
        ));
    }
}
