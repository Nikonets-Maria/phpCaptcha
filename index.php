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
    } else {
        $message = 'Попробуйте ещё раз';
    }
}

function createCaptchaImage($code) {
    $width = 200;
    $height = 50;
    $image = imagecreatetruecolor($width, $height);

    imagealphablending($image, false);
    imagesavealpha($image, true);

    $background_color = imagecolorallocatealpha($image, 0, 0, 0, 127);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    
    imagefill($image, 0, 0, $background_color);

    $font_path= "CAMBRIAZ.ttf";
    $letter_spacing = 10; 
    $x = 10; 
    

    for ($i = 0; $i < strlen($code); $i++) {
        $font_size = rand(15, 25 );
        $y = rand(20, $height - 10); 
        $z = rand(0, 70 );
        $text_char = $code[$i];

        imagettftext($image, $font_size, $z, $x, $y, $text_color, $font_path, $text_char);
        $x += $font_size + $letter_spacing; 
    }
    
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

if (isset($_GET['captcha'])) {
    createCaptchaImage($captcha_code);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Monomakh&display=swap" rel="stylesheet">
    <title>Капча</title>
    <style>
        *{
            font-family: "Monomakh", serif;
        }
        .captcha-container {
            position: relative;
            display: inline-block;
        }
        .captcha-image {
            width: 300px;
            height: auto;
        }
        .captcha-chars{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>



<h1>Введите код с капчи</h1>

<div class="captcha-container">
<img src="capcha.jpg" alt="Captcha Image " class="captcha-image">  
    <img src="?captcha=1" alt="Captcha Image img" class="captcha-chars">

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
    <p> <?php echo $message; ?> </p>
<?php endif; ?>

    <p> <?php echo $captcha_code; ?> </p>
   
</body>
</html>
