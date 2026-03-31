# Лабораторная работа №5 Объектно-ориентированное программирование в PHP `Бурцева Дарья, IA2403`

## Цель работы
Освоить основы объектно-ориентированного программирования в PHP на практике. Научиться создавать собственные классы, использовать инкапсуляцию для защиты данных, разделять ответственность между классами, а также применять интерфейсы для построения гибкой архитектуры приложения.

## Условие работы
Необходимо разработать приложение для управления банковскими транзакциями.

Приложение должно обеспечивать:
1. хранение банковских транзакций;
2. добавление новых транзакций;
3. удаление транзакций;
4. поиск транзакций;
5. сортировку транзакций;
6. выполнение вычислений над коллекцией транзакций;
7. вывод данных в виде HTML-таблицы.

В рамках лабораторной работы требовалось применить объектно-ориентированный подход.

---

## Ход работы

### 1. Включение строгой типизации
В начале файла была включена строгая типизация:

```php
<?php

declare(strict_types=1);
````

Строгая типизация позволяет контролировать соответствие типов аргументов и возвращаемых значений, что уменьшает количество ошибок во время выполнения программы.

---

### 2. Реализация класса `Transaction`

Для представления одной банковской транзакции был создан класс `Transaction`.

Класс содержит приватные свойства:

* `id` - идентификатор транзакции;
* `date` - дата транзакции;
* `amount` - сумма транзакции;
* `description` - описание платежа;
* `merchant` - получатель платежа;
* `category` - категория получателя.

```php
final class Transaction
{
   
    public function __construct(
        private int $id,
        private DateTime $date,
        private float $amount,
        private string $description,
        private string $merchant,
        private string $category
    ) {
    }

    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMerchant(): string
    {
        return $this->merchant;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDaysSinceTransaction(): int
    {
        $currentDate = new DateTime();
        $interval = $this->date->diff($currentDate);

        return (int) $interval->days;
    }
}
```

Все значения передаются через конструктор. Для доступа к данным были созданы getter-методы.

Также был реализован метод:

```php
getDaysSinceTransaction(): int
```

Этот метод вычисляет количество дней с момента совершения транзакции до текущей даты с использованием класса `DateTime`.

---

### 3. Реализация интерфейса `TransactionStorageInterface`

Для повышения гибкости архитектуры был создан интерфейс `TransactionStorageInterface`.

Интерфейс содержит методы:

* `addTransaction(Transaction $transaction): void`
* `removeTransactionById(int $id): void`
* `getAllTransactions(): array`
* `findById(int $id): ?Transaction`

```php
interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;

    public function removeTransactionById(int $id): void;

    public function getAllTransactions(): array;

    public function findById(int $id): ?Transaction;
}
```

---

### 4. Реализация класса `TransactionRepository`

Для хранения коллекции транзакций был создан класс `TransactionRepository`, реализующий интерфейс `TransactionStorageInterface`.

Класс содержит приватное свойство:

* массив объектов `Transaction`.

Были реализованы методы:

* добавление транзакции;
* удаление транзакции по идентификатору;
* получение полного списка транзакций;
* поиск транзакции по идентификатору.

```php
final class TransactionRepository implements TransactionStorageInterface
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
                $this->transactions = array_values($this->transactions);

                return;
            }
        }
    }

    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }
}
```

Таким образом, класс `TransactionRepository` отвечает только за работу с коллекцией транзакций и не содержит бизнес-логики.

---

### 5. Реализация класса `TransactionManager`

Для выполнения операций над коллекцией транзакций был создан класс `TransactionManager`.

```php
final class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    public function calculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }

        return $total;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $transactionDate = $transaction->getDate();

            if ($transactionDate >= $start && $transactionDate <= $end) {
                $total += $transaction->getAmount();
            }
        }

        return $total;
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            if ($transaction->getMerchant() === $merchant) {
                $count++;
            }
        }

        return $count;
    }

    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $first, Transaction $second): int {
            return $first->getDate() <=> $second->getDate();
        });

        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $first, Transaction $second): int {
            return $second->getAmount() <=> $first->getAmount();
        });

        return $transactions;
    }
}
```

В конструктор класса передается объект, реализующий интерфейс `TransactionStorageInterface`:

```php
public function __construct(
    private TransactionStorageInterface $repository
) {
}
```

Были реализованы следующие методы:

* `calculateTotalAmount(): float` - вычисление общей суммы всех транзакций;
* `calculateTotalAmountByDateRange(string $startDate, string $endDate): float` - вычисление суммы транзакций за указанный период;
* `countTransactionsByMerchant(string $merchant): int` - подсчет количества транзакций по получателю;
* `sortTransactionsByDate(): array` - сортировка транзакций по дате;
* `sortTransactionsByAmountDesc(): array` - сортировка транзакций по сумме по убыванию.

Класс `TransactionManager` не хранит транзакции самостоятельно и не создает их, а использует данные, полученные из репозитория.

---

### 6. Реализация класса `TransactionTableRenderer`

Для вывода транзакций в HTML был создан отдельный класс `TransactionTableRenderer`.

Класс объявлен как `final`, так как он предназначен только для формирования HTML-таблицы и не требует наследования.

```php
final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        $html = '<table border="1" cellpadding="8" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<th>ID транзакции</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Сумма</th>';
        $html .= '<th>Описание</th>';
        $html .= '<th>Название получателя</th>';
        $html .= '<th>Категория получателя</th>';
        $html .= '<th>Количество дней с момента транзакции</th>';
        $html .= '</tr>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . $transaction->getId() . '</td>';
            $html .= '<td>' . $transaction->getDate()->format('Y-m-d') . '</td>';
            $html .= '<td>' . number_format($transaction->getAmount(), 2, '.', ' ') . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDescription(), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getMerchant(), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getCategory(), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . $transaction->getDaysSinceTransaction() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
```

Был реализован метод:

```php
render(array $transactions): string
```

Этот метод принимает массив транзакций и возвращает строку с HTML-кодом таблицы.

Таблица содержит следующие столбцы:

* ID транзакции;
* дата;
* сумма;
* описание;
* название получателя;
* категория получателя;
* количество дней с момента транзакции.

В основном файле выполняется только вызов `render()` и вывод результата через `echo`.

---

### 7. Создание начальных данных

Для проверки работы приложения было создано не менее 10 объектов `Transaction`.

Каждая транзакция содержала:

* разные даты;
* разные суммы;
* разные описания;
* разных получателей;
* разные категории.

```php
$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, new DateTime('2026-01-05'), 500.00, 'Оплата интернета', 'Orange', 'Связь'),
    new Transaction(2, new DateTime('2026-01-11'), 1200.50, 'Покупка продуктов', 'Linella', 'Супермаркет'),
    new Transaction(3, new DateTime('2026-01-20'), 300.00, 'Оплата мобильной связи', 'Moldcell', 'Связь'),
    new Transaction(4, new DateTime('2026-02-02'), 850.75, 'Покупка одежды', 'Zara', 'Одежда'),
    new Transaction(5, new DateTime('2026-02-08'), 150.00, 'Кофе с друзьями', 'Tucano Coffee', 'Кафе'),
    new Transaction(6, new DateTime('2026-02-14'), 2200.00, 'Оплата аренды', 'Rent Service', 'Жилье'),
    new Transaction(7, new DateTime('2026-02-19'), 430.40, 'Заправка автомобиля', 'Rompetrol', 'Транспорт'),
    new Transaction(8, new DateTime('2026-03-01'), 199.99, 'Подписка на сервис', 'Netflix', 'Подписки'),
    new Transaction(9, new DateTime('2026-03-10'), 670.00, 'Покупка лекарств', 'Farmacia Familiei', 'Здоровье'),
    new Transaction(10, new DateTime('2026-03-16'), 980.30, 'Покупка техники', 'Darwin', 'Техника'),
];
```

---

## Структура приложения

В ходе выполнения лабораторной работы приложение было разделено на несколько логических частей:

1. **`Transaction`** - модель транзакции
2. **`TransactionStorageInterface`** - интерфейс для работы с хранилищем
3. **`TransactionRepository`** - хранение коллекции транзакций
4. **`TransactionManager`** - бизнес-логика и вычисления
5. **`TransactionTableRenderer`** - вывод данных в HTML-таблицу

---

## Основные результаты работы

В результате выполнения лабораторной работы было разработано PHP-приложение для управления банковскими транзакциями, в котором реализованы:

* строгая типизация;
* инкапсуляция данных;
* хранение транзакций в репозитории;
* поиск и удаление транзакций;
* вычисление общей суммы и суммы за период;
* подсчет количества транзакций по получателю;
* сортировка по дате и по сумме;
* вывод транзакций в HTML-таблице;
* использование интерфейса для повышения гибкости архитектуры.

---

## Вывод

В ходе лабораторной работы были изучены и практически применены основные принципы объектно-ориентированного программирования в PHP. Было показано, как разделение ответственности между классами упрощает разработку и сопровождение программы. Использование инкапсуляции позволило защитить данные, а применение интерфейса сделало архитектуру более гибкой и расширяемой.

Также была освоена работа со строгой типизацией, объектами `DateTime`, массивами объектов, сортировкой и генерацией HTML средствами PHP. Полученное приложение является примером простой, но правильно организованной объектно-ориентированной системы.

---

## Контрольные вопросы

### 1. Зачем нужна строгая типизация в PHP и как она помогает при разработке?

Строгая типизация в PHP заставляет интерпретатор проверять соответствие типов данных в аргументах функций, возвращаемых значениях и свойствах классов. Она исключает ошибки, связанные с неожиданным преобразованием типов данных, выбрасывая `TypeError` при несоответствии, гарантируя, что программа работает только с ожидаемыми данными, что значительно повышает надежность, читаемость и предсказуемость кода. 

### 2. Что такое класс в объектно-ориентированном программировании и какие основные компоненты класса вы знаете?

Класс - это шаблон для создания объектов. Он описывает, какие данные и действия будут у объектов. Основными компонентами класса являются свойства, методы, конструктор и модификаторы доступа (`public`, `private`, `protected`).

### 3. Объясните, что такое полиморфизм и как он может быть реализован в PHP.

Полиморфизм - это возможность работать с разными объектами через общий интерфейс или общий родительский тип. В PHP полиморфизм может быть реализован через интерфейсы и наследование. Если класс `TransactionRepository` реализует интерфейс `TransactionStorageInterface`, то `TransactionManager` может работать не с конкретным классом `TransactionRepository`, а с любым объектом, который реализует этот интерфейс. 

Это и есть проявление полиморфизма: один и тот же менеджер работает с разными хранилищами одинаковым образом.

### 4. Что такое интерфейс в PHP и как он отличается от абстрактного класса?

Интерфейс в PHP - это конструкция, которая задает набор методов, которые класс обязан реализовать. Интерфейс определяет что должен уметь объект, но не описывает, как именно это делается.


Основные отличия:  
Реализация: Интерфейсы не имеют реализации методов; абстрактные классы могут содержать как реализованные, так и абстрактные методы.  
Наследование: Класс может реализовывать множество интерфейсов (implements), но наследовать только один класс (extends).  
Свойства: Интерфейсы не могут содержать свойства (переменные), только константы; абстрактные классы - могут.  
Цель: Интерфейсы - для общего поведения разных классов, абстрактные классы - для общей базы родственных классов

### 5. Какие преимущества дает использование интерфейсов при проектировании архитектуры приложения? Объясните на примере данной лабораторной работы.

Использование интерфейсов делает код гибким и слабо связанным. В данной лабораторной работе `TransactionManager` зависит не от конкретного класса `TransactionRepository`, а от интерфейса `TransactionStorageInterface`. Это означает, что в будущем можно создать другое хранилище, например для работы с файлом или базой данных, и передать его в `TransactionManager` без изменения бизнес-логики. Это упрощает расширение и поддержку приложения.

---

## Заключение

Лабораторная работа позволила закрепить на практике основы объектно-ориентированного программирования в PHP, а также показать важность правильного проектирования архитектуры приложения. Реализованная система управления транзакциями демонстрирует применение классов, интерфейсов, инкапсуляции, строгой типизации и разделения ответственности между компонентами программы.
