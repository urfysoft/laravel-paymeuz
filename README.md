# Paycom (Payme) Subscribe API SDK for Laravel

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%5E12.0-red)](https://laravel.com/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Полнофункциональный SDK для интеграции с платежной системой Payme (Payme) Subscribe API для Laravel 12 и PHP 8.4.

## Возможности

- ✅ Полная поддержка Payme Subscribe API
- ✅ Работа с картами (создание токенов, верификация, удаление)
- ✅ Управление чеками (создание, оплата, отмена, проверка статуса)
- ✅ Детализированные чеки с товарами, доставкой и скидками
- ✅ Автоматическая обработка ошибок
- ✅ Логирование запросов и ответов
- ✅ Type-safe API с PHP 8.4
- ✅ Миграции для PostgreSQL 17
- ✅ Laravel Facade для удобного использования
- ✅ Полная документация и примеры

## Требования

- PHP ^8.4
- Laravel ^12.0
- PostgreSQL ^17.0
- GuzzleHTTP ^7.0

## Установка

### 1. Установите пакет через Composer:

```bash
composer require urfysoft/laravel-paymeuz
```

### 2. Опубликуйте конфигурацию:

```bash
php artisan vendor:publish --tag=paymeuz-config
```

### 3. Опубликуйте и запустите миграции:

```bash
php artisan vendor:publish --tag=paymeuz-migrations
php artisan migrate
```

### 4. Настройте переменные окружения в `.env`:

```env
URFYSOFT_PAYME_MERCHANT_ID=your_merchant_id
URFYSOFT_PAYME_SECRET_KEY=your_secret_key
URFYSOFT_PAYME_BASE_URL=https://checkout.test.payme.uz/api
URFYSOFT_PAYME_TIMEOUT=30
URFYSOFT_PAYME_CURRENCY=860
URFYSOFT_PAYME_LOGGING_ENABLED=true
```

## Быстрый старт

### Работа с картами

```php
use YourVendor\PaymeSdk\Facades\Payme;

// Создание токена карты
$response = Payme::cards()->create(
    number: '8600495473316478',
    expire: '0399',
    save: true
);

$token = $response['result']['card']['token'];

// Отправка кода верификации
Payme::cards()->getVerifyCode($token);

// Верификация карты
$response = Payme::cards()->verify($token, '123456');

// Проверка карты
$response = Payme::cards()->check($token);

// Удаление карты
Payme::cards()->remove($token);
```

### Работа с чеками

```php
use YourVendor\PaymeSdk\Facades\Payme;
use YourVendor\PaymeSdk\Data\ReceiptDetail;

// Простой чек
$amount = Payme::toTiyin(50000); // 50,000 UZS
$response = Payme::receipts()->create(
    amount: $amount,
    account: ['order_id' => '12345']
);

$receiptId = $response['result']['receipt']['_id'];

// Детализированный чек
$detail = new ReceiptDetail();
$detail->addItem(
    title: 'Товар 1',
    price: Payme::toTiyin(100000),
    count: 2,
    vatPercent: 15
);
$detail->setShipping('Доставка', Payme::toTiyin(10000));

$response = Payme::receipts()->create(
    amount: Payme::toTiyin(210000),
    account: ['order_id' => '12345'],
    detail: $detail
);

// Оплата чека
$response = Payme::receipts()->pay(
    id: $receiptId,
    token: $cardToken,
    payer: ['phone' => '998901234567']
);

// Проверка статуса
$response = Payme::receipts()->check($receiptId);

// Отмена чека
Payme::receipts()->cancel($receiptId);
```

## Структура проекта

```
├── src/
│   ├── Api/
│   │   ├── CardsApi.php          # API для работы с картами
│   │   └── ReceiptsApi.php       # API для работы с чеками
│   ├── Data/
│   │   └── ReceiptDetail.php     # DTO для деталей чека
│   ├── Exceptions/
│   │   └── PaymeException.php   # Исключения
│   ├── Facades/
│   │   └── Payme.php            # Laravel Facade
│   ├── PaymeClient.php          # HTTP клиент
│   ├── PaymeSdk.php             # Главный класс SDK
│   └── PaymeServiceProvider.php # Service Provider
├── config/
│   └── payme.php                # Конфигурация
├── database/
│   └── migrations/               # Миграции БД
├── tests/                        # Тесты
└── README.md
```

## API методы

### Cards API

| Метод             | Описание                       |
|-------------------|--------------------------------|
| `create()`        | Создание токена карты          |
| `getVerifyCode()` | Получение кода верификации     |
| `verify()`        | Верификация карты кодом из СМС |
| `check()`         | Проверка карты                 |
| `remove()`        | Удаление карты                 |

### Receipts API

| Метод      | Описание                    |
|------------|-----------------------------|
| `create()` | Создание чека               |
| `pay()`    | Оплата чека                 |
| `send()`   | Отправка инвойса по СМС     |
| `cancel()` | Отмена чека                 |
| `check()`  | Проверка статуса            |
| `get()`    | Получение информации о чеке |
| `getAll()` | Получение списка чеков      |

## Обработка ошибок

```php
use YourVendor\PaymeSdk\Exceptions\PaymeException;

try {
    $response = Payme::receipts()->pay($receiptId, $token);
} catch (PaymeException $e) {
    $message = $e->getMessage();      // Текст ошибки
    $code = $e->getCode();            // Код ошибки
    $data = $e->errorData;       // Дополнительные данные
    
    Log::error('Payme error', [
        'message' => $message,
        'code' => $code,
        'data' => $data
    ]);
}
```

## Хелперы

```php
// Конвертация валют
$tiyin = Payme::toTiyin(50000);        // UZS → tiyin
$uzs = Payme::toUzs(5000000);          // tiyin → UZS

// Работа с timestamp
$timestamp = Payme::getTimestamp();     // Текущий timestamp в мс
$timestampMs = Payme::timestampToMs(time()); // Конвертация в мс
```

## Тестирование

Для тестирования используйте тестовую среду:

```env
URFYSOFT_PAYME_BASE_URL=https://checkout.test.payme.uz/api
```

**Тестовые карты:**

- `8600 4954 7331 6478` - Успешная оплата
- `8600 0691 9540 6311` - Недостаточно средств

## Логирование

SDK автоматически логирует все запросы и ответы. Настройте в `config/payme.php`:

```php
'logging' => [
    'enabled' => true,
    'channel' => 'stack', // Любой канал из config/logging.php
],
```

## Состояния чека

| Код | Описание                         |
|-----|----------------------------------|
| 0   | Ожидает оплаты                   |
| 1   | В процессе оплаты                |
| 2   | Оплачен (средства заблокированы) |
| 3   | Ожидает отмены                   |
| 4   | Успешно завершен                 |
| 50  | Отменен                          |

## Документация

Полная документация доступна в файле [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)

Официальная документация Payme: https://developer.help.payme.uz

## Поддержка

Для получения `merchant_id` и `secret_key` обратитесь в Payme:

- **Email:** support@payme.uz
- **Telegram:** @PaymeSupport
- **Сайт:** https://payme.uz

## Лицензия

MIT License. См. [LICENSE](LICENSE) для подробностей.

## Авторы

Создано для Laravel 12 и PHP 8.4 с поддержкой PostgreSQL 17.

## Contributing

Pull requests приветствуются! Для больших изменений сначала откройте issue.
