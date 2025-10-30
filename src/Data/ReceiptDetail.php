<?php

declare(strict_types=1);

namespace Urfysoft\Payme\Data;

class ReceiptDetail
{
    private int $receiptType = 0;

    private array $items = [];

    private ?array $shipping = null;

    private ?array $discount = null;

    /**
     * Set receipt type
     * 0 - Fiscal receipt
     */
    public function setReceiptType(int $type): self
    {
        $this->receiptType = $type;

        return $this;
    }

    /**
     * Add item to receipt
     *
     * @param  string  $title  Item name
     * @param  int  $price  Price per item in tiyin
     * @param  int  $count  Quantity
     * @param  string|null  $code  Product code (ИКПУ)
     * @param  int|null  $units  Unit code
     * @param  int|null  $vatPercent  VAT percentage (0, 12, 15)
     * @param  string|null  $packageCode  Package code
     * @param  int  $discount  Discount amount in tiyin
     * @return $this
     */
    public function addItem(
        string $title,
        int $price,
        int $count,
        ?string $code = null,
        ?int $units = null,
        ?int $vatPercent = null,
        ?string $packageCode = null,
        int $discount = 0
    ): self {
        $item = [
            'title' => $title,
            'price' => $price,
            'count' => $count,
            'discount' => $discount,
        ];

        if ($code !== null) {
            $item['code'] = $code;
        }

        if ($units !== null) {
            $item['units'] = $units;
        }

        if ($vatPercent !== null) {
            $item['vat_percent'] = $vatPercent;
        }

        if ($packageCode !== null) {
            $item['package_code'] = $packageCode;
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Set shipping information
     *
     * @param  string  $title  Shipping description
     * @param  int  $price  Shipping cost in tiyin
     * @return $this
     */
    public function setShipping(string $title, int $price): self
    {
        $this->shipping = [
            'title' => $title,
            'price' => $price,
        ];

        return $this;
    }

    /**
     * Set discount information
     *
     * @param  string  $title  Discount description
     * @param  int  $price  Discount amount in tiyin
     * @return $this
     */
    public function setDiscount(string $title, int $price): self
    {
        $this->discount = [
            'title' => $title,
            'price' => $price,
        ];

        return $this;
    }

    /**
     * Convert to array for API request
     */
    public function toArray(): array
    {
        $data = [
            'receipt_type' => $this->receiptType,
            'items' => $this->items,
        ];

        if ($this->shipping !== null) {
            $data['shipping'] = $this->shipping;
        }

        if ($this->discount !== null) {
            $data['discount'] = $this->discount;
        }

        return $data;
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        $detail = new self;

        if (isset($data['receipt_type'])) {
            $detail->setReceiptType($data['receipt_type']);
        }

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $detail->addItem(
                    $item['title'],
                    $item['price'],
                    $item['count'],
                    $item['code'] ?? null,
                    $item['units'] ?? null,
                    $item['vat_percent'] ?? null,
                    $item['package_code'] ?? null,
                    $item['discount'] ?? 0
                );
            }
        }

        if (isset($data['shipping'])) {
            $detail->setShipping($data['shipping']['title'], $data['shipping']['price']);
        }

        if (isset($data['discount'])) {
            $detail->setDiscount($data['discount']['title'], $data['discount']['price']);
        }

        return $detail;
    }
}
