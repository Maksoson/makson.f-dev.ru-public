<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if ($_POST['type'] == 'autorization') {
    function getCaptcha($SecretKey)
    {
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
        $Return = json_decode($Response);
        return $Return;
    }

    $Return = getCaptcha($_POST['g-recaptcha-response']);
    if ($Return->success == true and $Return->score > 0.5) {
        $db = DB::getDb();
        $result = $db->prepare("SELECT * FROM `usertbl` ORDER BY id ASC");
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $usersList = $result->fetchAll();
        $result = null;

        $login = $_POST['login'];
        $password = $_POST['password'];
        $is_login_exists = false;
        $hashed_password = '';
        $username = '';

        foreach ($usersList as $user) {
            if ($user['email'] == $login) {
                $hashed_password = $user['password'];
                $username = $user['username'];
                $is_login_exists = true;
                break;
            }
            if ($user['username'] == $login) {
                $hashed_password = $user['password'];
                $username = $user['username'];
                $is_login_exists = true;
                break;
            }
        }

        if ($is_login_exists) {
            if (password_verify($password, $hashed_password) == true) {
                session_start();
                $_SESSION['session_username'] = $username;

                echo 'Success';
            } elseif (password_verify($password, $hashed_password) == false)
                echo 'Undefined login or password';
        } else
            echo 'Undefined login or password';
    } else
        echo 'Spam';
}