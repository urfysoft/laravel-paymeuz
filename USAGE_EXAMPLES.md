# Paycom (Payme) Subscribe API SDK for Laravel - Примеры использования

## Установка

```bash
composer require urfysoft/laravel-paymeuz
```

## Настройка

Опубликуйте конфигурацию:
```bash
php artisan vendor:publish --tag=paymeuz-config
```

Опубликуйте миграции:
```bash
php artisan vendor:publish --tag=paymeuz-migrations
php artisan migrate
```

Добавьте в `.env`:
```env
URFYSOFT_PAYME_MERCHANT_ID=your_merchant_id
URFYSOFT_PAYME_SECRET_KEY=your_secret_key
URFYSOFT_PAYME_BASE_URL=https://checkout.test.payme.uz/api
URFYSOFT_PAYME_TIMEOUT=30
URFYSOFT_PAYME_CURRENCY=860
URFYSOFT_PAYME_LOGGING_ENABLED=true
URFYSOFT_PAYME_LOGGING_CHANNEL=stack
```

## Использование

### 1. Работа с картами

#### Создание токена карты
```php
use YourVendor\PaymeSdk\Facades\Payme;

try {
    $response = Payme::cards()->create(
        number: '8600495473316478',
        expire: '0399',
        save: true
    );
    
    $token = $response['result']['card']['token'];
    echo "Token: {$token}";
} catch (\YourVendor\PaymeSdk\Exceptions\PaymeException $e) {
    echo "Error: " . $e->getMessage();
}
```

#### Получение кода верификации
```php
$response = Payme::cards()->getVerifyCode($token);
// Код отправлен на телефон держателя карты
```

#### Верификация карты с кодом из СМС
```php
$response = Payme::cards()->verify(
    token: $token,
    code: '123456'
);

if ($response['result']['card']['verify']) {
    echo "Карта верифицирована!";
    // Сохраните токен в базу данных
}
```

#### Проверка карты
```php
$response = Payme::cards()->check($token);
$cardInfo = $response['result']['card'];
echo "Number: {$cardInfo['number']}";
echo "Expire: {$cardInfo['expire']}";
```

#### Удаление карты
```php
$response = Payme::cards()->remove($token);
```

### 2. Работа с чеками (Receipts)

#### Создание простого чека
```php
use YourVendor\PaymeSdk\Facades\Payme;

$amount = Payme::toTiyin(50000); // 50,000 UZS = 5,000,000 tiyin

$response = Payme::receipts()->create(
    amount: $amount,
    account: [
        'order_id' => '12345'
    ]
);

$receiptId = $response['result']['receipt']['_id'];
```

#### Создание детализированного чека
```php
use YourVendor\PaymeSdk\Data\ReceiptDetail;
use YourVendor\PaymeSdk\Facades\Payme;

$detail = new ReceiptDetail();

// Добавляем товары
$detail->addItem(
    title: 'Ноутбук Dell XPS 15',
    price: Payme::toTiyin(12000000), // 12,000,000 UZS
    count: 1,
    code: '00702001001000001',
    units: 241092,
    vatPercent: 15,
    packageCode: '123456',
    discount: Payme::toTiyin(500000) // 500,000 UZS скидка
);

$detail->addItem(
    title: 'Мышь Logitech',
    price: Payme::toTiyin(250000),
    count: 2,
    vatPercent: 15
);

// Добавляем доставку
$detail->setShipping(
    title: 'Доставка по Ташкенту',
    price: Payme::toTiyin(50000)
);

// Добавляем общую скидку
$detail->setDiscount(
    title: 'Скидка 5%',
    price: Payme::toTiyin(100000)
);

$response = Payme::receipts()->create(
    amount: Payme::toTiyin(12050000), // Итоговая сумма
    account: ['order_id' => '67890'],
    detail: $detail
);
```

#### Оплата чека
```php
$response = Payme::receipts()->pay(
    id: $receiptId,
    token: $cardToken,
    payer: [
        'phone' => '998901234567'
    ]
);

if ($response['result']['receipt']['state'] === 4) {
    echo "Оплата успешна!";
}
```

#### Отправка инвойса по СМС
```php
$response = Payme::receipts()->send(
    id: $receiptId,
    phone: '998901234567'
);
```

#### Проверка статуса чека
```php
$response = Payme::receipts()->check($receiptId);
$state = $response['result']['receipt']['state'];

/*
Статусы:
0 - Ожидает оплаты
1 - В процессе оплаты
2 - Оплачен
3 - Ожидает отмены
4 - Завершен (успешно)
50 - Отменен
*/
```

#### Получение полной информации о чеке
```php
$response = Payme::receipts()->get($receiptId);
$receipt = $response['result']['receipt'];

echo "Amount: " . Payme::toUzs($receipt['amount']) . " UZS";
echo "State: {$receipt['state']}";
```

#### Отмена оплаченного чека
```php
$response = Payme::receipts()->cancel($receiptId);
```

#### Получение списка чеков за период
```php
use Carbon\Carbon;
use YourVendor\PaymeSdk\Facades\Payme;

$from = Payme::timestampToMs(Carbon::now()->subDays(7)->timestamp);
$to = Payme::timestampToMs(Carbon::now()->timestamp);

$response = Payme::receipts()->getAll(
    from: $from,
    to: $to,
    count: 50,
    offset: 0
);

foreach ($response['result'] as $receipt) {
    echo "Receipt ID: {$receipt['_id']}, Amount: {$receipt['amount']}\n";
}
```

### 3. Полный пример оплаты

```php
use YourVendor\PaymeSdk\Facades\Payme;
use YourVendor\PaymeSdk\Exceptions\PaymeException;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        try {
            // Шаг 1: Создаем токен карты
            $cardResponse = Payme::cards()->create(
                number: $request->card_number,
                expire: $request->card_expire,
                save: true
            );
            
            $token = $cardResponse['result']['card']['token'];
            
            // Шаг 2: Отправляем код верификации
            Payme::cards()->getVerifyCode($token);
            
            // Возвращаем пользователю форму для ввода кода
            return response()->json([
                'status' => 'verification_required',
                'token' => $token
            ]);
            
        } catch (PaymeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function verifyAndPay(Request $request)
    {
        try {
            $token = $request->token;
            $code = $request->verification_code;
            
            // Шаг 3: Верифицируем карту
            $verifyResponse = Payme::cards()->verify($token, $code);
            
            if (!$verifyResponse['result']['card']['verify']) {
                throw new \Exception('Неверный код верификации');
            }
            
            // Шаг 4: Создаем чек
            $amount = Payme::toTiyin($request->amount);
            $receiptResponse = Payme::receipts()->create(
                amount: $amount,
                account: [
                    'order_id' => $request->order_id
                ]
            );
            
            $receiptId = $receiptResponse['result']['receipt']['_id'];
            
            // Шаг 5: Оплачиваем чек
            $payResponse = Payme::receipts()->pay(
                id: $receiptId,
                token: $token,
                payer: [
                    'phone' => $request->phone
                ]
            );
            
            // Шаг 6: Сохраняем транзакцию в БД
            \DB::table('payme_receipts')->insert([
                'receipt_id' => $receiptId,
                'order_id' => $request->order_id,
                'amount' => $amount,
                'state' => $payResponse['result']['receipt']['state'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return response()->json([
                'status' => 'success',
                'receipt_id' => $receiptId,
                'state' => $payResponse['result']['receipt']['state']
            ]);
            
        } catch (PaymeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 400);
        }
    }
}
```

### 4. Использование моделей Eloquent (опционально)

Создайте модели для работы с данными:

```php
// app/Models/PaymeCard.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymeCard extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'card_number',
        'expire',
        'verified',
        'is_active',
        'verified_at'
    ];
    
    protected $casts = [
        'verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// app/Models/PaymeReceipt.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymeReceipt extends Model
{
    protected $fillable = [
        'receipt_id',
        'order_id',
        'amount',
        'state',
        'create_time',
        'pay_time',
        'cancel_time',
        'account',
        'detail',
        'card'
    ];
    
    protected $casts = [
        'create_time' => 'datetime',
        'pay_time' => 'datetime',
        'cancel_time' => 'datetime',
        'account' => 'array',
        'detail' => 'array',
        'card' => 'array'
    ];
}
```

### 5. Обработка ошибок

```php
use YourVendor\PaymeSdk\Exceptions\PaymeException;

try {
    $response = Payme::receipts()->pay($receiptId, $token);
} catch (PaymeException $e) {
    // Получение информации об ошибке
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode();
    $errorData = $e->errorData;
    
    // Логирование
    \Log::error('Payme error', [
        'message' => $errorMessage,
        'code' => $errorCode,
        'data' => $errorData
    ]);
    
    // Обработка специфичных ошибок
    if ($errorCode === -31050) {
        // Неверный код заказа
    } elseif ($errorCode === -31051) {
        // Недостаточно средств
    }
}
```

### 6. Хелперы

```php
use YourVendor\PaymeSdk\Facades\Payme;

// Конвертация UZS в тийины (1 UZS = 100 tiyin)
$tiyin = Payme::toTiyin(50000); // 5000000

// Конвертация тийинов в UZS
$uzs = Payme::toUzs(5000000); // 50000.0

// Получение текущего timestamp в миллисекундах
$timestamp = Payme::getTimestamp();

// Конвертация timestamp в миллисекунды
$timestampMs = Payme::timestampToMs(time());
```

### 7. Тестирование

Используйте тестовую среду для разработки:

```env
URFYSOFT_PAYME_BASE_URL=https://checkout.test.payme.uz/api
```

Тестовые карты:
- **8600 4954 7331 6478** - Успешная оплата
- **8600 0691 9540 6311** - Недостаточно средств

## Коды состояний чека

- `0` - Ожидает оплаты
- `1` - В процессе оплаты
- `2` - Оплачен (средства заблокированы)
- `3` - Ожидает отмены
- `4` - Успешно завершен (средства списаны)
- `50` - Отменен

## Поддержка

Для получения merchant_id и secret_key обратитесь в техническую поддержку Payme:
- Email: support@payme.uz
- Telegram: @PaymeSupport
- Документация: https://developer.help.payme.uz
