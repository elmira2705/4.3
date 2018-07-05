<?php
error_reporting(E_ALL);

include 'dbconfig.php';

$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
if (!$pdo) {
    die('Could not connect');
}
if ($_POST) {
    $login = trim(htmlspecialchars(stripslashes($_POST['login'])));
    $password = password_hash(trim(htmlspecialchars(stripslashes($_POST['password']))), PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT `login`, `password` FROM user WHERE `login` = ?");
    $stmt->execute([$login]);
    $check = $stmt->fetch();
    if (isset($_POST['log_in'])) {
        if ($check && password_verify($_POST['password'], $check['password'])) {
            setcookie("logged_in", $login, $cookie_expiration_time);
            header("Location: index.php");
        } else {
            echo 'Введены неверные данные. Попробуйте еще раз.';
        }
    } elseif (isset($_POST['register'])) {
        if ($check !== false) {
            echo 'Такой пользователь уже есть!';
        } else {
            $add = $pdo->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
            $add->execute([$login, $password]);
            setcookie("logged_in", $login, $cookie_expiration_time);
            header("Location: index.php");
        }
    }
}

?>

<!doctype html>
<html lang="ru">
<head>
    <title>Добро пожаловать!</title>
</head>

<body>
<p>Войдите или зарегистрируйтесь:</p>
<form method="post" action="">
    <input name="login" placeholder="Логин">
    <input type="password" name="password" placeholder="Пароль">
    <input type="submit" name="log_in" value="Вход">
    <input type="submit" name="register" value="Регистрация">
</form>
</body>

</html>
