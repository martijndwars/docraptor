<?php

namespace Bytes\Docraptor;

use Bytes\Docraptor\Http\ClientInterface as HttpClientInterface;
use Bytes\Docraptor\Document\DocumentInterface;

/**
 * DocRaptor client
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
     * DocRaptor API key
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
     * Flag indicating mode of asynchronous job
     *
     * @var boolean
     */
    private $_asyncMode;

    /**
     * The optional callback url if querying as asynchronous job
     *
     * @var string
     */
    private $_callbackUrl;


    /**
     * Create new client
     */
    public function __construct(HttpClientInterface $httpClient, $apiKey)
    {
        $this->setHttpClient($httpClient);
        $this->setApiKey($apiKey);
        $this->setTestMode(false);
        $this->setAsyncMode(false);

        $httpClient
            ->setBaseUrl('https://docraptor.com')
            ->setDefaultHeaders(array(
                'Authorization' => 'Basic '.base64_encode($apiKey.':'),
                'User-Agent' => 'Bytes DocRaptor client',
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
     * @param string $apiKey DocRaptor API key
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
     * Set async job
     *
     * @param boolean $asyncMode Flag indicating the async mode
     * @return Client
     */
    public function setAsyncMode($asyncMode)
    {
        if (!is_bool($asyncMode)) {
            throw new \InvalidArgumentException('The asyncMode must be a boolean, '.gettype($asyncMode).' given.');
        }

        $this->_asyncMode = $asyncMode;

        return $this;
    }

    /**
     * Get test mode
     *
     * @return boolean
     */
    public function getAsyncMode()
    {
        return $this->_asyncMode;
    }

    /**
     * Set callback url
     *
     * @param $callbackUrl
     * @return Client
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->_callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * Returns callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->_callbackUrl;
    }

    /**
     * Convert document using DocRaptor API
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

        //If in async mode the response will be in json
        if ($this->getAsyncMode()) {
            return json_decode($response->getBody(true));
        }

        return $response->getBody(true);
    }

    /**
     * Returns status information for a document which has been converted in async mode.
     *
     * The status id is returned by the convert() method if a document is converted in async mode.
     *
     * @param string $statusId
     * @return array
     */
    public function getStatus($statusId)
    {
        $request = $this->getHttpClient()->get('/status/'.$statusId);
        $response = $request->send();

        if ($response->getStatusCode() != 200) {
            switch ($response->getStatusCode()) {
                case 400:
                    throw new Exception\BadRequestException();
                case 401:
                    throw new Exception\UnauthorizedException();
                case 403:
                    throw new Exception\ForbiddenException();
                default:
                    throw new Exception\UnexpectedValueException($response->getStatusCode());
            }
        }

        return json_decode($response->getBody(true));
    }

    /**
     * Build a complete parameter list by merging document-specific parameters
     * with client-specific parameters. Client-specific parameters are given
     * precedence over document-specific ones.
     *
     * @param array $parameters Document-specific parameters
     * @return array Merged parameters, giving precedence to client-parameters
     */
    private function _buildParameters($docParameters)
    {
        $clientParameters = array(
            'doc' => array(
                'test'         => var_export($this->getTestMode(),  true),
                'async'        => var_export($this->getAsyncMode(), true),
            )
        );

        if ($this->getAsyncMode() && null !== $this->getCallbackUrl()) {
            $clientParameters['doc']['callback_url'] = $this->getCallbackUrl();
        }

        return array_replace_recursive($docParameters, $clientParameters);
    }
}
