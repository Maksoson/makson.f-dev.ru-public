<?  namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Восстановление пароля</title>
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
<div class="recovery-page">
    <?php if (!isset($_GET['email']) or !isset($_GET['u'])):?>
    <div class="recovery-page-forms">
        <h3 id="title-field">Восстановление</h3>
        <form>
            <div>
                <label for="email-recovery-field" class="email-recovery-field-label">Введите E-mail</label>
                <input type="email" id="email-recovery-field" class="email-recovery-field-input">
            </div>
            <div>
                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            </div>
            <div>
                <p id="error-field"></p>
            </div>
        </form>
        <div class="access">
            <button class="recovery-button">Восстановить</button>
        </div>
    </div>
    <?php elseif ($_GET['email'] != '' and  $_GET['u'] != ''):?>
    <div class="recovery-page-forms-finish">
        <h3 id="title-field">Восстановление</h3>
        <form>
            <div>
                <label for="password-field" class="password-field-label">Новый пароль</label>
                <input type="password" id="password-field" class="password-field-input" size="20">
            </div>
            <div>
                <label for="password-repeat-field" class="password-repeat-field-label">Повторите новый пароль</label>
                <input type="password" id="password-repeat-field" class="password-repeat-field-input" size="20">
            </div>
            <div>
                <input type="hidden" name="email" value="<?php echo $_GET['email']?>" class="hidden-email-field-input">
            </div>
            <div>
                <input type="hidden" name="key" value="<?php echo $_GET['u']?>" class="hidden-key-field-input">
            </div>
            <div>
                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            </div>
            <div>
                <p id="error-field"></p>
            </div>
        </form>
        <div class="access">
            <button class="accept-recovery-button">Подтвердить</button>
        </div>
    </div>
    <?php endif;?>
</div>
</body>
<script>
    $(".recovery-button").click(function () {
        var rec = 'start_recovery';
        captcha_generate(rec);
    });
    
    $(".accept-recovery-button").click(function () {
        var rec = 'finish_recovery';
        captcha_generate(rec);
    });

    function captcha_generate(choice) {
        if (choice === 'start_recovery') 
            grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }).then(start_recovery);
        else
            grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }).then(finish_recovery);
    }

    function start_recovery() {
        var pattern = /^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i;
        var data = {
          'type':'recovering',
          'email':$('.email-recovery-field-input').val(),
          'g-recaptcha-response':$("#g-recaptcha-response").val(),
        };
       if (data['email'] !== '') {
            if (data['email'].search(pattern) === 0) {
                $(".email-recovery-field-input").css('border-bottom', '3px solid #eff3f9');
                $('#error-field').text('');
                $.ajax({
                    url: 'handler.php',
                    type: "POST",
                    dataType: "html",
                    data: data,
                    success: function (response) {
                        if (response === 'Undefined email') {
                            $(".email-recovery-field-input").css('border-bottom', '3px solid darkred');
                            $("#error-field").text('Пользователя с такой почтой не существует');
                        } else if (response === 'Spam') {
                            $("#error-field").text('Вы робот!');
                        } else {
                            $(".recovery-page-forms").empty();
                            $(".recovery-page-forms").append("<h3 id='title-field'>Успех</h3>" +
                                "<p>На вашу почту отправлено письмо с последующими инструкциями по восстановлению пароля.</p>" +
                                "<p>Письмо будет действительно в течение <u>3-х дней</u>.</p>" +
                                "<p style='text-align: center; margin-bottom: 0; margin-top: 60px'><a href='/ru/' class='underline-a'>Вернуться на главную страницу</a></p>");
                        }
                    },
                    error: function (response) {
                        console.log('Восстановление не удалось!');
                    }
                })
            } else {
                $(".email-recovery-field-input").css('border-bottom', '3px solid darkred');
                $('#error-field').text('Неправильный Email');
            }
       } else {
           $(".email-recovery-field-input").css('border-bottom', '3px solid darkred');
           $("#error-field").text('Введите Email');
       }
    }
    
    function finish_recovery() {
        var data = {
            'type': 'finish_recovering',
            'new_password': $(".password-field-input").val(),
            'email': $(".hidden-email-field-input").val(),
            'key': $(".hidden-key-field-input").val(),
            'g-recaptcha-response': $("#g-recaptcha-response").val(),
        };
        var repeat = $(".password-repeat-field-input").val();

        if (data['new_password'] === '') {
            $('.password-field-input').css('border-bottom','3px solid darkred');
            $('#error-field').text('Введите новый пароль');
        }
        if (repeat === '') {
            $('.password-repeat-field-input').css('border-bottom','3px solid darkred');
            $('#error-field').text('Повторите пароль');
        }
        if (data['new_password'] === '' && repeat === '') {
            $('.password-field-input').css('border-bottom','3px solid darkred');
            $('.password-repeat-field-input').css('border-bottom','3px solid darkred');
            $('#error-field').text('Заполните указанные поля');
        }
        if (data['new_password'] !== repeat && data['new_password'] !== '' && repeat !== '') {
            $('.password-field-input').css('border-bottom','3px solid darkred');
            $('.password-repeat-field-input').css('border-bottom','3px solid darkred');
            $('#error-field').text('Пароли не совпадают');
            console.log(data['new_password'], repeat);
        } else {
            $('.password-field-input').css('border-bottom','3px solid #eff3f9');
            $('.password-repeat-field-input').css('border-bottom','3px solid #eff3f9');
            $('#error-field').text('');
            $.ajax({
                url: 'handler.php',
                type: "POST",
                dataType: "html",
                data: data,
                success: function (response) {
                    if (response === 'Spam') {
                        $("#error-field").text('Вы робот!');
                    } else if (response === 'Error key' || response === 'Undefined email') {
                        $(".recovery-page-forms-finish").empty();
                        $(".recovery-page-forms-finish").append("<h3 id='title-field'>Ошибка</h3>" +
                            "<p>Восстановить пароль не удалось, так как либо пароль уже был изменен по этой ссылке, либо время действия ссылки истекло.</p>" +
                            "<p>Обновите страницу и попробуйте снова. При неудаче создайте новую заявку на изменение пароля.</p>" +
                            "<p style='text-align: center; margin-bottom: 0; margin-top: 60px'><a href='/labs/kursach/recovery/' class='underline-a'>Перейти к восстановлению</a></p>");
                    } else if (response === 'Old password') {
                        $('.password-field-input').css('border-bottom','3px solid darkred');
                        $('.password-repeat-field-input').css('border-bottom','3px solid darkred');
                        $("#error-field").text('Вы ввели старый пароль!');
                    } else {
                        $(".recovery-page-forms-finish").empty();
                        $(".recovery-page-forms-finish").css('min-height','335px');
                        $(".recovery-page-forms-finish").append("<h3 id='title-field'>Успех</h3>" +
                            "<p>Ваш пароль был успешно изменен.</p>" +
                            "<p>Вам на почту было отправлено сообщение с вашим новым паролем.</p>" +
                            "<p style='text-align: center; margin-bottom: 0; margin-top: 60px'><a href='/labs/kursach/autorization/' class='underline-a'>Перейти к авторизации</a></p>");
                    }
                },
                error: function (response) {
                    console.log('Восстановление не удалось!');
                }
            });
        }
    }
</script>