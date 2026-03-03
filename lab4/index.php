<?php

declare(strict_types=1);

/**
 * Array of bank transactions.
 *
 * Each transaction is an associative array with the following keys:
 * - id (int): unique transaction identifier
 * - date (string): transaction date in YYYY-MM-DD format
 * - amount (float): transaction amount
 * - description (string): payment purpose/description
 * - merchant (string): recipient organization name
 *
 * @var array<int, array{
 *   id:int,
 *   date:string,
 *   amount:float,
 *   description:string,
 *   merchant:string
 * }>
 */
$transactions = [
    [
        'id' => 1,
        'date' => '2026-03-03',
        'amount' => 50.00,
        'description' => 'Breakfast',
        'merchant' => 'Local cafe',
    ],
    [
        'id' => 2,
        'date' => '2026-03-04',
        'amount' => 100.00,
        'description' => 'Subscription',
        'merchant' => 'Spotify',
    ],
    [
        'id' => 3,
        'date' => '2026-03-05',
        'amount' => 200.00,
        'description' => 'Purcharing a game',
        'merchant' => 'Steam',
    ],
    [
        'id' => 4,
        'date' => '2026-03-06',
        'amount' => 500.00,
        'description' => 'Buying clothes',
        'merchant' => 'Local clothing store',
    ],
    [
        'id' => 5,
        'date' => '2026-03-07',
        'amount' => 50.00,
        'description' => 'Paying bills',
        'merchant' => 'Local bank',
    ],
    [
        'id' => 6,
        'date' => '2026-03-08',
        'amount' => 80.00,
        'description' => 'Taxi ride',
        'merchant' => 'Local Taxi',
    ],
    [
        'id' => 7,
        'date' => '2026-03-09',
        'amount' => 30.00,
        'description' => 'Snack',
        'merchant' => 'Mini market',
    ],
    [
        'id' => 8,
        'date' => '2026-03-10',
        'amount' => 6.00,
        'description' => 'Bus ticket',
        'merchant' => 'City transport',
    ],
    [
        'id' => 9,
        'date' => '2026-03-11',
        'amount' => 100.00,
        'description' => 'Lunch',
        'merchant' => 'Food court',
    ],
    [
        'id' => 10,
        'date' => '2026-03-12',
        'amount' => 600.00,
        'description' => 'Pharmacy',
        'merchant' => 'Local Pharmacy',
    ],
];

/**
 * Calculates the total amount of all transactions.
 *
 * Iterates through the list and sums the "amount" field of each transaction.
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return float Total amount of all transactions.
 */
function calculateTotalAmount(array $transactions): float
{
    $total = 0.0;

    foreach ($transactions as $t) {
        $total += (float)$t['amount'];
    }

    return $total;
}

/**
 * Finds the first transaction where description contains a substring.
 *
 * Search is case-insensitive. Returns the first match or null if not found.
 * In this implementation array_search() is used over an auxiliary boolean array.
 *
 * @param string $descriptionPart Part of description to search for.
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array{id:int, date:string, amount:float, description:string, merchant:string}|null Found transaction or null.
 */
function findTransactionByDescription(string $descriptionPart, array $transactions): ?array
{
    $matches = [];

    foreach ($transactions as $t) {
        $matches[] = stripos($t['description'], $descriptionPart) !== false;
    }

    $index = array_search(true, $matches, true);

    if ($index === false) {
        return null;
    }

    return $transactions[$index];
}

/**
 * Finds transaction by id using a foreach loop.
 *
 * Returns the first transaction whose "id" equals the provided id.
 *
 * @param int $id Transaction identifier.
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array{id:int, date:string, amount:float, description:string, merchant:string}|null Found transaction or null.
 */
function findTransactionByIdForeach(int $id, array $transactions): ?array
{
    foreach ($transactions as $t) {
        if ($t['id'] === $id) {
            return $t;
        }
    }

    return null;
}

/**
 * Finds transaction by id using array_filter().
 *
 * array_filter() returns an array of matches (with original keys preserved),
 * so array_values() is used to safely get the first element.
 *
 * @param int $id Transaction identifier.
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array{id:int, date:string, amount:float, description:string, merchant:string}|null Found transaction or null.
 */
function findTransactionByIdFilter(int $id, array $transactions): ?array
{
    $filtered = array_filter($transactions, function (array $t) use ($id) {
        return $t['id'] === $id;
    });

    if (empty($filtered)) {
        return null;
    }

    return array_values($filtered)[0];
}

/**
 * Calculates the number of days between transaction date and today.
 *
 * Uses DateTime::diff(). Returned value:
 * - positive: if the date is in the past
 * - 0: if the date is today
 * - negative: if the date is in the future
 *
 * @param string $date Transaction date in YYYY-MM-DD format.
 * @return int Difference in days (signed).
 * @throws Exception If date format is invalid for DateTime.
 */
function daysSinceTransaction(string $date): int
{
    $tDate = new DateTime($date);
    $today = new DateTime('today');

    return (int)$tDate->diff($today)->format('%r%a');
}

/**
 * Adds a new transaction to the global $transactions array.
 *
 * According to the task requirement, $transactions is accessed via global scope.
 * The function appends a new associative array with the specified fields.
 *
 * @param int $id Transaction identifier.
 * @param string $date Transaction date in YYYY-MM-DD format.
 * @param float $amount Transaction amount.
 * @param string $description Transaction description.
 * @param string $merchant Merchant/recipient name.
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void
{
    global $transactions;

    $transactions[] = [
        'id' => $id,
        'date' => $date,
        'description' => $description,
        'merchant' => $merchant,
        'amount' => $amount,
    ];
}

// Calculate total before adding (as in your original code).
$totalAmount = calculateTotalAmount($transactions);

// Example: search by description part
$found = findTransactionByDescription('bill', $transactions);
if ($found) {
    echo $found['description'];
} else {
    echo "Не найдено";
}
echo "<br>";

// Example: find by id (two implementations)
$found1 = findTransactionByIdForeach(2, $transactions);
$found2 = findTransactionByIdFilter(5, $transactions);

echo "Через foreach:<br>";
var_dump($found1);
echo "<br>Через array_filter:<br>";
var_dump($found2);
echo "<br>";

// Add new transaction
addTransaction(11, '2026-03-13', 1500.00, 'Shoes', 'Sport store');

// Sort by date ascending
#usort($transactions, function (array $a, array $b): int { 
#return strtotime($b['date']) <=> strtotime($a['date']); 
#});

// Sort by amount descending
usort($transactions, function (array $a, array $b): int {
return $b['amount'] <=> $a['amount'];
});

?>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Days Since Transaction</th>
            <th>Description</th>
            <th>Merchant</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $t) { ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= $t['date'] ?></td>
                <td><?= daysSinceTransaction($t['date']) ?></td>
                <td><?= $t['description'] ?></td>
                <td><?= $t['merchant'] ?></td>
                <td><?= $t['amount'] ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="5"><strong>Total</strong></td>
            <td><strong><?= $totalAmount ?></strong></td>
        </tr>
    </tbody>
</table>