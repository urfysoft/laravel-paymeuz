<?php

declare(strict_types=1);

namespace Urfysoft\Payme;

use Urfysoft\Payme\Api\CardsApi;
use Urfysoft\Payme\Api\ReceiptsApi;

class PaymeSdk
{
    private PaymeClient $client;

    private CardsApi $cards;

    private ReceiptsApi $receipts;

    public function __construct(
        string $merchantId,
        string $secretKey,
        string $baseUrl,
        int $timeout = 30,
        bool $loggingEnabled = true,
        ?string $loggingChannel = null
    ) {
        $this->client = new PaymeClient(
            $merchantId,
            $secretKey,
            $baseUrl,
            $timeout,
            $loggingEnabled,
            $loggingChannel
        );

        $this->cards = new CardsApi($this->client);
        $this->receipts = new ReceiptsApi($this->client);
    }

    /**
     * Get Cards API instance
     */
    public function cards(): CardsApi
    {
        return $this->cards;
    }

    /**
     * Get Receipts API instance
     */
    public function receipts(): ReceiptsApi
    {
        return $this->receipts;
    }

    /**
     * Helper: Convert UZS to tiyin
     */
    public static function toTiyin(float $uzs): int
    {
        return (int) round($uzs * 100);
    }

    /**
     * Helper: Convert tiyin to UZS
     */
    public static function toUzs(int $tiyin): float
    {
        return $tiyin / 100;
    }

    /**
     * Helper: Get current timestamp in milliseconds
     */
    public static function getTimestamp(): int
    {
        return (int) (microtime(true) * 1000);
    }

    /**
     * Helper: Convert timestamp to milliseconds
     */
    public static function timestampToMs(int $timestamp): int
    {
        return $timestamp * 1000;
    }
}
