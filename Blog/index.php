<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/resources/functions.php';
    session_start();
//    $posts = get_start_posts();
    $postObject = new Post();
    $posts = $postObject->get_start_posts();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Блог Краснопер Максима</title>
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

<div class="main">
    <ul>
        <?php foreach ($posts as $post):
            $category = $postObject->get_category_by_id($post['id_category']); ?>
        <li class="blog-post">
            <a href="/Blog/post/?id=<?php echo $post['id']; ?>">
                <p class="image"><img src="<?php echo $post['img'] ?>" height="200px" width="200px"></p>
                <h3 class="title"><?php echo $post['title'] ?></h3>
                <p class="date"><?php echo date('d.m.Y в H:i', strtotime($post['date'])) ?></p>
                <p class="description"><?php echo $post['description'] ?></p>
            </a>
        </li>
        <? endforeach; ?>
    </ul>
    <div class="show-more-button"><button class="show-more" count_show="6" count_add="6" type="button">Показать еще</button></div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/footer.php'?>

</body>

<script>
    let countPosts = 0;

    window.onload = function () {
        var data = {'type':'get_count'};
        $.ajax({
            url: 'handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                res = $.parseJSON(response);
                if (res.result == "success") {
                    countPosts = res.countPosts;
                }
            },
            error: function () {

            }
        });
    };

    $('body').on('drag dragstart dragend dragover dragenter dragleave drop', function(){
        return false;
    });

    $('.show-more').click(function() {
        let show_more = $(this);
        let count_show = parseInt($(this).attr('count_show'));
        let count_add = $(this).attr('count_add');
        show_more.text('Подождите...');

        let res;
        let data = {
            'type': 'show_more',
            'count_show': count_show,
            'count_add': count_add,
        };

        $.ajax({
            url: "handler.php",
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                res = JSON.parse(response);
                if (res.result == "success" && res.html != "") {
                    $('.main ul').append(res.html);
                    show_more.text('Показать еще');
                    count_show += 6;
                    show_more.attr('count_show', count_show);
                    console.log(count_show, countPosts);
                    if (countPosts - count_show < 6) {
                        show_more.attr('count_show', (count_show + (countPosts - count_show)));
                        $('.show-more-button').remove();
                    }
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(".footer").offset().top
                    }, 1000);
                }
            }
        });
    });

    $(".logout").click(function () {
        var data = {
            'type':'logout',
        };

        $.ajax({
            url: '/handler.php',
            type: "POST",
            dataType: "html",
            data: data,
            success: function (response) {
                if (response === 'Session successfully destroyed')
                    window.location = 'http://makson.f-dev.ru/Blog/';
            },
            error: function (response) {
                console.log('Выйти не удалось!');
            }
        })
    });

</script>
</html>
