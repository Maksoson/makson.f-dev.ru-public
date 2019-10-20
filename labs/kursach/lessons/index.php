<?
namespace Makson;
//include $_SERVER["DOCUMENT_ROOT"]."/header.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="КубГУ, расписание, ФКТиПМ, Краснодар, KubSU">
    <meta name="description" content="Расписание ФКТиПМ Кубанского государственного университета">
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <title>Расписание</title>
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
<body class="timeTableBody">
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
                    </ul>
                </li>
            <?php else:?>
                <li><a href="/labs/kursach/lessons/">Расписание</a></li>
            <?php endif;?>
            <li><a href="/labs/testlaba/ru/">Тест зона</a></li>
        </ul>
    </nav>
</header>
<div class="fancy">
    <form onsubmit="return false" autocomplete="off">
        <div id="fancy-inputs">
            <label class="input">
                <input type="text" id="inputGroup">
                <span><span>Введите группу</span></span><span class="close"><i class="fas fa-times"></i></span>
                <div class="dialog"></div>
            </label>
            <button id="btnSearch" type="button"><i class="fas fa-search"></i></button>
<!--            <button id="loadTimetable" type="button"><i class="far fa-calendar-plus"></i></button>-->
        </div>
        <div class="radio-inputs">
            <div id="fancy-radio">
                <input type="radio" name="group" id="groups" class="pull-left">
                <label class="radio groups selected" for="groups">Группа</label>

                <input type="radio" name="teacher" id="teachers" class="pull-left">
                <label class="radio teachers" for="teachers">Преподаватель</label>
            </div>
        </div>
        <div>
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
        </div>
    </form>
</div>

<?php if(isset($_SESSION["session_username"])): if ($_SESSION["session_username"] == "Maksoson"): ?>
<div class="insert-timetable">
    <form id="upload-container">
        <img id="upload-image" src="https://habrastorage.org/webt/dr/qg/cs/drqgcsoh1mosho2swyk3kk_mtwi.png">
        <div>
            <input id="file-input" type="file" name="file" multiple>
            <label for="file-input">Выберите файл</label>
            <span>или перетащите его сюда</span>
        </div>
    </form>
</div>
<?php endif; endif; ?>

<div class="parity_weeks">
    <button id="first_week">1-я неделя</button>
    <button id="second_week">2-я неделя</button>
</div>

<div class="tables">
    <div class="parityNowInfo"><p class="parityNow"></p></div>
    <div id="teacherTableBlock"></div>
    <div class="swiper-container">
        <div class="swiper-wrapper"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>

<div class="modal_form">
    <span class="modal_close"><i class="fas fa-sign-out-alt"></i></span>
    <p class="whatDay"></p>
    <p class="whatTeacher"></p>
    <p class="whatSubgroup"></p>
    <p class="whatCabinet"></p>
    <p class="whatSubject"></p>
    <p class="whatTime"></p>
    <p class="whatType"></p>
    <p class="whatParity"></p>
    <?php if (isset($_SESSION['session_username'])):?>
        <button id="addLesson" style="display: none; width: 50%;">Добавить</button>
        <button id="updateLesson">Изменить</button>
        <button id="deleteLesson">Удалить</button>
    <?php endif; ?>
</div>

<div class="overlay"></div>

<div class="please-wait"><p class="please-wait-text"></p></div>

<div class="bottom"></div>

</body>

<script src="https://www.google.com/recaptcha/api.js?render=6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt"></script>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="/chosen.jquery.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/js/swiper.min.js"></script>

<script>
    var lastGroupNum = '';
    var lastTeacher = '';
    var globalParity = 1;
    var timesArray = ['8:00 - 9:30','9:40 - 11:10','11:30 - 13:00','13:10 - 14:40','15:00 - 16:30','16:40 - 18:10','18:20 - 19:50'];
    var alreadyFilled = false;
    var groups = [];
    var teachers = [];
    var states = [];
    var now_day;
    var now_count = 0;
    var files;

    var dropZone = $('#upload-container');
    var swiper = undefined;

    $(document).on({
        ajaxStart: function () { $('body').addClass("loading"); },
        ajaxStop: function () { $('body').removeClass("loading"); }
    });

    function size_for_swiper() {
        var screenWidth = window.innerWidth;
        if (screenWidth < 1800 && screenWidth > 1024 && now_count !== 2) {
            var teacherStat = document.querySelector('label.radio.teachers');
            console.log(screenWidth, 2);
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 2,
                spaceBetween: 0,
                slidesPerGroup: 2,
                // loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 2;
            swiper.update();
            swiper.slideToLoop(now_day, 600, false);
            if (teacherStat.classList.contains('selected')) {
                $(".swiper-container").css('display', 'none');
                $("#teacherTableBlock").css('display', 'flex');
            }
        } else if (screenWidth < 1025 && now_count !== 1) {
            var teacherStat = document.querySelector('label.radio.teachers');
            console.log(screenWidth, 1);
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                slidesPerGroup: 1,
                // loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 1;
            swiper.update();
            swiper.slideToLoop(now_day, 600, false);
            if (teacherStat.classList.contains('selected')) {
                $(".swiper-container").css('display', 'flex');
                $("#teacherTableBlock").css('display', 'none');
            }
        } else if (screenWidth > 1799 && now_count !== 3) {
            var teacherStat = document.querySelector('label.radio.teachers');
            console.log(screenWidth, 3);
            if (swiper !== undefined)
                swiper.destroy();
            swiper = new Swiper('.swiper-container', {
                slidesPerView: 3,
                spaceBetween: 0,
                slidesPerGroup: 3,
                // loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            now_count = 3;
            swiper.update();
            swiper.slideToLoop(now_day, 600, false);
            if (teacherStat.classList.contains('selected')) {
                $(".swiper-container").css('display', 'none');
                $("#teacherTableBlock").css('display', 'flex');
            }
        }
    }

    $(window).on('resize', function(){
        size_for_swiper();
    });

    $('button').on('click', function () {
        $('.insert-timetable').fadeOut(250, function () {
            $('.insert-timetable').css('display','none');
        });
    });

    function groupSearch(parity) {
        var check = false;
        var info = $('#inputGroup').val();
        var elem = document.querySelector('label.radio.groups');
        if (elem.classList.contains("selected")) {
            if (info !== '')
                lastGroupNum = info;
            if (lastGroupNum !== '') {
                var data = {
                    'type': 'groupSearch',
                    'group': lastGroupNum,
                    'parity': parity,
                    'g-recaptcha-response': $("#g-recaptcha-response").val()
                };
                check = true;
            }
        } else {
            if (info !== '')
                lastTeacher = info;
            if (lastTeacher !== '') {
                var data = {
                    'type': 'teacherSearch',
                    'teacher': lastTeacher,
                    'parity': parity,
                    'g-recaptcha-response': $("#g-recaptcha-response").val()
                };
                check = true;
            }
        }
        if (groups.indexOf(info) != -1 || teachers.indexOf(info) != -1) {
            if (check) {
                $.ajax({
                    url: 'handler.php',
                    type: "POST",
                    dataType: "html",
                    data: data,
                    success: function (response) {
                        $(".bottom").css('display', 'flex');
                        result = $.parseJSON(response);
                        now_day = result.now_day;
                        console.log(result.par);
                        $(".swiper-wrapper").empty();
                        $("#teacherTableBlock").empty();
                        $(".parityNow").empty();
                        if (response === 'Spam') {
                            $(".parity-weeks").css('display', 'flex');
                        } else if (response === 'Undefined teacher') {
                            $("#inputGroup").css('border-bottom', '3px solid darkred');
                        } else {
                            console.log("Success");
                            $(".tables").css('display', 'flex');
                            if (data['type'] === 'groupSearch') {
                                $(".parityNow").append(lastGroupNum + " группа | " + parity + "-я неделя");
                                $(".swiper-wrapper").append(result.content);
                                $(".swiper-container").css('display', 'flex');
                                $("#teacherTableBlock").css('display', 'none')
                            } else if (data['type'] === 'teacherSearch') {
                                $(".parityNow").append(lastTeacher + " | " + parity + "-я неделя");
                                $("#teacherTableBlock").append(result.content);
                                $(".swiper-wrapper").append(result.swiper_content);
                                // $("#teacherTableBlock").css('display', 'flex');
                                // $(".swiper-container").css('display', 'none');
                            }
                            if (swiper !== undefined) {
                                console.log('tut');
                                swiper.update();
                                swiper.slideToLoop(now_day, 600, false);
                            }
                            $(".parity_weeks").css('display', 'flex');
                        }
                        $('#inputGroup').css('border-bottom', '3px solid #ccc');
                        $(".parity_weeks").css('display', 'flex');
                    },
                    error: function () {
                        alert('Ошибка. Данные не переданы');
                    }
                }).then(size_for_swiper);
            } else {
                $('#inputGroup').css('border-bottom', '3px solid darkred');
            }
        } else {
            $('#inputGroup').css('border-bottom', '3px solid darkred');
        }
    }

    $("#btnSearch, #first_week").click(function () {
        globalParity = 1;
        captcha_generate(globalParity);
    });

    $("#second_week").click(function () {
        globalParity = 2;
        captcha_generate(globalParity);
    });

    function search_with_captcha_first () {
        groupSearch(1);
    }

    function search_with_captcha_second () {
        groupSearch(2);
    }

    function captcha_generate(parity) {
        if (parity === 1)
            grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }).then(search_with_captcha_first);
        else
        if (parity === 2)
            grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }).then(search_with_captcha_second);
    }

    function modal(id, day, parity, subgroup, type_lesson, parity_info, number, groupID) {
        $(".tableplace").on('click', '#' + id, function(event){
            $('.overlay').css('pointer-events','none');
            $('.modal_close').css('pointer-events','none');
            event.preventDefault();
            $('.overlay').fadeIn(400,
                function(){
                    $('.modal_form').css('display', 'flex').animate({opacity: 1, top: '50%'}, 50);
                    $('.whatDay').replaceWith('<p class="whatDay" id="whatDay' +  id + '">' + $('#table' + day + ' #tableDay').text() + '</p>');
                    $('.whatTeacher').replaceWith('<p class="whatTeacher" id="whatTeacher' + id + '">' + $('#teacher' + id).text() + '</p>');
                    if (subgroup === 1) {
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    }else if (subgroup === 2){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    } else if (subgroup === 3){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + 'Общая' + '</p>');
                    }
                    $('.whatCabinet').replaceWith('<p class="whatCabinet" id="whatCabinet' + id + '">' + $('#cabinet' + id).text() + '</p>');
                    $('.whatSubject').replaceWith('<p class="whatSubject" id="whatSubject' + id + '">' + $('#subject' + id).text() + '</p>');
                    $('.whatTime').replaceWith('<p class="whatTime" id="whatTime' + id + '">' + (number + 1) + ' пара (' + timesArray[number] + ')</p>');
                    if (type_lesson === 1) {
                        $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '">' + 'Лекция' + '</p>');
                    }else if (type_lesson === 2){
                        $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '">' + 'Практика' + '</p>');
                    }
                    if (parity_info === 1) {
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + '1-я неделя' + '</p>');
                    } else if (parity_info === 2){
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + '2-я неделя' + '</p>');
                    } else if (parity_info === 3){
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + 'Обе недели' + '</p>');
                    }
                    $('#updateLesson').replaceWith('<button id="updateLesson" onclick="updateLessonInDB(' + id + ',' + parity + ',' + groupID + ',' +
                        '' + day + ',' + subgroup + ',' + type_lesson + ',' + number + ')">Изменить</button>');
                    $('#deleteLesson').replaceWith('<button id="deleteLesson" onclick="deleteLessonFromDB(' + id + ',' + parity +')" >Удалить</button>');
                    $('.overlay').css('pointer-events','auto');
                    $('.modal_close').css('pointer-events','auto');
                });
        });

        $(".tableplace").on('click', '#place' + id, function(event){
            $('.overlay').css('pointer-events','none');
            $('.modal_close').css('pointer-events','none');
            event.preventDefault();
            $('.overlay').fadeIn(400,
                function(){
                    $('.modal_form').css('display', 'flex').animate({opacity: 1, top: '50%'}, 50);
                    $('.whatDay').replaceWith('<p class="whatDay" id="whatDay' +  id + '">' + $('#table' + day + ' #tableDay').text() + '</p>');
                    $('.whatTeacher').replaceWith('<p class="whatTeacher" id="whatTeacher' + id + '">' + $('#teacher' + id).text() + '</p>');
                    if (subgroup === 1) {
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    }else if (subgroup === 2){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    } else if (subgroup === 3){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + 'Общая' + '</p>');
                    }
                    $('.whatCabinet').replaceWith('<p class="whatCabinet" id="whatCabinet' + id + '">' + $('#cabinet' + id).text() + '</p>');
                    $('.whatSubject').replaceWith('<p class="whatSubject" id="whatSubject' + id + '">' + $('#subject' + id).text() + '</p>');
                    $('.whatTime').replaceWith('<p class="whatTime" id="whatTime' + id + '">' + (number + 1) + ' пара (' + timesArray[number] + ')</p>');
                    if (type_lesson === 1) {
                        $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '">' + 'Лекция' + '</p>');
                    }else if (type_lesson === 2){
                        $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '">' + 'Практика' + '</p>');
                    }
                    if (parity_info === 1) {
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + '1-я неделя' + '</p>');
                    } else if (parity_info === 2){
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + '2-я неделя' + '</p>');
                    } else if (parity_info === 3){
                        $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '">' + 'Обе недели' + '</p>');
                    }
                    <?php if (isset($_SESSION['session_username'])):?>
                    $('#updateLesson').replaceWith('<button id="updateLesson" onclick="updateLessonInDB(' + id + ',' + parity + ',' + groupID + ',' +
                        '' + day + ',' + subgroup + ',' + type_lesson + ',' + number + ')">Изменить</button>');
                    $('#deleteLesson').replaceWith('<button id="deleteLesson" onclick="deleteLessonFromDB(' + id + ',' + parity +')" >Удалить</button>');
                    <? endif; ?>
                    $('.overlay').css('pointer-events','auto');
                    $('.modal_close').css('pointer-events','auto');
                });
        });

        $(".tableplace").on('click', '#emptyinfo' + id, function (event) {
            $('.overlay').css('pointer-events','none');
            $('.modal_close').css('pointer-events','none');
            event.preventDefault();
            $('.overlay').fadeIn(400,
                function () {
                    $('.modal_form').css('display', 'flex').animate({opacity: 1, top: '50%'}, 50);
                    $('.whatDay').replaceWith('<p class="whatDay" id="whatDay' +  id + '">' + $('#table' + day + ' #tableDay').text() + '</p>');
                    $('.whatTeacher').replaceWith('<p class="whatTeacher" id="whatTeacher' + id + '" style="color: #cccccc">Преподаватель</p>');
                    if (subgroup === 1) {
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    }else if (subgroup === 2){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    } else if (subgroup === 3){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + 'Общая' + '</p>');
                    }
                    $('.whatCabinet').replaceWith('<p class="whatCabinet" id="whatCabinet' + id + '" style="color: #cccccc">Ауд</p>');
                    $('.whatSubject').replaceWith('<p class="whatSubject" id="whatSubject' + id + '" style="font-size: 14px; font-weight: bold;">' + 'Пары нет' + '</p>');
                    $('.whatTime').replaceWith('<p class="whatTime" id="whatTime' + id + '">' + (number + 1) + ' пара (' + timesArray[number] + ')</p>');
                    $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '" style="color: #cccccc">' + 'Тип' + '</p>');
                    $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '" style="color: #cccccc">' + 'Неделя' + '</p>');
                    <?php if (isset($_SESSION['session_username'])):?>
                    $('#addLesson').replaceWith('<button id="addLesson" onclick="addLessonInDB(' + id + ', ' + day + ',' + parity + ', ' + subgroup + ',' + number + ',' + groupID + ')" style="display: inline-block; width: 50%">Добавить</button>');
                    $('#updateLesson').css('display','none');
                    $('#deleteLesson').css('display','none');
                    <?php endif; ?>
                    $('.overlay').css('pointer-events','auto');
                    $('.modal_close').css('pointer-events','auto');
                });
        });

        $(".tableplace").on('click', '#emptyplace' + id, function (event) {
            $('.overlay').css('pointer-events','none');
            $('.modal_close').css('pointer-events','none');
            event.preventDefault();
            $('.overlay').fadeIn(400,
                function () {
                    $('.modal_form').css('display', 'flex').animate({opacity: 1, top: '50%'}, 50);
                    $('.whatDay').replaceWith('<p class="whatDay" id="whatDay' +  id + '">' + $('#table' + day + ' #tableDay').text() + '</p>');
                    $('.whatTeacher').replaceWith('<p class="whatTeacher" id="whatTeacher' + id + '" style="color: #cccccc">Преподаватель</p>');
                    if (subgroup === 1) {
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    }else if (subgroup === 2){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + subgroup + ' пг' + '</p>');
                    } else if (subgroup === 3){
                        $('.whatSubgroup').replaceWith('<p class="whatSubgroup" id="whatSubgroup' + id + '">' + 'Общая' + '</p>');
                    }
                    $('.whatCabinet').replaceWith('<p class="whatCabinet" id="whatCabinet' + id + '" style="color: #cccccc">Ауд</p>');
                    $('.whatSubject').replaceWith('<p class="whatSubject" id="whatSubject' + id + '" style="font-size: 14px; font-weight: bold;">' + 'Пары нет' + '</p>');
                    $('.whatTime').replaceWith('<p class="whatTime" id="whatTime' + id + '">' + (number + 1) + ' пара (' + timesArray[number] + ')</p>');
                    $('.whatType').replaceWith('<p class="whatType" id="whatType' + id + '" style="color: #cccccc">' + 'Тип' + '</p>');
                    $('.whatParity').replaceWith('<p class="whatParity" id="whatParity' + id + '" style="color: #cccccc">' + 'Неделя' + '</p>');
                    <?php if (isset($_SESSION['session_username'])):?>
                    $('#addLesson').replaceWith('<button id="addLesson" onclick="addLessonInDB(' + id + ', ' + day + ',' + parity + ', ' + subgroup + ',' + number + ',' + groupID + ')" style="display: inline-block; width: 50%">Добавить</button>');
                    $('#updateLesson').css('display','none');
                    $('#deleteLesson').css('display','none');
                    <?php endif;?>
                    $('.overlay').css('pointer-events','auto');
                    $('.modal_close').css('pointer-events','auto');
                });
        });

        $('.modal_close, .overlay').click( function(){
            $('.overlay').css('pointer-events','none');
            $('.modal_close').css('pointer-events','none');
            $('.modal_form')
                .animate({opacity: 0, top: '45%'}, 50,
                    function(){
                        $(this).css('display', 'none');
                        $('.overlay').fadeOut(400);
                        <?php if (isset($_SESSION['session_username'])):?>
                        $('#addLesson').css('display','none');
                        $('#updateLesson').css('display','flex');
                        $('#deleteLesson').css('display','flex');
                        <?php endif; ?>
                        $('.overlay').css('pointer-events','auto');
                        $('.modal_close').css('pointer-events','auto');
                    }
                );
        });
    }

    function addLessonInDB(id, day, parity, subgroup, number, groupID) {
        var data = {'type':'demoAdd', 'id': id, 'day': day, 'subgroup': subgroup, 'number': number, 'parity-global': parity, 'groupID': groupID};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                console.log("Success");
                $(".modal_form").empty();
                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span>');
                $(".modal_form").append(response);
                $(".selectWhatTeacher").chosen({display_disabled_options: false});
                $(".selectWhatSubgroup").chosen({display_disabled_options: false});
                $(".selectWhatCabinet").chosen({display_disabled_options: false});
                $(".selectWhatSubject").chosen({display_disabled_options: false});
                $(".selectWhatType").chosen({display_disabled_options: false});
                $(".selectWhatParity").chosen({display_disabled_options: false});

                $('.modal_close, .overlay').click( function(){
                    $('.modal_form')
                        .animate({opacity: 0, top: '45%'}, 50,
                            function(){
                                $(this).css('display', 'none');
                                $('.overlay').fadeOut(400);
                                console.log("Exited");
                                $(".modal_form").empty();
                                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                                    '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                                    '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                                    '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                                    '<button id="deleteLesson" >Удалить</button>');
                                $('.overlay').css('pointer-events','auto');
                                $('.modal_close').css('pointer-events','auto');
                            }
                        );
                });
            },
            error: function () {
                alert('Ошибка. Данные не переданы');
            }
        });
    }

    function save(day, global_parity, subgroup, number, groupID) {
        if (subgroup !== 3) {
            var data =
                {
                    'type': 'insert', 'group': groupID, 'day': day,
                    'parity': $(".divParity span").html(), 'types': $(".divTypes span").html(),
                    'numbers': number + 1, 'subjects': $(".divSubjects span").html(),
                    'teachers': $(".divTeachers span").html(), 'subs': subgroup,
                    'cabinets': $(".divCabinets span").html()
                };
        } else {
            var data = {
                'type': 'insert', 'group': groupID, 'day': day,
                'parity': $(".divParity span").html(), 'types': $(".divTypes span").html(),
                'numbers': number + 1, 'subjects': $(".divSubjects span").html(),
                'teachers': $(".divTeachers span").html(), 'subs': $(".divSubgroups span").html(),
                'cabinets': $(".divCabinets span").html()
            };
        }
        console.log(data['teachers']);
        console.log(data['cabinets']);
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                $('.overlay').css('pointer-events', 'none');
                $('.modal_close').css('pointer-events', 'none');
                $('.modal_form')
                    .animate({opacity: 0, top: '45%'}, 50,
                        function () {
                            $(this).css('display', 'none');
                            $('.overlay').fadeOut(400);
                            $('.overlay').css('pointer-events', 'auto');
                            $('.modal_close').css('pointer-events', 'auto');
                        }
                    );
                captcha_generate(1);
                $(".modal_form").empty();
                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                    '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                    '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                    '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                    '<button id="deleteLesson" >Удалить</button>');
                // var data_new = {'type': 'groupSearch', 'group': groupNum, 'parity': global_parity};
                // $.ajax({
                //     url: 'handler.php',
                //     type: "POST",
                //     dataType: "html",
                //     data: data_new,
                //     success: function (response) {
                //         console.log("Success");
                //         $(".tables").empty();
                //         $(".tables").append(response);
                //         $(".modal_form").empty();
                //         $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                //             '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                //             '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                //             '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                //             '<button id="deleteLesson" >Удалить</button>');
                //         $(".parity_weeks").css('display', 'flex');
                //     },
                //     error: function () {
                //         alert('Ошибка. Данные не переданы');
                //     }
                // });
            },
            error: function () {
                alert('Ошибка. Данные не переданы');
            }
        });
    }

    function cancel() {
        $('.overlay').css('pointer-events','none');
        $('.modal_close').css('pointer-events','none');
        $('.modal_form')
            .animate({opacity: 0, top: '45%'}, 50,
                function() {
                    $(this).css('display', 'none');
                    $('.overlay').fadeOut(400);
                    $(".modal_form").empty();
                    $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                        '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                        '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                        '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                        '<button id="deleteLesson" >Удалить</button>');
                    $('.overlay').css('pointer-events','auto');
                    $('.modal_close').css('pointer-events','auto');
                });
    }

    function updateLessonInDB(id, parity, groupID, day, subgroup, type_lesson, number) {
        var old_data = {
            'type': 'demoUpdate', 'id': id, 'parity': $('.whatParity').text(), 'groupID': groupID, 'day': day, 'subgroup': $('.whatSubgroup').text(),
            'type_lesson': $('.whatType').text(), 'number': number, 'subject': $('.whatSubject').text(), 'teacher': $('.whatTeacher').text(), 'cabinet': $('.whatCabinet').text()
        };

        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: old_data,
            success: function (response) {
                $(".modal_form").empty();
                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span>');
                $(".modal_form").append(response);
                $(".selectWhatTeacher").chosen({display_disabled_options: false});
                $(".selectWhatSubgroup").chosen({display_disabled_options: false});
                $(".selectWhatCabinet").chosen({display_disabled_options: false});
                $(".selectWhatSubject").chosen({display_disabled_options: false});
                $(".selectWhatType").chosen({display_disabled_options: false});
                $(".selectWhatParity").chosen({display_disabled_options: false});

                $('.modal_close, .overlay').click( function(){
                    $('.modal_form')
                        .animate({opacity: 0, top: '45%'}, 50,
                            function(){
                                $(this).css('display', 'none');
                                $('.overlay').fadeOut(400);
                                console.log("Exited");
                                $(".modal_form").empty();
                                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                                    '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                                    '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                                    '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                                    '<button id="deleteLesson" >Удалить</button>');
                                $('.overlay').css('pointer-events','auto');
                                $('.modal_close').css('pointer-events','auto');
                            }
                        );
                });

                $('#updateLesson').click(function () {
                    var new_data = {
                        'type': 'demoUpdate',
                        'id': id,
                        'parity': $('.divParity span').text(),
                        'groupID': groupID,
                        'day': day,
                        'subgroup': $('.divSubgroups span').text(),
                        'type_lesson': $('.divTypes span').text(),
                        'number': number,
                        'subject': $('.divSubjects span').text(),
                        'teacher': $('.divTeachers span').text(),
                        'cabinet': $('.divCabinets span').text()
                    };
                    if (old_data !== new_data){
                        new_data = {
                            'type': 'Update',
                            'id': id,
                            'parity': $('.divParity span').text(),
                            'groupID': groupID,
                            'day': day,
                            'subgroup': $('.divSubgroups span').text(),
                            'type_lesson': $('.divTypes span').text(),
                            'number': number + 1,
                            'subject': $('.divSubjects span').text(),
                            'teacher': $('.divTeachers span').text(),
                            'cabinet': $('.divCabinets span').text()
                        };
                        console.log(new_data);
                        $.ajax({
                            url: 'handler.php',
                            type: "POST",
                            dataType: "html",
                            data: new_data,
                            success: function (response) {
                                $('.overlay').css('pointer-events','none');
                                $('.modal_close').css('pointer-events','none');
                                $('.modal_form')
                                    .animate({opacity: 0, top: '45%'}, 50,
                                        function(){
                                            $(this).css('display', 'none');
                                            $('.overlay').fadeOut(400);
                                            $('.overlay').css('pointer-events','auto');
                                            $('.modal_close').css('pointer-events','auto');
                                        }
                                    );
                                captcha_generate(1);
                                $(".modal_form").empty();
                                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                                    '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                                    '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                                    '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                                    '<button id="deleteLesson" >Удалить</button>');
                                // var data_new = {'type': 'groupSearch', 'group': groupNum, 'parity': parity};
                                // $.ajax({
                                //     url: 'handler.php',
                                //     type: "POST",
                                //     dataType: "html",
                                //     data: data_new,
                                //     success: function (response) {
                                //         console.log("Success");
                                //         $(".tables").empty();
                                //         $(".tables").append(response);
                                //         $(".modal_form").empty();
                                //         $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                                //             '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                                //             '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                                //             '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                                //             '<button id="deleteLesson" >Удалить</button>');
                                //         $(".parity_weeks").css('display', 'flex');
                                //     },
                                //     error: function () {
                                //         alert('Ошибка. Данные не переданы');
                                //     }
                                // });
                            },
                            error: function () {
                                alert('Ошибка. Данные не переданы');
                            }
                        });
                    }
                })
            },
            error: function (response) {
                alert('Ошибка. Данные не переданы');
            }
        });
    }

    function deleteLessonFromDB(id, parity) {
        var data = {'type': 'deleteLesson', 'id': id};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                $('.overlay').css('pointer-events','none');
                $('.modal_close').css('pointer-events','none');
                console.log("Lesson" + id + " - deleted!");
                $('.modal_form')
                    .animate({opacity: 0, top: '45%'}, 50,
                        function(){
                            $(this).css('display', 'none');
                            $('.overlay').fadeOut(400);
                            $('.overlay').css('pointer-events','auto');
                            $('.modal_close').css('pointer-events','auto');
                        }
                    );
                captcha_generate(1);
                $(".modal_form").empty();
                $(".modal_form").append('<span class="modal_close"><i class="fas fa-sign-out-alt"></i></span><p class="whatDay"></p>' +
                    '<p class="whatTeacher"></p> <p class="whatSubgroup"></p> <p class="whatCabinet"></p> <p class="whatSubject"></p> ' +
                    '<p class="whatTime"></p> <p class="whatType"></p> <p class="whatParity"></p> ' +
                    '<button id="addLesson" style="display: none; width: 50%;">Добавить</button> <button id="updateLesson">Изменить</button> ' +
                    '<button id="deleteLesson" >Удалить</button>');
                // var data_new = {'type': 'groupSearch', 'group': groupNum, 'parity': parity};
                // $.ajax({
                //     url: 'handler.php',
                //     type: "POST",
                //     dataType: "html",
                //     data: data_new,
                //     success: function (response) {
                //         $(".tables").empty();
                //         $(".tables").append(response);
                //         $(".parity_weeks").css('display', 'flex');
                //     },
                //     error: function () {
                //         alert('Ошибка. Данные не переданы');
                //     }
                // });
            },
            error: function () {
                alert('Ошибка. Данные не удалены');
            }
        });
    }

    $('#fancy-inputs input[type="text"]').blur(function(){
        if($(this).val().length > 0){
            $(this).addClass('white');
        } else {
            $(this).removeClass('white');
        }
    });

    window.onload = function () {
        var data = {'type':'autocomplete'};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                result = $.parseJSON(response);
                groups = result.groups;
                teachers = result.teachers;
                states = groups;
                for (var i = 0; i < Object.keys(states).length; i++) {
                    $('.dialog').append('<div id="autoGroups' + i + '">' + states[i] + '</div>');
                }
            },
            error: function () {

            }
        });
    };

    function clearDialog() {
        $('.dialog').empty();
    }

    $('#fancy-inputs input').click(function() {
        if (!alreadyFilled) {
            $('.dialog').addClass('open');
        }
    });

    $('html').on('click', '.dialog > div', function() {
        $('#fancy-inputs input').val($(this).text()).focus();
        $('#fancy-inputs .close').addClass('visible');
        $('#fancy-inputs input').addClass('white');
        var elem = document.querySelector('.dialog');
        if (elem.classList.contains('open'))
            $('.dialog').removeClass('open');
        alreadyFilled = true;
    });

    $('span.close').click(function() {
        alreadyFilled = false;
        $('.dialog').addClass('open');
        $('#fancy-inputs input').val('').focus();
        $('#fancy-inputs input').removeClass('white');
        $(this).removeClass('visible');
        match('');
    });

    function match(str) {
        str = str.toLowerCase();
        clearDialog();
        for (var i = 0; i < Object.keys(states).length; i++) {
            if (states[i].toLowerCase().startsWith(str)) {
                $('.dialog').append('<div id="autoGroups' + i + '">' + states[i] + '</div>');
            }
        }
    }

    $('#fancy-inputs input').on('input', function() {
        $('.dialog').addClass('open');
        alreadyFilled = false;
        match($(this).val());
    });

    $('html').click(function(e) {
        if (!$(e.target).is("input, .close")) {
            $('.dialog').removeClass('open');
        }
    });

    function swapTables(object) {
        var groupStat = document.querySelector('label.radio.groups');
        var teacherStat = document.querySelector('label.radio.teachers');
        console.log(object);
        if (!object.classList.contains('selected')) {
            console.log(object.classList);
            $('label.radio').removeClass('selected');
            $('#fancy-inputs .close').removeClass('visible');
            $('#fancy-inputs input').removeClass('white');
            $('#fancy-inputs input').val('');
            $('.bottom').css('display', 'none');
            $('span.close').removeClass('visible');
            alreadyFilled = false;
            var inputID = $(object).attr('id');
            if ($(object).is(':checked')) {
                $('.' + inputID).addClass('selected');
            } else {
                $('.' + inputID).removeClass('selected');
            }
            clearDialog();
            now_count = 0;
            // $('#teacherTableBlock').css('display', 'flex');
            // $('.swiper-wrapper').css('display', 'none');
            $('.insert-timetable').fadeOut(250, function () {
                $('.insert-timetable').css('display','none');
                $('.swiper-wrapper').empty();
                $('#teacherTableBlock').empty();
                $(".parityNow").empty();
                $(".parity_weeks").css('display', 'none');
            });
            if (groupStat.classList.contains("selected")) {
                states = groups;
                lastTeacher = '';
                $('.input span span').replaceWith('<span>Введите группу</span>');
                for (var i = 0; i < Object.keys(groups).length; i++) {
                    $('.dialog').append('<div id="autoGroups' + i + '">' + groups[i] + '</div>');
                }
            } else if (teacherStat.classList.contains("selected")) {
                states = teachers;
                lastGroupNum = '';
                $('.input span span').replaceWith('<span>Ф.И.О. преподавателя</span>');
                for (var i = 2; i < Object.keys(teachers).length; i++) {
                    $('.dialog').append('<div id="autoTeacher' + i + '">' + teachers[i] + '</div>');
                }
            }
        } else
            console.log('Selected yet')
    }

    $('#fancy-radio input[type="radio"]').on('click', function () {
        swapTables(this);
    });

    $('#file-input').focus(function() {
        $('label').addClass('focus');
    })
        .focusout(function() {
            $('label').removeClass('focus');
        });

    $('.timeTableBody').on('dragover dragenter', function () {
        $('.insert-timetable').fadeIn(250, function () {
            $('.insert-timetable').css('display','flex');
        });
    });

    $('.timeTableBody').on('drop', function () {
        $('.insert-timetable').fadeOut(250, function () {
            $('.insert-timetable').css('display','none');
        });
    });

    $('.timeTableBody').on('drag dragstart dragend dragover dragenter dragleave drop', function(){
        return false;
    });

    dropZone.on('drag dragstart dragend dragover dragenter dragleave drop', function(){
        return false;
    });

    dropZone.on('dragover dragenter', function() {
        dropZone.fadeIn(250, function () {
            dropZone.addClass('dragover');
        })
    });

    dropZone.on('dragleave', function(e) {
        let dx = e.pageX - dropZone.offset().left;
        let dy = e.pageY - dropZone.offset().top;
        if ((dx < 0) || (dx > dropZone.width()) || (dy < 0) || (dy > dropZone.height())) {
            dropZone.removeClass('dragover');
        }
    });

    dropZone.on('drop', function(e) {
        dropZone.removeClass('dragover');
        let files = e.originalEvent.dataTransfer.files;
        sendFiles(files);
    });

    $('#file-input').change(function() {
        let files = this.files;
        sendFiles(files);
    });

    function sendFiles(files) {
        var data = new FormData();
        $.each(files, function (key, value) {
            data.append(key, value);
        });
        $('.please-wait-text').text('Пожалуйста, подождите...');

        $.ajax({
            url: 'handler.php',
            type: "POST",
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                if (typeof response.error === 'undefined') {
                    files = null;
                    $('.insert-timetable').fadeOut(250, function () {
                        $('.insert-timetable').css('display', 'none');
                    });
                    $('.please-wait-text').text('');
                } else {
                    console.log('ОШИБКИ ОТВЕТА сервера: ' + response.error);
                }
            },
            error: function (jqXHR, textStatus) {
                console.log('ОШИБКИ AJAX запроса: ' + textStatus);
            }
        });
    }

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

    $(document).keydown(function(e){
        switch (e.which) {
            case 9:
                if (globalParity == 1) {
                    globalParity = 2;
                    captcha_generate(globalParity);
                } else {
                    globalParity = 1;
                    captcha_generate(globalParity);
                }
                break;
            case 13:
                captcha_generate(1);
                break;
            case 18:
                let groupStat = document.querySelector('label.radio.groups');
                let object;
                if (groupStat.classList.contains('selected')) {
                    object = $("#teachers")[0];
                } else {
                    object = $("#groups")[0];
                }
                swapTables(object);
                break;
            case 27:
                $('.insert-timetable').fadeOut(250, function () {
                    $('.insert-timetable').css('display', 'none');
                });
                break;
            case 40:
                $('#autoGroups0').addClass('selected');
                break;
        }
    });

</script>
</html>