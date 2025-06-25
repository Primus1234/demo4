<?php
// create_order.php
include 'config.php';
include 'functions.php';
check_auth();

$equipment = $pdo->query("SELECT * FROM equipment WHERE available_quantity > 0")->fetchAll();
$points = $pdo->query("SELECT * FROM pickup_points")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_id = (int)$_POST['equipment_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $point_id = (int)$_POST['point_id'];
    $payment_method = $_POST['payment_method'];
    
    // Расчет часов
    $hours = (strtotime($end_time) - strtotime($start_time)) / 3600;
    
    // Получение цены
    $stmt = $pdo->prepare("SELECT price_per_hour FROM equipment WHERE equipment_id = ?");
    $stmt->execute([$equipment_id]);
    $price = $stmt->fetchColumn();
    $total_price = $hours * $price;
    
    // Создание заказа
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, equipment_id, point_id, start_time, end_time, total_price, payment_method) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $equipment_id,
        $point_id,
        $start_time,
        $end_time,
        $total_price,
        $payment_method
    ]);
    
    // Обновление доступного количества
    $stmt = $pdo->prepare("UPDATE equipment SET available_quantity = available_quantity - 1 WHERE equipment_id = ?");
    $stmt->execute([$equipment_id]);
    
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа | СпортGo</title>
    <style>
    :root {
        --primary-color: #4a6fa5;
        --secondary-color: #166088;
        --accent-color: #4fc3f7;
        --success-color: #4caf50;
        --warning-color: #ff9800;
        --danger-color: #f44336;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --text-color: #333;
        --border-radius: 8px;
        --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
        color: var(--text-color);
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    h1, h2, h3 {
        color: var(--secondary-color);
        margin-bottom: 1.5rem;
    }

    h1 {
        font-size: 2.2rem;
        font-weight: 600;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--secondary-color);
    }

    input, select, textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.2);
    }

    input:invalid {
        border-color: var(--danger-color);
    }

    button, .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: var(--transition);
    }

    button:hover, .btn:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-success {
        background-color: var(--success-color);
    }

    .btn-danger {
        background-color: var(--danger-color);
    }

    .btn-warning {
        background-color: var(--warning-color);
    }

    .error {
        color: var(--danger-color);
        background-color: #ffebee;
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
    }

    .success {
        color: var(--success-color);
        background-color: #e8f5e9;
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
    }

    .login-link, .register-link {
        text-align: center;
        margin-top: 1.5rem;
    }

    .login-link a, .register-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover, .register-link a:hover {
        text-decoration: underline;
    }

    /* Таблицы */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        box-shadow: var(--box-shadow);
    }

    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Статусы */
    .status-new {
        color: #2196f3;
    }

    .status-confirmed {
        color: #4caf50;
    }

    .status-completed {
        color: #9e9e9e;
    }

    .status-cancelled {
        color: #f44336;
    }

    /* Формы */
    .radio-group {
        display: flex;
        gap: 1rem;
        margin: 1rem 0;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        font-weight: normal;
        cursor: pointer;
    }

    .radio-group input {
        width: auto;
        margin-right: 0.5rem;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
            margin: 1rem;
        }

        h1 {
            font-size: 1.8rem;
        }

        .radio-group {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Анимации */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .container {
        animation: fadeIn 0.5s ease-out;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Оформление заказа</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Спортивный инвентарь:</label>
                <select name="equipment_id" required>
                    <?php foreach($equipment as $item): ?>
                        <option value="<?= $item['equipment_id'] ?>">
                            <?= htmlspecialchars($item['name']) ?> 
                            (<?= $item['price_per_hour'] ?>₽/час)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата и время начала аренды:</label>
                <input type="datetime-local" name="start_time" required>
            </div>
            
            <div class="form-group">
                <label>Дата и время окончания аренды:</label>
                <input type="datetime-local" name="end_time" required>
            </div>
            
            <div class="form-group">
                <label>Пункт выдачи:</label>
                <select name="point_id" required>
                    <?php foreach($points as $point): ?>
                        <option value="<?= $point['point_id'] ?>">
                            <?= htmlspecialchars($point['address']) ?> 
                            (<?= htmlspecialchars($point['working_hours']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Способ оплаты:</label>
                <div class="radio-group">
                    <label><input type="radio" name="payment_method" value="cash" checked> Наличные</label>
                    <label><input type="radio" name="payment_method" value="card"> Карта</label>
                </div>
            </div>
            
            <button type="submit">Подтвердить заказ</button>
        </form>
        
        <a href="dashboard.php" class="back-link">← Вернуться в личный кабинет</a>
    </div>
</body>
</html>