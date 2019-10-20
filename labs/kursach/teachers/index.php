<?  namespace Makson;
include $_SERVER["DOCUMENT_ROOT"]."/header.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if (!isset($_SESSION['session_username'])) {
    header('Location: http://makson.f-dev.ru/labs/kursach/autorization/');
    exit();
}

$db = DB::getDb();
$result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
$result->execute();
$result->setFetchMode(\PDO::FETCH_ASSOC);
$teachersList= $result->fetchAll();
$result = null;
?>

<div class="fancy">
    <form onsubmit="return false" autocomplete="off">
        <div id="fancy-inputs">
            <label class="input">
                <input type="text" id="inputTeacher">
                <span><span>Введите имя преподавателя</span></span><span class="close"><i class="fas fa-times"></i></span>
                <div class="dialog"></div>
            </label>
            <button id="btn" type="button"><i class="far fa-plus-square"></i></button>
        </div>
        <div>
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
        </div>
    </form>
</div>

<div id="results">
    <p id="infoParagr">Текущий список преподавателей</p>
    <?
    foreach ($teachersList as $teacherItem):?>
            <div class="infoP" id="<?='infoP'.$teacherItem['id']?>">
                <p id="<?='p'.$teacherItem['id']?>"><?=$teacherItem['name']?></p>
            </div>
            <div class="buttonsP" id="<?='buttonsP'.$teacherItem['id']?>">
                <button class="update" onclick="is_update(<?=$teacherItem['id']?>)" id="<?='update'.$teacherItem['id']?>" type="button"><i class="fas fa-pencil-alt"></i></button>
                <button class="del" onclick="del(<?=$teacherItem['id']?>);" id="<?=$teacherItem['id']?>" type="button"><i class="far fa-trash-alt"></i></button>
            </div>
    <? endforeach;?>
</div>

<div class="bottom"></div>

<script src="https://www.google.com/recaptcha/api.js?render=6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt"></script>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script>
    function captcha_generate() {
        grecaptcha.execute('6Lc9JawUAAAAACYDu852G-WLjPE6Iw9X_f4ctuTt', {action: 'homepage'})
            .then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
            }).then(add);
    }

    $("#inputTeacher").keypress(function (e) {
        if (e.which == 13) {
            captcha_generate();
        }
    });

    $("#btn").click(function () {
        captcha_generate();
    });

    function add() {
        var data = {
            'type': 'insert', 'data': $("#inputTeacher").val(), 'g-recaptcha-response': $("#g-recaptcha-response").val()
        };
        console.log(data);
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                if (response !== 'Spam') {
                    result = $.parseJSON(response);
                    $('#results').append('<div class="infoP" id="infoP' + result.id + '"><p id="p' + result.id + '"> ' + result.name + '</p>' + '</div>' + '<div class="buttonsP" id="buttonsP' + result.id + '">' +
                        '<button class="update" onclick="is_update(' + result.id + ');" id="update' + result.id + '" type="button"><i class="fas fa-pencil-alt"></i></button>' + ' ' +
                        '<button class="del" onclick="del(' + result.id + ');" id="' + result.id + '" type="button"><i class="far fa-trash-alt"></i></button>' +
                        '</div>');
                }
            },
            error: function (response) {
                alert('Ошибка. Данные не отправлены.');
            }
        });
        $('#inputTeacher').val('');
    }


    function del(id) {
        var data = {'type':'delete','data':id};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function(response) {

            },
            error: function(response) {
                alert('Ошибка. Данные не удалены.');
            }
        });
        $('#infoP' + id).remove();
        $('#buttonsP' + id).remove()
    }


    function is_update(id) {
        var lastid = id;
        var txt = $("#p" + id).html();
        $("#p" + id).replaceWith('<input id="form' + lastid + '" value="'+ txt +'" class="updateInput" />');
        $("#update" + id).replaceWith('<button class="save" onclick="update(' + lastid + ');" id="save' + id + '"><i class="fas fa-check"></i></button> ' +
            '<button class="cancel" onclick="cancel(' + lastid + ')" id="cancel' + id + '"><i class="fas fa-times"></i></button>');
    }

    function cancel(id) {
        var lastid = id;
        var data = $("#form" + id).val();
        $("#form" + id).replaceWith('<p id="p' + lastid + '">' + data + '</p>');
        $("#save" + id).replaceWith('<button class="update" onclick="is_update(' + lastid + ');" id="update'+ id + '" type="button"><i class="fas fa-pencil-alt"></i></button>');
        $("#cancel" + id).remove();
    }

    function update(id) {
        var lastid = id;
        var data = {'type':'update','data':$("#form" + id).val(), 'id': id};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function () {
                $("#form" + id).replaceWith('<p id="p' + lastid + '">' + data['data'] + '</p>');
                $("#save" + id).replaceWith('<button class="update" onclick="is_update(' + lastid + ');" id="update'+ id + '" type="button"><i class="fas fa-pencil-alt"></i></button>');
                $("#cancel" + id).remove();
            },
            error: function () {
                alert('Ошибка. Данные не изменены.');
            }
        })

    }

</script>




