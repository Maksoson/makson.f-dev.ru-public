<?php
include $_SERVER["DOCUMENT_ROOT"]."/header.php";
?>

<!--<form method="post" enctype="multipart/form-data">-->
<!--    <label for="filename">ФАЙЛ:</label>-->
<!--    <input id="filename" type="file" name="filename">-->
<!--    <input class="btn" type="submit" value="Загрузить"/>-->
<!--    <div class="ajax-respond"></div>-->
<!--</form>-->

<div class="insert-timetable">
    <form id="upload-container" method="POST">
        <img id="upload-image" src="https://habrastorage.org/webt/dr/qg/cs/drqgcsoh1mosho2swyk3kk_mtwi.png">
        <div>
            <input id="file-input" type="file" name="file" multiple>
            <label for="file-input">Выберите файл</label>
            <span>или перетащите его сюда</span>
        </div>
    </form>
</div>


<div class="please-wait"><p class="please-wait-text">Пожалуйста, подождите...</p></div>

<script>
    var files;

    $(document).on({
        ajaxStart: function () { $('body').addClass("loading"); },
        ajaxStop: function () { $('body').removeClass("loading"); }
    });

    // $(document).ready(function(){
        var dropZone = $('#upload-container');

        $('#file-input').focus(function() {
            $('label').addClass('focus');
        })
            .focusout(function() {
                $('label').removeClass('focus');
            });


        dropZone.on('drag dragstart dragend dragover dragenter dragleave drop', function(){
            return false;
        });

        dropZone.on('dragover dragenter', function() {
            dropZone.addClass('dragover');
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
            $.each( files, function ( key, value ) {
                data.append(key, value);
            });

            $.ajax({
                url: 'handler.php',
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (typeof response.error === 'undefined') {
                        console.log(response);
                        files = null;
                        $('#filename').val('');
                    } else {
                        console.log('ОШИБКИ ОТВЕТА сервера: ' + response.error);
                    }
                },
                error: function (jqXHR, textStatus) {
                    console.log('ОШИБКИ AJAX запроса: ' + textStatus);
                }
            });
        }

</script>

<!-- Swiper JS -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/js/swiper.min.js"></script>-->
<!---->
<!--<div class="fancy">-->
<!--    <form onsubmit="return false" autocomplete="off">-->
<!--        <div id="fancy-inputs">-->
<!--            <label class="input">-->
<!--                <input type="text" id="inputGroup">-->
<!--                <span><span>Введите группу</span></span><span class="close"><i class="fas fa-times"></i></span>-->
<!--                <div class="dialog"></div>-->
<!--            </label>-->
<!--            <button id="btnSearch" type="button"><i class="fas fa-search"></i></button>-->
<!--        </div>-->
<!--        <div class="radio-inputs">-->
<!--            <div id="fancy-radio">-->
<!--                <input type="radio" name="group" id="groups" class="pull-left">-->
<!--                <label class="radio groups" for="groups">Группа</label>-->
<!---->
<!--                <input type="radio" name="teacher" id="teachers" class="pull-left">-->
<!--                <label class="radio teachers" for="teachers">Преподаватель</label>-->
<!--            </div>-->
<!--        </div>-->
<!--      <div>-->
<!--            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">-->
<!--      </div>-->
<!--    </form>-->
<!--</div>-->
<!---->
<!--<div class="parity_weeks">-->
<!--    <button id="first_week">1-я неделя</button>-->
<!--    <button id="second_week">2-я неделя</button>-->
<!--</div>-->
<!---->
<!--<div class="tables" style="display: none">-->
<!--    <div class="parityNowInfo"><p class="parityNow"></p></div>-->
<!--    <div id="teacherTableBlock"></div>-->
<!--    <div class="swiper-container">-->
<!--        <div class="swiper-wrapper">-->
<!---->
<!--        </div>-->
<!--        <div class="swiper-button-next"></div>-->
<!--        <div class="swiper-button-prev"></div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div class="modal_form">-->
<!--    <span class="modal_close"><i class="fas fa-sign-out-alt"></i></span>-->
<!--    <p class="whatDay"></p>-->
<!--    <p class="whatTeacher"></p>-->
<!--    <p class="whatSubgroup"></p>-->
<!--    <p class="whatCabinet"></p>-->
<!--    <p class="whatSubject"></p>-->
<!--    <p class="whatTime"></p>-->
<!--    <p class="whatType"></p>-->
<!--    <p class="whatParity"></p>-->
<!--    --><?php //if (isset($_SESSION['session_username'])):?>
<!--        <button id="addLesson" style="display: none; width: 50%;">Добавить</button>-->
<!--        <button id="updateLesson">Изменить</button>-->
<!--        <button id="deleteLesson">Удалить</button>-->
<!--    --><?php //endif; ?>
<!--</div>-->
<!---->
<!--<div class="overlay"></div>-->
<!---->
<!--<script>-->
<!--    var lastGroupNum = '';-->
<!--    var lastTeacher = '';-->
<!--    var timesArray = ['8:00 - 9:30','9:40 - 11:10','11:30 - 13:00','13:10 - 14:40','15:00 - 16:30','16:40 - 18:10','18:20 - 19:50'];-->
<!--    var alreadyFilled = false;-->
<!--    var groups = [];-->
<!--    var teachers = [];-->
<!--    var states = [];-->
<!--    var now_day;-->
<!--    var now_count = 3;-->
<!---->
<!--    var swiper = new Swiper('.swiper-container', {-->
<!--        slidesPerView: 3,-->
<!--        spaceBetween: 0,-->
<!--        slidesPerGroup: 3,-->
<!--        navigation: {-->
<!--            nextEl: '.swiper-button-next',-->
<!--            prevEl: '.swiper-button-prev',-->
<!--        },-->
<!--    });-->
<!---->
<!--    function size_for_swiper() {-->
<!--        var screenWidth = $(window).width();-->
<!--        if (screenWidth < 1800 && screenWidth > 1024 && now_count !== 2) {-->
<!--            console.log(screenWidth);-->
<!--            swiper.destroy();-->
<!--            $('.swiper-container').css('display', 'flex');-->
<!--            swiper = new Swiper('.swiper-container', {-->
<!--                slidesPerView: 2,-->
<!--                spaceBetween: 0,-->
<!--                slidesPerGroup: 2,-->
<!--                navigation: {-->
<!--                    nextEl: '.swiper-button-next',-->
<!--                    prevEl: '.swiper-button-prev',-->
<!--                },-->
<!--            });-->
<!--            now_count = 2;-->
<!--            swiper.slideTo(now_day, 600, false);-->
<!--        } else if (screenWidth < 1025 && now_count !== 1) {-->
<!--            console.log(screenWidth);-->
<!--            swiper.destroy();-->
<!--            swiper = new Swiper('.swiper-container', {-->
<!--                slidesPerView: 1,-->
<!--                spaceBetween: 0,-->
<!--                slidesPerGroup: 1,-->
<!--                navigation: {-->
<!--                    nextEl: '.swiper-button-next',-->
<!--                    prevEl: '.swiper-button-prev',-->
<!--                },-->
<!--            });-->
<!--            now_count = 1;-->
<!--            swiper.slideTo(now_day, 600, false);-->
<!--        } else if (screenWidth > 1799 && now_count !== 3) {-->
<!--            console.log(screenWidth);-->
<!--            swiper.destroy();-->
<!--            swiper = new Swiper('.swiper-container', {-->
<!--                slidesPerView: 3,-->
<!--                spaceBetween: 0,-->
<!--                slidesPerGroup: 3,-->
<!--                navigation: {-->
<!--                    nextEl: '.swiper-button-next',-->
<!--                    prevEl: '.swiper-button-prev',-->
<!--                },-->
<!--            });-->
<!--            now_count = 3;-->
<!--            swiper.slideTo(now_day, 600, false);-->
<!--        }-->
<!--    }-->
<!---->
<!--    window.onload = function () {-->
<!--        size_for_swiper();-->
<!--    };-->
<!---->
<!--    $(window).on('resize', function(){-->
<!--        size_for_swiper();-->
<!--    });-->
<!---->
<!--    function groupSearch(parity) {-->
<!--        var check = true;-->
<!--        var info = $('#inputGroup').val();-->
<!--        var data = {-->
<!--            'type': 'groupSearch',-->
<!--            'group': info,-->
<!--            'parity': parity,-->
<!--            // 'g-recaptcha-response': $("#g-recaptcha-response").val()-->
<!--        };-->
<!--        console.log(data);-->
<!--        if (check) {-->
<!--            $.ajax({-->
<!--                url: 'handler.php',-->
<!--                type: "POST",-->
<!--                dataType: "html",-->
<!--                data: data,-->
<!--                success: function (response) {-->
<!--                    result = $.parseJSON(response);-->
<!--                    now_day = result.now_day;-->
<!--                    console.log(result.sw);-->
<!--                    $(".swiper-wrapper").empty();-->
<!--                    if (response === 'Spam') {-->
<!--                        $(".parity-weeks").css('display', 'flex');-->
<!--                    } else if (response === 'Undefined teacher') {-->
<!--                        $("#inputGroup").css('border-bottom', '3px solid darkred');-->
<!--                    } else {-->
<!--                        console.log("Success");-->
<!--                        $(".tables").css('display', 'flex');-->
<!--                        $(".swiper-slide").css('margin-top', 0);-->
<!--                        $(".parityNow").append(info + " группа | " + parity + "-я неделя");-->
<!--                        $(".swiper-wrapper").append(result.swiper_content);-->
<!--                        $(".parity_weeks").css('display', 'flex');-->
<!--                        swiper.update();-->
<!--                        swiper.slideTo(now_day, 600, false);-->
<!--                    }-->
<!--                    $('#inputGroup').css('border-bottom', '3px solid #ccc');-->
<!--                    $(".parity_weeks").css('display', 'flex');-->
<!--                },-->
<!--                error: function () {-->
<!--                    alert('Ошибка. Данные не переданы');-->
<!--                }-->
<!--            });-->
<!--        } else {-->
<!--            $('#inputGroup').css('border-bottom', '3px solid darkred');-->
<!--        }-->
<!--    }-->
<!---->
<!--    $("#btnSearch").click(function () {-->
<!--        groupSearch(1);-->
<!--    });-->
<!---->
<!--    $('#fancy-inputs input[type="text"]').blur(function(){-->
<!--        if($(this).val().length > 0){-->
<!--            $(this).addClass('white');-->
<!--        } else {-->
<!--            $(this).removeClass('white');-->
<!--        }-->
<!--    });-->
<!---->
<!---->
<!--    function clearDialog() {-->
<!--        $('.dialog').empty();-->
<!--    }-->
<!---->
<!--    $('#fancy-inputs label.input').click(function() {-->
<!--        if (!alreadyFilled) {-->
<!--            $('.dialog').addClass('open');-->
<!--        }-->
<!--    });-->
<!---->
<!--    $('html').on('click', '.dialog > div', function() {-->
<!--        $('#fancy-inputs input').val($(this).text()).focus();-->
<!--        $('#fancy-inputs .close').addClass('visible');-->
<!--        $('#fancy-inputs input').addClass('white');-->
<!--        var elem = document.querySelector('.dialog');-->
<!--        if (elem.classList.contains('open'))-->
<!--            $('.dialog').removeClass('open');-->
<!--        alreadyFilled = true;-->
<!--    });-->
<!---->
<!--    $('span.close').click(function() {-->
<!--        alreadyFilled = false;-->
<!--        $('.dialog').addClass('open');-->
<!--        $('#fancy-inputs input').val('').focus();-->
<!--        $('#fancy-inputs input').removeClass('white');-->
<!--        $(this).removeClass('visible');-->
<!--    });-->
<!---->
<!--    function match(str) {-->
<!--        str = str.toLowerCase();-->
<!--        clearDialog();-->
<!--        for (var i = 0; i < Object.keys(states).length; i++) {-->
<!--            if (states[i].toLowerCase().startsWith(str)) {-->
<!--                $('.dialog').append('<div id="autoGroups' + i + '">' + states[i] + '</div>');-->
<!--            }-->
<!--        }-->
<!--    }-->
<!---->
<!--    $('#fancy-inputs input').on('input', function() {-->
<!--        $('.dialog').addClass('open');-->
<!--        alreadyFilled = false;-->
<!--        match($(this).val());-->
<!--    });-->
<!---->
<!--    $('html').click(function(e) {-->
<!--        if (!$(e.target).is("input, .close")) {-->
<!--            $('.dialog').removeClass('open');-->
<!--        }-->
<!--    });-->
<!---->
<!--    $('#fancy-radio label').on('click', function () {-->
<!--        var groupStat = document.querySelector('label.radio.groups');-->
<!--        var teacherStat = document.querySelector('label.radio.teachers');-->
<!--        if (!this.classList.contains('selected')) {-->
<!--            $('label.radio').removeClass('selected');-->
<!--            $(this).addClass('selected');-->
<!--            $('#fancy-inputs .close').removeClass('visible');-->
<!--            $('#fancy-inputs input').removeClass('white');-->
<!--            $('#fancy-inputs input').val('');-->
<!--            $('span.close').removeClass('visible');-->
<!--            alreadyFilled = false;-->
<!--            var inputID = $(this).attr('id');-->
<!--            if ($(this).is(':checked')) {-->
<!--                $('.' + inputID).addClass('selected');-->
<!--            } else {-->
<!--                $('.' + inputID).removeClass('selected');-->
<!--            }-->
<!--            clearDialog();-->
<!--            $('.tables').empty();-->
<!--            $(".parity_weeks").css('display', 'none');-->
<!--            if (groupStat.classList.contains("selected")) {-->
<!--                states = groups;-->
<!--                lastTeacher = '';-->
<!--                $('.input span span').replaceWith('<span>Введите группу</span>');-->
<!--                for (var i = 0; i < Object.keys(groups).length; i++) {-->
<!--                    $('.dialog').append('<div id="autoGroups' + i + '">' + groups[i] + '</div>');-->
<!--                }-->
<!--            } else if (teacherStat.classList.contains("selected")) {-->
<!--                states = teachers;-->
<!--                lastGroupNum = '';-->
<!--                $('.input span span').replaceWith('<span>Ф.И.О. преподавателя</span>');-->
<!--                for (var i = 0; i < Object.keys(teachers).length; i++) {-->
<!--                    $('.dialog').append('<div id="autoTeacher' + i + '">' + teachers[i] + '</div>');-->
<!--                }-->
<!--            }-->
<!--        } else-->
<!--            console.log('Selected yet')-->
<!--    });-->
<!--</script>-->

<!--<div class="cameras">-->
<!--<iframe width="640" height="480" src="https://rtsp.me/embed/NKh9A8HH/" frameborder="0" allowfullscreen></iframe>-->
<!--<iframe width="640" height="480" src="https://rtsp.me/embed/25bRt6bd/" frameborder="0" allowfullscreen></iframe>-->
<!--</div>-->
<!---->
