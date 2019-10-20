<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if ($_POST['type'] == 'insert') {
    function getCaptcha($SecretKey)
    {
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
        $Return = json_decode($Response);
        return $Return;
    }

    $Return = getCaptcha($_POST['g-recaptcha-response']);
    if ($Return->success == true && $Return->score > 0.5) {
        if (isset($_POST["data"])) {
            $name = $_POST["data"];
            $db = DB::getDb();
            $result = $db->prepare("INSERT INTO groups (name) VALUES (:groupName)");
            $result->bindParam(':groupName', $name);
            $result->execute();
            $id = $db->lastInsertId();
            if ($id) {
                $outputArray = array(
                    'name' => $_POST['data'],
                    'id' => $id,
                );

                echo json_encode($outputArray);
            }

        }
    } else
        echo 'Spam';
} elseif ($_POST['type'] == 'delete') {
    if (isset($_POST['data'])) {
        $id = $_POST['data'];
        $db = DB::getDb();
        $result = $db->prepare("DELETE FROM groups WHERE id = :id");
        $result->bindParam(':id', $id);
        $result->execute();
    }
} elseif ($_POST['type'] == 'update') {
    if (isset($_POST['data'])) {
        $id = $_POST['id'];
        $new_name = $_POST['data'];
        $db = DB::getDb();
        $result = $db->prepare("UPDATE groups SET name = :new_name where id = :id");
        $result->bindParam(':new_name', $new_name);
        $result->bindParam(':id', $id);
        $result->execute();

    }
}
