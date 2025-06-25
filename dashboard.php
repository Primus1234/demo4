<?php
// dashboard.php
include 'config.php';
include 'functions.php';
check_auth();

$orders = get_user_orders($_SESSION['user_id'], $pdo);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет | СпортGo</title>
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
        <header>
            <h1 class="welcome">Добро пожаловать, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h1>
            <div class="user-actions">
                <a href="create_order.php" class="btn">Создать заказ</a>
                <a href="logout.php" class="btn logout-btn">Выйти</a>
            </div>
        </header>
        
        <h2>История заказов</h2>
        
        <?php if (count($orders) > 0): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Инвентарь</th>
                        <th>Дата начала</th>
                        <th>Дата окончания</th>
                        <th>Пункт выдачи</th>
                        <th>Стоимость</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['equipment_name']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['start_time'])) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['end_time'])) ?></td>
                            <td><?= htmlspecialchars($order['address']) ?></td>
                            <td><?= number_format($order['total_price'], 2) ?>₽</td>
                            <td class="status-<?= $order['status'] ?>">
                                <?php 
                                $statuses = [
                                    'new' => 'Новый',
                                    'confirmed' => 'Подтвержден',
                                    'completed' => 'Выполнен',
                                    'cancelled' => 'Отменен'
                                ];
                                echo $statuses[$order['status']];
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <p>У вас пока нет заказов</p>
                <a href="create_order.php" class="btn create-order-btn">Оформить первый заказ</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>