<?php

declare(strict_types=1);

namespace Urfysoft\Payme\Exceptions;

use Exception;
use Throwable;

class PaymeException extends Exception
{
    public ?array $errorData {
        get {
            return $this->errorData;
        }
    }

    public function __construct(
        string $message = '',
        int $code = 0,
        ?array $errorData = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorData = $errorData;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorData['code'] ?? null;
    }

    public function getErrorMessage(): ?string
    {
        return is_array($this->errorData['message'] ?? null)
            ? ($this->errorData['message']['ru'] ?? $this->errorData['message']['en'] ?? null)
            : ($this->errorData['message'] ?? null);
    }
}
