<?php  namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация</title>
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
    <div class="registration-page">
        <div class="registration-page-forms">
            <h3 id="title-field">Регистрация</h3>
            <form action="/labs/kursach/registration/" method="POST">
                <div>
                    <label for="email-field" class="email-field-label">E-mail</label>
                    <input type="email" id="email-field" class="email-field-input" size="32">
                </div>
                <div>
                    <label for="fullname-field" class="fullname-field-label">Ф.И.О. (Полностью)</label>
                    <input type="text" id="fullname-field" class="fullname-field-input" size="32">
                </div>
                <div>
                    <label for="username-field" class="username-field-label">Никнейм</label>
                    <input type="text" id="username-field" class="username-field-input" size="32">
                </div>
                <div>
                    <label for="password-field" class="password-field-label">Пароль</label>
                    <input type="password" id="password-field" class="password-field-input" size="20">
                </div>
                <div>
                    <label for="password-repeat-field" class="password-repeat-field-label">Повторите пароль</label>
                    <input type="password" id="password-repeat-field" class="password-repeat-field-input" size="20">
                </div>
                <div>
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                </div>
                <div>
                    <p id="error-field"></p>
                </div>
            </form>
            <div class="access">
                <button class="reg-button" id="reg-button">Зарегистрироваться</button>
            </div>
        </div>
        <div class="if-registered">
            <p>Уже есть аккаунт? <a href="/labs/kursach/autorization/" class="">Войдите</a></p>
        </div>
    </div>
</body>

<script>

    $(".reg-button").click(function () {
        captcha_generate();
    });

    function captcha_generate() {
        grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
            .then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
            }).then(registration);
    }

    function registration() {
        var data = {'type':'registration',
            'email':$(".email-field-input").val(),
            'fullname':$(".fullname-field-input").val(),
            'username':$(".username-field-input").val(),
            'password':$(".password-field-input").val(),
            'g-recaptcha-response':$("#g-recaptcha-response").val()};
        var repeat = $(".password-repeat-field-input").val();
        var pattern = /^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i;

        if (data['email'] === ''){
            $(".email-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните выделенные поля');
        } else if (data['email'].search(pattern) === 0) {
            $(".email-field-input").css('border-bottom', '3px solid #eff3f9');
            $("#error-field").text('');
        } else {
            $(".email-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Неверный Email');
        }

        if (data['fullname'] === ''){
            $(".fullname-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните выделенные поля');
        } else {
            $(".fullname-field-input").css('border-bottom', '3px solid #eff3f9');
        }

        if (data['username'] === ''){
            $(".username-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните выделенные поля');
        } else {
            $(".username-field-input").css('border-bottom', '3px solid #eff3f9');
        }

        if (data['password'] === ''){
            $(".password-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните выделенные поля');
        } else {
            $(".password-field-input").css('border-bottom', '3px solid #eff3f9');
        }

        if (repeat === ''){
            $(".password-repeat-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Заполните выделенные поля');
        } else {
            $(".password-repeat-field-input").css('border-bottom', '3px solid #eff3f9');
        }

        if (data['password'] !== repeat && data['password'] !== '' && repeat !== ''){
            $(".password-field-input").css('border-bottom', '3px solid darkred');
            $(".password-repeat-field-input").css('border-bottom', '3px solid darkred');
            $("#error-field").text('Пароли не совпадают');
        } else if(data['password'] === repeat && data['password'] !== '' && repeat !== '') {
            $(".password-field-input").css('border-bottom', '3px solid #eff3f9');
            $(".password-repeat-field-input").css('border-bottom', '3px solid #eff3f9');
        }
        if (data['password'] === repeat && data['email'] !== '' && data['fullname'] !== '' && data['username'] !== '' && data['password'] !== ''){
            $.ajax({
                url: 'handler.php',
                type: "POST",
                dataType: "html",
                data: data,
                success: function(response) {
                    if (response === "Spam") {
                        console.log(response);
                        $("#error-field").text("Вы робот!");
                    } else if (response === "Success") {
                        window.location = 'http://makson.f-dev.ru/ru/';
                    } else {
                        var result = $.parseJSON(response);
                        if (result.email === 1 && result.username === 1) {
                            $("#error-field").text('Введенные никнейм и почта уже заняты!');
                            $(".email-field-input").css('border-bottom', '3px solid darkred');
                            $(".username-field-input").css('border-bottom', '3px solid darkred');
                        } else if (result.email === 1 && result.username === 0) {
                            $("#error-field").text('Введенная почта уже занята!');
                            $(".email-field-input").css('border-bottom', '3px solid darkred');
                        } else if (result.email === 0 && result.username === 1) {
                            $("#error-field").text('Введенный никнейм уже занят!');
                            $(".username-field-input").css('border-bottom', '3px solid darkred');
                        }
                    }
                },
                error: function(response) {

                }
            });
        }
    }
</script>