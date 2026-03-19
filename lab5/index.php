<?php

declare(strict_types=1);

/**
 * Defines the contract for transaction storage operations.
 */
interface TransactionStorageInterface
{
    /**
     * Adds a transaction to storage.
     *
     * @param Transaction $transaction Transaction to add.
     *
     * @return void
     */
    public function addTransaction(Transaction $transaction): void;

    /**
     * Removes a transaction by its identifier.
     *
     * @param int $id Transaction identifier.
     *
     * @return void
     */
    public function removeTransactionById(int $id): void;

    /**
     * Returns all stored transactions.
     *
     * @return Transaction[]
     */
    public function getAllTransactions(): array;

    /**
     * Finds a transaction by its identifier.
     *
     * @param int $id Transaction identifier.
     *
     * @return Transaction|null Found transaction or null if it does not exist.
     */
    public function findById(int $id): ?Transaction;
}

/**
 * Represents a single banking transaction.
 */
final class Transaction
{
    /**
     * Creates a transaction object.
     *
     * @param int $id Unique transaction identifier.
     * @param DateTime $date Transaction date.
     * @param float $amount Transaction amount.
     * @param string $description Payment description.
     * @param string $merchant Payment recipient.
     * @param string $category Recipient category.
     */
    public function __construct(
        private int $id,
        private DateTime $date,
        private float $amount,
        private string $description,
        private string $merchant,
        private string $category
    ) {
    }

    /**
     * Returns the transaction identifier.
     *
     * @return int Unique transaction identifier.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the transaction date.
     *
     * @return DateTime Transaction date.
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Returns the transaction amount.
     *
     * @return float Transaction amount.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Returns the payment description.
     *
     * @return string Payment description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns the recipient name.
     *
     * @return string Recipient name.
     */
    public function getMerchant(): string
    {
        return $this->merchant;
    }

    /**
     * Returns the recipient category.
     *
     * @return string Recipient category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Calculates how many days have passed since the transaction date.
     *
     * @return int Number of days since the transaction.
     */
    public function getDaysSinceTransaction(): int
    {
        $currentDate = new DateTime();
        $interval = $this->date->diff($currentDate);

        return (int) $interval->days;
    }
}

/**
 * Stores transactions and provides basic access operations.
 */
final class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[]
     */
    private array $transactions = [];

    /**
     * Adds a transaction to the repository.
     *
     * @param Transaction $transaction Transaction to add.
     *
     * @return void
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Removes a transaction by its identifier.
     *
     * @param int $id Transaction identifier.
     *
     * @return void
     */
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

    /**
     * Returns all transactions stored in the repository.
     *
     * @return Transaction[]
     */
    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Finds a transaction by its identifier.
     *
     * @param int $id Transaction identifier.
     *
     * @return Transaction|null Found transaction or null if it is absent.
     */
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

/**
 * Provides business logic for processing transactions.
 */
final class TransactionManager
{
    /**
     * Creates a transaction manager.
     *
     * @param TransactionStorageInterface $repository Transaction storage implementation.
     */
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    /**
     * Calculates the total amount of all transactions.
     *
     * @return float Total amount of all transactions.
     */
    public function calculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }

        return $total;
    }

    /**
     * Calculates the total amount of transactions within a date range.
     *
     * @param string $startDate Start date of the range.
     * @param string $endDate End date of the range.
     *
     * @return float Total amount for transactions in the specified period.
     */
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

    /**
     * Counts transactions for a specific recipient.
     *
     * @param string $merchant Recipient name.
     *
     * @return int Number of transactions for the recipient.
     */
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

    /**
     * Returns transactions sorted by date in ascending order.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $first, Transaction $second): int {
            return $first->getDate() <=> $second->getDate();
        });

        return $transactions;
    }

    /**
     * Returns transactions sorted by amount in descending order.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $first, Transaction $second): int {
            return $second->getAmount() <=> $first->getAmount();
        });

        return $transactions;
    }
}

/**
 * Builds an HTML table for displaying transactions.
 */
final class TransactionTableRenderer
{
    /**
     * Generates HTML markup for a transaction table.
     *
     * @param Transaction[] $transactions
     *
     * @return string HTML table markup.
     */
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

foreach ($transactions as $transaction) {
    $repository->addTransaction($transaction);
}

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$foundTransaction = $repository->findById(5);
$sortedByDate = $manager->sortTransactionsByDate();
$sortedByAmount = $manager->sortTransactionsByAmountDesc();

echo '<h2>Банковские транзакции</h2>';
echo '<p>Общая сумма всех транзакций: ' . number_format($manager->calculateTotalAmount(), 2, '.', ' ') . '</p>';
echo '<p>Сумма транзакций за период с 2026-02-01 по 2026-03-15: ' .
    number_format($manager->calculateTotalAmountByDateRange('2026-02-01', '2026-03-15'), 2, '.', ' ') .
    '</p>';
echo '<p>Количество транзакций для получателя Orange: ' . $manager->countTransactionsByMerchant('Orange') . '</p>';
echo '<p>Поиск транзакции по ID 5: ' . ($foundTransaction?->getDescription() ?? 'Транзакция не найдена') . '</p>';

echo '<h3>Сортировка по дате</h3>';
echo $renderer->render($sortedByDate);

echo '<h3>Сортировка по сумме по убыванию</h3>';
echo $renderer->render($sortedByAmount);
