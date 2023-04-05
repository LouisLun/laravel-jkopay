<?php
namespace Louislun\LaravelJkopay;

use GuzzleHttp\Client;
use LouisLun\LaravelJkopay\Exceptions\JkopayException;

class Jkopay
{
    /**
     * 正式站host
     */
    const API_HOST = 'https://';

    /**
     * 測試環境host
     */
    const SANDBOX_API_HOST = 'https://';

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

    public static function getAuthSignature($secretKey, $queryOrBody)
    {
        return hash_hmac('sha256', $queryOrBody, $secretKey);
    }

    public function client()
    {
        return $this->httpClient;
    }
}