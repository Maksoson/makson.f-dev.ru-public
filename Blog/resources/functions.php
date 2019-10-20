<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Blog/db/db.php';

$db = \DB::getDb();

// Выбор всех статей
function get_start_posts() {
    global $db;
    $result = $db->prepare("SELECT * FROM posts ORDER BY id ASC LIMIT 6");
    $result->execute();
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $posts = $result->fetchAll();

    return $posts;
}

// Выбор статьи по id
function get_post_by_id($id) {
    global $db;
    $result = $db->prepare("SELECT * FROM posts WHERE id = :id");
    $result->bindParam(':id', $id);
    $result->execute();
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $posts = $result->fetchAll();
    $posts = $posts[0];

    return $posts;
}

// Узнать количество статей
function get_count_posts() {
    global $db;
    $result = $db->prepare("SELECT COUNT(*) FROM posts");
    $result->execute();
    $count = $result->fetchColumn();

    return (int)$count;
}

// Выбор названия категории по id
function get_category_by_id($id) {
    global $db;
    $result = $db->prepare("SELECT * FROM categories WHERE id = :id");
    $result->bindParam(':id', $id);
    $result->execute();
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $category = $result->fetchAll();
    $category = $category[0]['name'];

    return $category;
}

// Выбор имени автора по id
function get_author_by_id($id) {
    global $db;
    $result = $db->prepare("SELECT * FROM authors WHERE id = :id");
    $result->bindParam(':id', $id);
    $result->execute();
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $author = $result->fetchAll();
    $author = $author[0]['name'];

    return $author;
}
