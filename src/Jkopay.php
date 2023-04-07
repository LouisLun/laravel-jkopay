<?php
namespace LouisLun\LaravelJkopay;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use LouisLun\LaravelJkopay\Exceptions\JkopayException;
use LouisLun\LaravelJkopay\Response;

class Jkopay
{
    /**
     * 正式站host
     */
    const API_HOST = 'https://';

    /**
     * 測試環境host
     */
    const SANDBOX_API_HOST = 'https://onlinepay.jkopay.com';

    /**
     * jkospay api uri list
     *
     * @var array
     */
    protected static $apiUris = [
        'request' => '/platform/entry',
        'refund' => '/platform/refund',
        'details' => '/platform/inquiry',
    ];

    /**
     * config
     *
     * @var array
     */
    protected $config;

    /**
     * HTTP Client
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * JKOS SERCERT KEY
     *
     * @var string
     */
    protected $secretKey;

    /**
     * store ID
     *
     * @var string
     */
    protected $storeID;

    /**
     * api key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * constructor
     * @param array $config config
     *  'secretKey' => Your merchant JKOS SERCERT KEY
     *  'apiKey' => Your merchant JKOS API KEY
     *  'storeID' => Your merchant JKOS StoreID
     *  'isSandbox' => sandbox mode
     * @return self
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->storeID = $config['storeID'] ?? null;
        $this->secretKey = $config['secretKey'] ?? null;
        $this->apiKey = $config['apiKey'] ?? null;
        $isSanbox = $config['isSandbox'] ?? false;
        $debug = $config['debug'] ?? false;

        if (!$this->storeID) {
            throw new JkopayException('the storeID is required', 400);
        }

        if (!$this->secretKey) {
            throw new JkopayException('the secretKey is required', 400);
        }

        if (!$this->apiKey) {
            throw new JkopayException('the apiKey is required', 400);
        }

        // Base URI
        $baseUri = ($isSanbox) ? self::SANDBOX_API_HOST : self::API_HOST;

        // Headers
        $headers = [
            'Content-Type' => 'application/JSON',
            'charset' => 'utf8',
        ];

        $this->httpClient = new Client([
            'base_uri' => $baseUri,
            'headers' => $headers,
            'http_errors' => false,
            'debug' => $debug,
        ]);

        return $this;
    }

    /**
     * request API
     *
     * @param array $params
     * @return \LouisLun\LaravelJkopay\Response
     */
    public function request(array $params)
    {
        return $this->requestHandler('POST', $this->getAPIUri('request'), $params, [
            'connect_timeout' => 5,
            'timeout' => 20,
        ]);
    }

    /**
     * refund API
     *
     * @param array $params
     * @return \LouisLun\LaravelJkopay\Response
     */
    public function refund(array $params)
    {
        return $this->requestHandler('POST', $this->getAPIUri('refund'), $params, [
            'connect_timeout' => 5,
            'timeout' => 20,
        ]);
    }

    /**
     * details API
     *
     * @param array $params
     * @return \LouisLun\LaravelJkopay\Response
     */
    public function details(array $params)
    {
        return $this->requestHandler('GET', $this->getAPIUri('details'), $params, [
            'connect_timeout' => 5,
            'timeout' => 20,
        ]);
    }

    /**
     * signature
     *
     * @param string $secretKey JKOS SECRET KEY
     * @param string $queryOrBody request payload
     * @return string
     */
    public static function getAuthSignature($secretKey, $queryOrBody)
    {
        return hash_hmac('sha256', $queryOrBody, $secretKey);
    }

    /**
     * sender
     *
     * @return \GuzzleHttp\Client
     */
    public function client()
    {
        return $this->httpClient;
    }

    /**
     * get api uri
     *
     * @param string $key
     * @return string
     */
    protected function getAPIUri($key)
    {
        return self::$apiUris[$key];
    }

    /**
     * request handler
     *
     * @param string $method method
     * @param string $uri
     * @param array $params
     * @param array $options
     * @return \LouisLun\LaravelJkopay\Response
     */
    public function requestHandler($method, $uri, array $params = [], $options = [])
    {
        if (!isset($params['store_id'])) {
            $params['store_id'] = $this->storeID;
        }

        $headers = [];
        $authParams = '';
        $url = $uri;
        $body = '';
        if ($method == 'GET') {
            $rows = [];
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    $rows[] = $key . '=' . implode(',', $val);
                } else {
                    $rows[] = $key . '=' . $val;
                }
            }
            $authParams = implode('&', $rows);
            $url = "$uri?$authParams";
        } else {
            $authParams = json_encode($params);
            $body = $authParams;
        }

        // set Digest
        $headers['Digest'] = 'sha-256=' . $this->getAuthSignature($this->secretKey, $authParams);

        $stats = null;
        $options['on_stats'] = function (\GuzzleHttp\TransferStats $transferStats) use (&$stats) {
            $stats = $transferStats;
        };

        $request = new Request($method, $url, $headers, $body);
        try {
            $response = $this->client()->send($request, $options);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious(), $e->getHandlerContext());
        }

        return new Response($response, $stats);
    }
}