<!-- title: Тестовое задание -->
<p>
    <h1 align="center">Тестовое задание Laravel</h1>
    <br>
</p>

> После пуша Laravel в ваш репозиторий, сделайте задание в любой другой ветке и PR в основную.

Задача: подключить 2 платёжных шлюза (приём платежей).

1. В базе должны присутствовать таблицы пользователей и платежей
2. Оба шлюза присылают данные на `callback url` при изменении состояния платежа.
3. Оба шлюза работают только с одной валютой и передают суммы в центах.
4. Создание платежа на стороне шлюза не требуется (нужно сделать только обработку изменения состояний).
5. Для каждого платёжного шлюза нужно реализовать возможность установки лимита, после прохождения которого проведение платежей останавливается до следующего дня.

## Шлюз №1
### Данные для мерчанта:
```
merchant_id=6
merchant_key=KaTf5tZYHx4v7pgZ
```

### Поля, которые отправляются на `callback_url` в формате `application/json`:
| Field       | Type    | Description                                                           |
|-------------|---------|-----------------------------------------------------------------------|
| merchant_id | Integer | ID of merchant                                                        |
| payment_id  | Integer | Merchant's payment ID                                                 |
| status      | String  | Payment status. Could be new, pending, completed, expired or rejected |
| amount      | Integer | Payment amount                                                        |
| amount_paid | Integer | Actually paid amount (in merchant's currency)                         |
| timestamp   | Integer | Current timestamp                                                     |
| sign        | String  | Signature                                                             |

### Правила формирования подписи:

1. Отсортировать параметры по алфавиту.
2. Объединить их (`за исключением sign`), используя разделитель `:`.
3. К концу получившейся строки добавить `merchant_key`.
4. Получить из неё SHA256 хеш.

### Пример запроса:
```
{
    "merchant_id": 6,
    "payment_id": 13,
    "status": "completed",
    "amount": 500,
    "amount_paid": 500,
    "timestamp": 1654103837,
    "sign": "f027612e0e6cb321ca161de060237eeb97e46000da39d3add08d09074f931728"
}
```
<br><br><br>
## Шлюз №2
### Данные для мерчанта:
```
app_id=816
app_key=rTaasVHeteGbhwBx
```

### Поля, которые отправляются на `callback_url` в формате `multipart/form-data`:
| Field       | Type    | Description                                                           |
|-------------|---------|-----------------------------------------------------------------------|
| project | Integer | ID of merchant                                                        |
| invoice  | Integer | Merchant's payment ID                                                 |
| status      | String  | Payment status. Could be created, inprogress, paid, expired or rejected |
| amount      | Integer | Payment amount                                                        |
| amount_paid | Integer | Actually paid amount (in merchant's currency)                         |
| rand   | String | Random string                                                     |

В запросе передается заголовок `Authorization`, в котором находится подпись.

### Правила формирования подписи:

1. Отсортировать параметры по алфавиту.
2. Объединить поля, используя разделитель `.`.
3. К концу получившейся строки добавить `app_key`.
4. Получить из неё MD5 хеш.

### Пример запроса:
```
Headers:
    Authorization: d84eb9036bfc2fa7f46727f101c73c73
Body:
{
    "project": 816,
    "invoice": 73,
    "status": "completed",
    "amount": 700,
    "amount_paid": 700,
    "rand": "SNuHufEJ",
}
```
