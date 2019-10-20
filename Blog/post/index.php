<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/resources/functions.php';
    session_start();
    $id = (int)$_GET['id'];
    $post = get_post_by_id($id);
    $category = get_category_by_id($post['id_category']);
    $author = get_author_by_id($post['id_author']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?php echo $post['title'] ?></title>
    <meta charset="utf-8">
    <meta name="keywords" content="Краснопер, Краснопёр, Максим, Блог">
    <meta name="description" content="Блог Краснопер Максима">
    <link rel="stylesheet" href="/blog-styles.css">
    <link rel="stylesheet" href="/animated.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/css/swiper.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/header.php'?>

<div class="main-post">
    <div class="main-info-place">
        <div class="post-image-place">
            <img src="../..<?php echo $post['img'] ?>" width="330px" height="364px">
        </div>
        <div class="post-info-place">
            <h1 class="post-title"><?php echo $post['title'] ?></h1>
            <p class="post-category"><span class="post-category-span">Класс: </span><?php echo $category ?></p>
            <p class="post-author"><span class="post-author-span">Автор статьи: </span><?php echo $author ?></p>
            <p class="post-date"><span class="post-date-span">Дата публикации: </span><?php echo date('d.m.Y в H:i', strtotime($post['date'])) ?></p>
            <p class="post-description-label">Краткая информация:</p>
            <p class="post-description"><?php echo $post['description'] ?></p>
        </div>
        <div class="post-text-place">
            <div class="post-text-label">
                <p class="post-biography">Биография</p>
                <p class="post-text"><?php echo $post['text'] ?></p>
            </div>
        </div>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/footer.php'?>

</body>