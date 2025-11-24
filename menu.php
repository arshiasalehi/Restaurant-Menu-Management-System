<?php
$dbHost = '127.0.0.1';
$dbName = 'resturant';
$dbUser = 'root';
$dbPass = '';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
$messages = [];
$errors = [];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}

$categories = ['Appetizer', 'Main Course', 'Dessert', 'Beverage'];
$statuses = ['Pending', 'Completed', 'Cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        // Handle dish submission
        $dishName = trim($_POST['dish_name'] ?? '');
        $dishCategory = $_POST['dish_category'] ?? '';
        $dishPrice = $_POST['dish_price'] ?? '';
        $dishDescription = trim($_POST['dish_description'] ?? '');
        $dishAvailable = isset($_POST['dish_available']) ? 1 : 0;

        if ($dishName !== '' && $dishCategory && $dishPrice !== '') {
            $stmt = $pdo->prepare(
                'INSERT INTO dishes (name, category, price, description, available) VALUES (:name, :category, :price, :description, :available)'
            );
            $stmt->execute([
                ':name' => $dishName,
                ':category' => $dishCategory,
                ':price' => $dishPrice,
                ':description' => $dishDescription,
                ':available' => $dishAvailable,
            ]);
            $messages[] = 'Dish added successfully.';
        }

        // Handle orders submission
        $dishIds = $_POST['order_dish_id'] ?? [];
        $customers = $_POST['order_customer'] ?? [];
        $quantities = $_POST['order_quantity'] ?? [];
        $dates = $_POST['order_date'] ?? [];
        $orderStatuses = $_POST['order_status'] ?? [];

        $stmtOrder = $pdo->prepare(
            'INSERT INTO orders (dish_id, customer_name, quantity, order_date, status) VALUES (:dish_id, :customer_name, :quantity, :order_date, :status)'
        );

        $addedOrders = 0;
        foreach ($dishIds as $index => $dishId) {
            $dishId = (int) $dishId;
            $customer = trim($customers[$index] ?? '');
            $quantity = (int) ($quantities[$index] ?? 0);
            $orderDate = $dates[$index] ?? '';
            $status = $orderStatuses[$index] ?? 'Pending';

            if ($dishId > 0 && $customer !== '' && $quantity > 0) {
                $stmtOrder->execute([
                    ':dish_id' => $dishId,
                    ':customer_name' => $customer,
                    ':quantity' => $quantity,
                    ':order_date' => $orderDate !== '' ? $orderDate : date('Y-m-d'),
                    ':status' => $status,
                ]);
                $addedOrders++;
            }
        }

        if ($addedOrders > 0) {
            $messages[] = "$addedOrders order(s) saved.";
        }

        if (empty($messages)) {
            $messages[] = 'Nothing to save — please fill in the form.';
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        $errors[] = 'Save failed: ' . $e->getMessage();
    }
}

$dishOptions = $pdo->query('SELECT id, name, category FROM dishes ORDER BY name')->fetchAll();
$dishStats = $pdo->query(
    "SELECT d.id, d.name, d.category, d.price, d.available, COALESCE(SUM(o.quantity), 0) AS orders_count
     FROM dishes d
     LEFT JOIN orders o ON o.dish_id = d.id
     GROUP BY d.id
     ORDER BY orders_count DESC, d.name ASC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Menu &amp; Orders</h1>
        <p class="text-slate-600">Manage dishes and register orders in one place.</p>
    </div>

    <?php foreach ($messages as $msg): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800"><?php echo htmlspecialchars($msg); ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $err): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>

    <form method="post" class="bg-white rounded-xl shadow-md ring-1 ring-slate-100">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Add Dish</h2>
                    <p class="text-sm text-slate-500">Include pricing, category, and availability.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Name</span>
                    <input type="text" name="dish_name" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required />
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Category</span>
                    <select name="dish_category" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required>
                        <option value="" disabled selected>Select one</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Price</span>
                    <input type="number" step="0.01" min="0" name="dish_price" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required />
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Availability</span>
                    <div class="mt-2 flex items-center gap-2">
                        <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" name="dish_available" id="dishAvailable" checked />
                        <label for="dishAvailable" class="text-sm text-slate-700">Available</label>
                    </div>
                </label>
                <label class="block md:col-span-2 lg:col-span-3">
                    <span class="text-sm font-medium text-slate-700">Description</span>
                    <textarea name="dish_description" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" rows="3"></textarea>
                </label>
            </div>

            <div class="border-t border-slate-200 pt-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-900">Orders</h2>
                    <button type="button" class="rounded-lg border border-indigo-200 px-3 py-1 text-sm font-medium text-indigo-700 hover:bg-indigo-50 active:bg-indigo-100" id="addOrderRow">Add order</button>
                </div>
                <div id="orderRows" class="mt-4 space-y-4">
                    <div class="order-row grid gap-3 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Dish</span>
                            <select name="order_dish_id[]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required>
                                <option value="" disabled selected>Select dish</option>
                                <?php foreach ($dishOptions as $dish): ?>
                                    <option value="<?php echo (int) $dish['id']; ?>">
                                        <?php echo htmlspecialchars($dish['name'] . ' (' . $dish['category'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Customer name</span>
                            <input type="text" name="order_customer[]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Quantity</span>
                            <input type="number" name="order_quantity[]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" min="1" value="1" required />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Date</span>
                            <input type="date" name="order_date[]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" value="<?php echo date('Y-m-d'); ?>" />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Status</span>
                            <select name="order_status[]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-6 py-4 rounded-b-xl">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white font-semibold shadow hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-300 focus:outline-none">Save</button>
        </div>
    </form>

    <div class="mt-6 bg-white rounded-xl shadow-md ring-1 ring-slate-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Dishes Overview</h2>
                <p class="text-sm text-slate-500">Sorted by total orders (desc)</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold">Dish</th>
                        <th class="px-4 py-3 text-sm font-semibold">Category</th>
                        <th class="px-4 py-3 text-sm font-semibold text-right">Price</th>
                        <th class="px-4 py-3 text-sm font-semibold text-right">Total Orders</th>
                        <th class="px-4 py-3 text-sm font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($dishStats as $row): ?>
                        <?php $available = (int) $row['available'] === 1; ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 <?php echo $available ? 'text-emerald-600 font-semibold' : 'text-slate-800'; ?>">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?php echo htmlspecialchars($row['category']); ?></td>
                            <td class="px-4 py-3 text-right text-slate-700">$<?php echo number_format((float)$row['price'], 2); ?></td>
                            <td class="px-4 py-3 text-right text-slate-700"><?php echo (int) $row['orders_count']; ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo $available ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'; ?>">
                                    <?php echo $available ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    const orderRows = document.getElementById('orderRows');
    const addOrderButton = document.getElementById('addOrderRow');

    function resetRow(row) {
        row.querySelectorAll('input, select').forEach(el => {
            if (el.tagName === 'SELECT') {
                const firstOption = el.querySelector('option');
                el.selectedIndex = firstOption ? 0 : -1;
            } else if (el.type === 'date') {
                el.value = new Date().toISOString().slice(0, 10);
            } else if (el.type === 'number') {
                el.value = 1;
            } else {
                el.value = '';
            }
        });
    }

    addOrderButton.addEventListener('click', () => {
        const firstRow = orderRows.querySelector('.order-row');
        if (!firstRow) return;
        const clone = firstRow.cloneNode(true);
        resetRow(clone);
        orderRows.appendChild(clone);
    });
})();
</script>
</body>
</html>
