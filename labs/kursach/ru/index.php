<?
    include $_SERVER["DOCUMENT_ROOT"]."/header.php";

    if (!isset($_SESSION['session_username'])) {
        header('Location: http://makson.f-dev.ru/labs/kursach/autorization/');
        exit();
    }
?>

<div class="flex-container">
    <div class="redact_choice">
        <ul><p>Выберите таблицу данных</p>
            <li><a href="../groups/">Группы</a></li>
            <li><a href="../subjects/">Предметы</a></li>
            <li><a href="../teachers/">Преподаватели</a></li>
            <li><a href="../cabinets/">Аудитории</a></li>
            <li><a href="../subgroups/">Подгруппы</a></li>
        </ul>
    </div>
</div>
