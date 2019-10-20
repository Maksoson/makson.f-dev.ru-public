<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/lib/PHPMailer/PHPMailerAutoload.php";

function getCaptcha($SecretKey)
{
    $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
    $Return = json_decode($Response);
    return $Return;
}

$Return = getCaptcha($_POST['g-recaptcha-response']);
if ($Return->success == true and $Return->score > 0.5) {

    if ($_POST['type'] == 'recovering') {

        $db = DB::getDb();
        $result = $db->prepare("SELECT * FROM `usertbl` ORDER BY id ASC");
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $usersList = $result->fetchAll();
        $result = null;

        $email = $_POST['email'];
        $is_email_exist = false;
        $username = '';
        $full_name = '';

        foreach ($usersList as $user) {
            if ($user['email'] == $email) {
                $username = $user['username'];
                $full_name = $user['full_name'];
                $is_email_exist = true;
                break;
            }
        }

        if ($is_email_exist) {
            $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 3, date("Y"));
            $expDate = date("Y-m-d H:i:s", $expFormat);
            $demo_key = $username . '_' . $email . rand(0, 10000) . $expDate;
            $key = password_hash($demo_key, PASSWORD_DEFAULT);
            $passwordLink = "<a href=https://makson.f-dev.ru/labs/kursach/recovery/?a=recovery&email=" . $email . "&u=" . $key . ">https://makson.f-dev.ru/finish_recovery</a>";

            $message = "Уважаемый(ая) " . $full_name . "!<br/>";
            $message .= "Пройдите по ссылке, чтобы изменить пароль:<br/>";
            $message .= "-----------------------<br/>";
            $message .= "$passwordLink<br/>";
            $message .= "-----------------------<br/>";
            $message .= "Ссылка будет действительна в течении 3-х дней.<br/><br/>";
            $message .= "<strong>Ничего не делайте, если вы не запрашивали восстановение пароля!</strong><br/><br/>";
            $message .= "-----------------------<br/>";
            $message .= "С уважением,<br/>Команда сайта - <a href='https://makson.f-dev.ru'>https://makson.f-dev.ru</a> - :)";

            $mail = new \PHPMailer();
            $mail->SMTPDebug = 1;
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true;
            $mail->Username = 'maxibon_17@mail.ru';
            $mail->Password = 'xgntmxjgwpbfbtps';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = '465';

            $mail->CharSet = 'UTF-8';
            $mail->setFrom('maxibon_17@mail.ru', 'BOT Max');

            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Восстановление пароля';
            $mail->Body = $message;

            if ($mail->send()) {
                $result = $db->prepare("INSERT INTO `recoveryemails` (`userID`, `check_key`, `expDate`) VALUES (:userID, :check_key, :expDate)");
                $result->bindParam(':userID', $email);
                $result->bindParam(':check_key', $key);
                $result->bindParam(':expDate', $expDate);
                $result->execute();
                $result = null;

                echo 'Success';
            } else
                echo 'Error';
        } else
            echo 'Undefined email';

    } elseif ($_POST['type'] == 'finish_recovering') {
        $email = $_POST['email'];
        $key = $_POST['key'];
        $password = $_POST['new_password'];
        $new_password = password_hash($password, PASSWORD_DEFAULT);
        $is_email_exist = false;
        $is_key_is_right = false;

        $db = DB::getDb();
        $result = $db->prepare("SELECT `password`,`full_name` FROM `usertbl` WHERE `email` = :email");
        $result->bindParam(':email', $email);
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $demo = $result->fetchAll();
        $old_password = $demo[0]['password'];
        $full_name = $demo[0]['full_name'];
        $result = null;

        if (password_verify($password, $old_password)) {
            echo 'Old password';
        } else {
            $result = $db->prepare("SELECT * FROM `recoveryemails` ORDER BY id ASC");
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $emailsList = $result->fetchAll();
            $result = null;

            foreach ($emailsList as $item) {
                if ($item['userID'] == $email) {
                    $is_email_exist = true;
                    if ($key == $item['check_key']) {
                        $is_key_is_right = true;
                        $id = $item['id'];
                        $result = $db->prepare("UPDATE `usertbl` SET `password` = :new_password WHERE `email` = :email");
                        $result->bindParam(':new_password', $new_password);
                        $result->bindParam(':email', $email);
                        $result->execute();
                        $result = null;

                        $result = $db->prepare("DELETE FROM `recoveryemails` WHERE `id` = :id");
                        $result->bindParam(':id', $id);
                        $result->execute();
                        $result = null;

                        $message = "Уважаемый(ая) " . $full_name . "! <br/>";
                        $message .= "Ваш пароль на сайте <a href='https://makson.f-dev.ru/ru/'>https://makson.f-dev.ru/ru/</a> был успешно изменен!<br/>";
                        $message .= "------------------------------<br/>";
                        $message .= "<strong>Ваш новый пароль:</strong> " . $password . "<br/>";
                        $message .= "------------------------------<br/>";
                        $message .= "Если это были не вы, обратитесь в нашу тех.поддержку - <a href='https://makson.f-dev.ru/labs/kursach/help/'>https://makson.f-dev.ru/help/</a><br/><br/>";
                        $message .= "------------------------------<br/>";
                        $message .= "С уважением,<br/>Команда сайта - <a href='https://makson.f-dev.ru'>https://makson.f-dev.ru</a> - :)";

                        $mail = new \PHPMailer();
                        $mail->SMTPDebug = 1;
                        $mail->isSMTP();
                        $mail->Host = 'smtp.mail.ru';
                        $mail->SMTPAuth = true;
                        $mail->SMTPKeepAlive = true;
                        $mail->Username = 'maxibon_17@mail.ru';
                        $mail->Password = 'xgntmxjgwpbfbtps';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = '465';

                        $mail->CharSet = 'UTF-8';
                        $mail->setFrom('maxibon_17@mail.ru', 'BOT Max');

                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Пароль восстановлен';
                        $mail->Body = $message;

                        $mail->send();
                        break;
                    }
                }
            }

            if ($is_email_exist)
                if ($is_key_is_right)
                    echo ('Successfully changed!');
                else
                    echo 'Error key';
            else
                echo 'Undefined email';
        }
    }

} else
    echo 'Spam';