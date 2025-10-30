<?php

declare(strict_types=1);

namespace Urfysoft\Payme\Api;

use Urfysoft\Payme\Data\ReceiptDetail;
use Urfysoft\Payme\Exceptions\PaymeException;
use Urfysoft\Payme\PaymeClient;

readonly class ReceiptsApi
{
    public function __construct(
        private PaymeClient $client
    ) {}

    /**
     * Create a new receipt
     *
     * @param  int  $amount  Amount in tiyin (1 UZS = 100 tiyin)
     * @param  array  $account  Account parameters (order_id, etc.)
     * @param  ReceiptDetail|null  $detail  Receipt details (items, shipping, etc.)
     * @return array Response with receipt data
     *
     * @throws PaymeException
     */
    public function create(int $amount, array $account, ?ReceiptDetail $detail = null): array
    {
        $params = [
            'amount' => $amount,
            'account' => $account,
        ];

        if ($detail) {
            $params['detail'] = $detail->toArray();
        }

        return $this->client->request('receipts.create', $params);
    }

    /**
     * Pay a receipt using card token
     *
     * @param  string  $id  Receipt ID
     * @param  string  $token  Card token
     * @param  array|null  $payer  Payer information (phone number)
     * @return array Response with payment result
     *
     * @throws PaymeException
     */
    public function pay(string $id, string $token, ?array $payer = null): array
    {
        $params = [
            'id' => $id,
            'token' => $token,
        ];

        if ($payer) {
            $params['payer'] = $payer;
        }

        return $this->client->request('receipts.pay', $params);
    }

    /**
     * Send invoice to user via SMS
     *
     * @param  string  $id  Receipt ID
     * @param  string  $phone  Phone number with country code (998901234567)
     * @return array Response with send status
     *
     * @throws PaymeException
     */
    public function send(string $id, string $phone): array
    {
        $params = [
            'id' => $id,
            'phone' => $phone,
        ];

        return $this->client->request('receipts.send', $params);
    }

    /**
     * Cancel a paid receipt
     *
     * @param  string  $id  Receipt ID
     * @return array Response with cancellation status
     *
     * @throws PaymeException
     */
    public function cancel(string $id): array
    {
        $params = [
            'id' => $id,
        ];

        return $this->client->request('receipts.cancel', $params);
    }

    /**
     * Check receipt status
     *
     * @param  string  $id  Receipt ID
     * @return array Response with receipt status
     *
     * @throws PaymeException
     */
    public function check(string $id): array
    {
        $params = [
            'id' => $id,
        ];

        return $this->client->request('receipts.check', $params);
    }

    /**
     * Get full receipt information
     *
     * @param  string  $id  Receipt ID
     * @return array Response with full receipt data
     *
     * @throws PaymeException
     */
    public function get(string $id): array
    {
        $params = [
            'id' => $id,
        ];

        return $this->client->request('receipts.get', $params);
    }

    /**
     * Get all receipts for a period
     *
     * @param  int  $from  Start timestamp in milliseconds
     * @param  int  $to  End timestamp in milliseconds
     * @param  int  $count  Number of receipts to retrieve
     * @param  int  $offset  Offset for pagination
     * @return array Response with receipts list
     *
     * @throws PaymeException
     */
    public function getAll(int $from, int $to, int $count = 50, int $offset = 0): array
    {
        $params = [
            'from' => $from,
            'to' => $to,
            'count' => $count,
            'offset' => $offset,
        ];

        return $this->client->request('receipts.get_all', $params);
    }
}
