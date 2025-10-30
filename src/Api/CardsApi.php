<?php

declare(strict_types=1);

namespace Urfysoft\Payme\Api;

use Urfysoft\Payme\Exceptions\PaymeException;
use Urfysoft\Payme\PaymeClient;

readonly class CardsApi
{
    public function __construct(
        private PaymeClient $client
    ) {}

    /**
     * Create a new card token
     *
     * @param  string  $number  Card number
     * @param  string  $expire  Card expiry date (MMYY)
     * @param  bool  $save  Whether to save the card
     * @return array Response with token
     *
     * @throws PaymeException
     */
    public function create(string $number, string $expire, bool $save = true): array
    {
        $params = [
            'card' => [
                'number' => $number,
                'expire' => $expire,
            ],
            'save' => $save,
        ];

        return $this->client->request('cards.create', $params);
    }

    /**
     * Get OTP code for card verification
     *
     * @param  string  $token  Card token
     * @return array Response with sent status
     *
     * @throws PaymeException
     */
    public function getVerifyCode(string $token): array
    {
        $params = [
            'token' => $token,
        ];

        return $this->client->request('cards.get_verify_code', $params);
    }

    /**
     * Verify card with OTP code
     *
     * @param  string  $token  Card token
     * @param  string  $code  OTP code from SMS
     * @return array Response with verification result
     *
     * @throws PaymeException
     */
    public function verify(string $token, string $code): array
    {
        $params = [
            'token' => $token,
            'code' => $code,
        ];

        return $this->client->request('cards.verify', $params);
    }

    /**
     * Check if card token is valid
     *
     * @param  string  $token  Card token
     * @return array Response with card info
     *
     * @throws PaymeException
     */
    public function check(string $token): array
    {
        $params = [
            'token' => $token,
        ];

        return $this->client->request('cards.check', $params);
    }

    /**
     * Remove (deactivate) a card
     *
     * @param  string  $token  Card token
     * @return array Response with removal status
     *
     * @throws PaymeException
     */
    public function remove(string $token): array
    {
        $params = [
            'token' => $token,
        ];

        return $this->client->request('cards.remove', $params);
    }
}
