<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/db/db.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/resources/functions.php';

// Связь с базой данных
$db = \DB::getDb();
// Инициализация объекта Post
$post = new Post();

// Выход из аккаунта
if ($_POST['type'] == 'logout') {
    session_start();
    unset($_SESSION['session_username']);
    session_destroy();

    echo 'Session successfully destroyed';
}
// Кнопка "Показать еще"
elseif ($_POST['type'] == 'show_more') {

    $html = $post->show_more();

    $outputArray = array(
        'result' => 'success',
        'html' => $html,
    );
    echo json_encode($outputArray);
}
// Получить количество статей
elseif ($_POST['type'] == 'get_count') {
    $count = $post->get_count_posts();

    $outputArray = array(
        'result' => 'success',
        'countPosts' => $count,
    );

    echo json_encode($outputArray);
}