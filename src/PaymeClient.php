<?php

declare(strict_types=1);

namespace Urfysoft\Payme;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Urfysoft\Payme\Exceptions\PaymeException;

class PaymeClient
{
    private Client $httpClient;

    private string $merchantId;

    private string $secretKey;

    private string $baseUrl;

    private bool $loggingEnabled;

    private ?string $loggingChannel;

    public function __construct(
        string $merchantId,
        string $secretKey,
        string $baseUrl,
        int $timeout = 30,
        bool $loggingEnabled = true,
        ?string $loggingChannel = null
    ) {
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->loggingEnabled = $loggingEnabled;
        $this->loggingChannel = $loggingChannel;

        $this->httpClient = new Client([
            'timeout' => $timeout,
            'verify' => true,
            'http_errors' => false,
        ]);
    }

    /**
     * Make a request to Payme API
     *
     * @throws PaymeException
     */
    public function request(string $method, array $params, ?int $id = null): array
    {
        $id = $id ?? time();

        $payload = [
            'id' => $id,
            'method' => $method,
            'params' => $params,
        ];

        $authHeader = 'X-Auth: '.$this->merchantId.':'.$this->secretKey;

        if ($this->loggingEnabled) {
            $this->log('info', 'Payme Request', [
                'method' => $method,
                'payload' => $payload,
            ]);
        }

        try {
            $response = $this->httpClient->post($this->baseUrl, [
                'headers' => [
                    'X-Auth' => $authHeader,
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache',
                ],
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($this->loggingEnabled) {
                $this->log('info', 'Payme Response', [
                    'status_code' => $statusCode,
                    'response' => $data,
                ]);
            }

            if ($statusCode !== 200) {
                throw new PaymeException(
                    "HTTP Error: $statusCode",
                    $statusCode,
                    $data
                );
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PaymeException(
                    'Invalid JSON response: '.json_last_error_msg(),
                    0,
                    ['raw_response' => $body]
                );
            }

            if (isset($data['error'])) {
                throw new PaymeException(
                    $data['error']['message'] ?? 'Unknown error',
                    $data['error']['code'] ?? 0,
                    $data['error']
                );
            }

            return $data;

        } catch (GuzzleException $e) {
            if ($this->loggingEnabled) {
                $this->log('error', 'Payme HTTP Error', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
            }

            throw new PaymeException(
                'HTTP Request failed: '.$e->getMessage(),
                $e->getCode(),
                null,
                $e
            );
        }
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->loggingChannel) {
            Log::channel($this->loggingChannel)->{$level}($message, $context);
        } else {
            Log::{$level}($message, $context);
        }
    }
}
