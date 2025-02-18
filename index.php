<?php
session_start();

function generateCaptchaCode($length = 5) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $captchaCode = '';
    for ($i = 0; $i < $length; $i++) {
        $captchaCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $captchaCode;
}

if (!isset($_SESSION['captcha_code']) || isset($_POST['refresh_captcha'])) {
    $_SESSION['captcha_code'] = generateCaptchaCode();
}

$captcha_code = $_SESSION['captcha_code'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['refresh_captcha'])) {
    $user_input = $_POST['captcha_input'];

    if ($user_input === $captcha_code) {
        $message = 'Успешно';
        $_SESSION['captcha_code'] = generateCaptchaCode();
    } else {
        $message = 'Попробуйте ещё раз';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Капча</title>
    <style>
        .captcha-container {
            position: relative;
            display: inline-block;
        }
        .captcha-image {
            width: 300px;
            height: auto;
        }
        .captcha-code {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 50px;
        }
    </style>
</head>
<body>

<h1>Введите код с капчи</h1>

<div class="captcha-container">
<img src="https://storage.yandexcloud.net/s3lxpbulgakov/public/documents/6894/nRNhRhGyhFl2OEYISbdixbi6BbRwkkzkTtJUdZn0.jpg" alt="Captcha Image" class="captcha-image">
<div class="captcha-code"><?php echo $captcha_code; ?></div>
</div>

<form method="POST">
    <input type="text" name="captcha_input" required>
    <button type="submit">Отправить</button>
</form>

<form method="POST" style="display:inline;">
    <input type="hidden" name="refresh_captcha" value="1">
    <button type="submit">Обновить капчу</button>
</form>

<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

</body>
</html>

