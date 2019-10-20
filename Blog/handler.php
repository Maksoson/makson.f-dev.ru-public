<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/db/db.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/resources/functions.php';

// Связь с базой данных
$db = \DB::getDb();

// Выход из аккаунта
if ($_POST['type'] == 'logout') {
    session_start();
    unset($_SESSION['session_username']);
    session_destroy();

    echo 'Session successfully destroyed';
}
// Кнопка "Показать еще"
elseif ($_POST['type'] == 'show_more') {
    global $db;
    $startIndex = (int)$_POST['count_show'];
    $countView = (int)$_POST['count_add'];

    $result = $db->prepare('SELECT * FROM `posts` ORDER BY id ASC LIMIT :startIndex, :countView');
    $result->bindValue(':startIndex', $startIndex, PDO::PARAM_INT);
    $result->bindValue(':countView', $countView, PDO::PARAM_INT);
    $result->execute();
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $newPosts = $result->fetchAll();

    $html = "";
    foreach ($newPosts as $newPost) {
        $html .= '<li class="blog-post animated fadeIn" id="' . $newPost['id'] . '">';
            $html .= '<a href="/Blog/post/?id=' . $newPost['id'] . '">';
                $html .= '<p class="image"><img src="' . $newPost['img'] . '" height="200px" width="200px"></p>';
                $html .= '<h3 class="title">' . $newPost['title'] . '</h3>';
                $html .= '<p class="date">' . date('d.m.Y в H:i', strtotime($newPost['date'])) . '</p>';
                $html .= '<p class="description">' . $newPost['description'] . '</p>';
            $html .= '</a>';
        $html .= '</li>';
    }

    $outputArray = array(
        'result' => 'success',
        'html' => $html,
    );
    echo json_encode($outputArray);
}
// Получить количество статей
elseif ($_POST['type'] == 'get_count') {
    $count = get_count_posts();

    $outputArray = array(
        'result' => 'success',
        'countPosts' => $count,
    );

    echo json_encode($outputArray);
}