<?  namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/css/swiper.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/chosen.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
</head>

<body>

<div class="autorization-page">
    <div class="autorization-page-forms">
        <h3 id="title-field">Вход</h3>
        <form>
            <div>
                <label for="email-field" class="email-field-label">E-mail или Никнейм</label>
                <input type="text" id="email-field" class="email-field-input">
            </div>
            <div>
                <label for="password-field" class="password-field-label">Пароль</label>
                <input type="password" id="password-field" class="password-field-input">
            </div>
            <div>
                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            </div>
            <div>
                <p id="error-field"></p>
            </div>
        </form>
        <div class="access">
            <button class="aut-button">Войти</button>
            <a href="/labs/kursach/recovery/">Забыли пароль?</a>
        </div>
    </div>

    <div class="if-not-registered">
        <p>У вас нет аккаунта? <a href="/labs/kursach/registration/">Зарегистрируйтесь</a></p>
    </div>
</div>

</body>

<script>
    $(".aut-button").click(function () {
        captcha_generate();
    });

    function captcha_generate() {
        grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
            .then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
            }).then(autorization);
    }

    function autorization() {
        var data = {
            'type':'autorization',
            'login':$(".email-field-input").val(),
            'password':$(".password-field-input").val(),
            'g-recaptcha-response':$("#g-recaptcha-response").val(),
        };
        if (data['login'] === '') {
            $(".email-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните указанные поля');
        } else
            $(".email-field-input").css('border-bottom', '3px solid #eff3f9');
        if (data['password'] === '') {
            $(".password-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните указанные поля');
        } else
            $(".password-field-input").css('border-bottom', '3px solid #eff3f9');
        console.log(data);
        if (data['login'] !== '' && data['password'] !== '') {
            $.ajax({
                url: 'handler.php',
                type: "POST",
                dataType: "html",
                data: data,
                success: function (response) {
                    if (response === 'Success')
                        window.location = 'http://makson.f-dev.ru/ru/';
                    if (response === 'Undefined login or password') {
                        $(".email-field-input").css('border-bottom', '3px solid darkred');
                        $(".password-field-input").css('border-bottom', '3px solid darkred');
                        $("#error-field").text('Неверный логин или пароль');
                    }
                },
                error: function (response) {
                    console.log('Авторизация не удалась!');
                }
            })
        }
    }
</script>