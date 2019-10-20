<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if ($_POST['type'] == 'registration'){
        function getCaptcha($SecretKey)
        {
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
            $Return = json_decode($Response);
            return $Return;
        }

        $Return = getCaptcha($_POST['g-recaptcha-response']);
        if ($Return->success == true && $Return->score > 0.5) {
            $errors_array = array(
                'email' => 0,
                'username' => 0,
            );
            $db = DB::getDb();
            $result = $db->prepare("SELECT * FROM `usertbl` ORDER BY id ASC");
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $usersList = $result->fetchAll();
            $result = null;

            $email = $_POST['email'];
            $fullname = $_POST['fullname'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            foreach ($usersList as $user) {
                if ($user['email'] == $email) {
                    $errors_array['email'] = 1;
                    break;
                }
            }
            foreach ($usersList as $user) {
                if ($user['username'] == $username) {
                    $errors_array['username'] = 1;
                    break;
                }
            }
            if ($errors_array['email'] == 0 and $errors_array['username'] == 0) {
                $result = $db->prepare("INSERT INTO `usertbl` (`full_name`, `email`, `username`, `password`)
                                                  VALUES (:full_name, :email, :username, :password)");
                $result->bindParam(':full_name', $fullname);
                $result->bindParam(':email', $email);
                $result->bindParam(':username', $username);
                $result->bindParam(':password', $hashed_password);
                $result->execute();
                $result = null;
                echo 'Success';
            } else {
                echo json_encode($errors_array);
            }
        } else {
            echo "Spam";
        }

}