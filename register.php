<?php
// register.php
include 'config.php';
include 'functions.php';

// Функции валидации
function validate_phone($phone) {
    // Проверка формата +7XXXXXXXXXX (11 цифр)
    return preg_match('/^\+7\d{10}$/', $phone);
}

function validate_email($email) {
    // Проверка формата email и наличия @
    return filter_var($email, FILTER_VALIDATE_EMAIL) && strpos($email, '@') !== false;
}

function validate_full_name($full_name) {
    // Проверка что ФИО состоит ровно из 3 слов (разделенных пробелами)
    $words = explode(' ', trim($full_name));
    return count($words) === 3 && strlen($full_name) <= 100;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    $errors = [];
    
    // Валидация полей
    if (empty($full_name)) {
        $errors[] = "ФИО обязательно";
    } elseif (!validate_full_name($full_name)) {
        $errors[] = "ФИО должно состоять ровно из 3 слов (Фамилия Имя Отчество) и быть не длиннее 100 символов";
    }
    
    if (empty($phone)) {
        $errors[] = "Телефон обязателен";
    } elseif (!validate_phone($phone)) {
        $errors[] = "Неверный формат телефона. Используйте формат +7XXXXXXXXXX";
    }
    
    if (empty($email)) {
        $errors[] = "Email обязателен";
    } elseif (!validate_email($email)) {
        $errors[] = "Неверный формат email. Email должен содержать @ и быть в правильном формате (например, user@example.com)";
    } elseif (strlen($email) > 50) {
        $errors[] = "Email слишком длинный (макс. 50 символов)";
    }
    
    if (empty($login)) {
        $errors[] = "Логин обязателен";
    } elseif (strlen($login) < 4 || strlen($login) > 20) {
        $errors[] = "Логин должен быть от 4 до 20 символов";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        $errors[] = "Логин может содержать только буквы, цифры и подчеркивание";
    }
    
    if (empty($password)) {
        $errors[] = "Пароль обязателен";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен быть не менее 6 символов";
    } elseif (strlen($password) > 30) {
        $errors[] = "Пароль слишком длинный (макс. 30 символов)";
    }
    
    // Проверка уникальности
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR login = ?");
        $stmt->execute([$email, $login]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Email или логин уже используются";
        }
    }
    
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, phone, email, login, password) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$full_name, $phone, $email, $login, $password_hash]);
        
        $_SESSION['success'] = "Регистрация прошла успешно! Теперь войдите в систему.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | СпортGo</title>
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
        <h1>Регистрация</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-group">
                <label>ФИО (Фамилия Имя Отчество):</label>
                <input type="text" name="full_name" required 
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                       maxlength="100"
                       pattern="^[А-Яа-яЁёA-Za-z]+\s[А-Яа-яЁёA-Za-z]+\s[А-Яа-яЁёA-Za-z]+$"
                       title="Введите ФИО ровно из 3 слов (Фамилия Имя Отчество) через пробел">
            </div>
            <div class="form-group">
                <label>Телефон (формат: +7XXXXXXXXXX):</label>
                <input type="tel" name="phone" required 
                       pattern="\+7\d{10}" 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       placeholder="+71234567890">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       maxlength="50"
                       title="Введите корректный email (например, user@example.com)">
            </div>
            <div class="form-group">
                <label>Логин (4-20 символов, буквы, цифры и _):</label>
                <input type="text" name="login" required 
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                       pattern="[a-zA-Z0-9_]{4,20}"
                       title="Только буквы, цифры и подчеркивание, от 4 до 20 символов">
            </div>
            <div class="form-group">
                <label>Пароль (6-30 символов):</label>
                <input type="password" name="password" required 
                       minlength="6" maxlength="30">
            </div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <div class="login-link">
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        </div>
    </div>
</body>
</html>