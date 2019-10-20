<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="КубГУ, расписание, ФКТиПМ, Краснодар, KubSU">
    <meta name="description" content="Расписание ФКТиПМ Кубанского государственного университета">
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <title>Бьютиблог Максончика</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/css/swiper.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/chosen.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
</head>
<body>
<div class="upper">
    <?php if (!isset($_SESSION['session_username'])):?>
    <h3><a href="/labs/kursach/autorization/" id="login">Войти</a></h3>
    <?php else:?>
    <h3><a id="logout">Выйти</a></h3>
    <?php endif?>
</div>
<header>
    <nav>
        <ul class="topmenu">
            <li><a href="/ru/">Главная</a></li>
            <li><a href="/about/ru/">Обо мне</a></li>
            <?php if (isset($_SESSION['session_username'])):?>
            <li class="submenu-link-li"><a href="#" class="submenu-link" style="pointer-events: none; cursor: default">Прочее</a>
                <ul class="submenu">
                    <li><a href="/labs/kursach/ru/">Таблицы</a></li>
                    <li><a href="/labs/kursach/lessons/">Расписание</a></li>
                    <li><a href="/Blog/">Блог</a></li>
                </ul>
            </li>
            <?php else:?>
            <li><a href="/labs/kursach/lessons/">Расписание</a></li>
            <li><a href="/Blog/">Блог</a></li>
            <?php endif;?>
            <li><a href="/labs/testlaba/ru/">Тест зона</a></li>
        </ul>
    </nav>
</header>

<script>
    $("#logout").click(function () {
        var data = {
            'type':'logout',
        };
        console.log('ll');
        $.ajax({
            url: '/handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                if (response === 'Session successfully destroyed')
                    window.location = 'http://makson.f-dev.ru/ru/';
            },
            error: function (response) {
                console.log('Выйти не удалось!');
            }
        })
    });
    

    $('body').on('drag dragstart dragend dragover dragenter dragleave drop', function(){
        return false;
    });
</script>


