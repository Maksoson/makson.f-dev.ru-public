<? namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if (count($_FILES) == 0) {
    if ($_POST['type'] == 'insert') {
        if (isset($_POST['group'])) {

            $db = DB::getDB();
            $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $subjectsList = $result->fetchAll();
            $result = null;

            $result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $teachersList = $result->fetchAll();
            $result = null;

            $result = $db->prepare('SELECT * FROM sub_groups ORDER BY id ASC');
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $subsList = $result->fetchAll();
            $result = null;

            $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $cabinetsList = $result->fetchAll();
            $result = null;

            $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

            $typesList = array('Лекция', 'Практика');

            $group = (int)$_POST['group'];
            $day = (int)$_POST['day'];

            $parity = $_POST['parity'];
            if ($parity == $parityList[0]) {
                $parity = 1;
            } elseif ($parity == $parityList[1]) {
                $parity = 2;
            } elseif ($parity == $parityList[2]) {
                $parity = 3;
            }

            $lesson_type = $_POST['types'];
            if ($lesson_type == $typesList[0]) {
                $lesson_type = 1;
            } elseif ($lesson_type == $typesList[1]) {
                $lesson_type = 2;
            }

            $number = (int)$_POST['numbers'];

            $subject = $_POST['subjects'];
            foreach ($subjectsList as $subj) {
                if ($subj['name'] == $subject) {
                    $subject = (int)$subj['id'];
                    break;
                }
            }

            $teacher = $_POST['teachers'];
            echo ($teacher) . " ";
            foreach ($teachersList as $teach) {
                if ($teach['name'] == $teacher) {
                    $teacher = (int)$teach['id'];
                    break;
                }
            }
            echo ($teacher) . " ";

            $sub = $_POST['subs'];
            if (is_int($sub) === false) {
                foreach ($subsList as $subgr) {
                    if ($subgr['name'] == $sub) {
                        $sub = (int)$subgr['id'];
                        break;
                    }
                }
            }

            $cabinet = $_POST['cabinets'];
            echo $cabinet . " ";
            foreach ($cabinetsList as $cab) {
                if ($cab['name'] == $cabinet) {
                    $cabinet = (int)$cab['id'];
                    break;
                }
            }
            echo($cabinet);

            $result = $db->prepare("INSERT INTO `lesson` (`id_group`, `day`, `parity_week`, `lesson_type`, `number`,`id_subject`, `id_teacher`, `subgroup`, `id_cabinet`) 
                       VALUES (:groupName,:dayName,:parityName,:lesson_typeName,:numberName,:subject_id,:teacher_id,:subgroup,:cabinet_id)");

            $result->bindParam(':groupName', $group);
            $result->bindParam(':dayName', $day);
            $result->bindParam(':parityName', $parity);
            $result->bindParam(':lesson_typeName', $lesson_type);
            $result->bindParam(':numberName', $number);
            $result->bindParam(':subject_id', $subject);
            $result->bindParam(':teacher_id', $teacher);
            $result->bindParam(':subgroup', $sub);
            $result->bindParam(':cabinet_id', $cabinet);
            $result->execute();
            $result = null;

        }
    } elseif ($_POST['type'] == 'groupSearch') {
        function getCaptcha($SecretKey)
        {
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
            $Return = json_decode($Response);
            return $Return;
        }

        $Return = getCaptcha($_POST['g-recaptcha-response']);
        if ($Return->success == true && $Return->score > 0.5) {
            if (isset($_POST['group'])) {
                $groupName = $_POST['group'];
                $parity = $_POST['parity'];
                $groupNameId = 0;

                $db = DB::getDB();
                $result = $db->prepare('SELECT * FROM groups ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $groupsList = $result->fetchAll();
                $result = null;

                foreach ($groupsList as $group) {
                    if ($group["name"] == $groupName) {
                        $groupNameId = (int)$group["id"];
                        break;
                    }
                }

                $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $subjectsList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $teachersList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM sub_groups ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $subsList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $cabinetsList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM departments ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $departmentsList = $result->fetchAll();
                $result = null;

                $daysList = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');

                $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

                $typesList = array('Лекция', 'Практика');

                $numbersList = array(1, 2, 3, 4, 5, 6, 7);

                $timesList = array('<p id="t1">8:00</p><p id="t2">9:30</p>', '<p id="t1">9:40</p><p id="t2">11:10</p>', '<p id="t1">11:30</p><p id="t2">13:00</p>',
                    '<p id="t1">13:10</p><p id="t2">14:40</p>', '<p id="t1">15:00</p><p id="t2">16:30</p>',
                    '<p id="t1">16:40</p><p id="t2">18:10</p>', '<p id="t1">18:20</p><p id="t2">19:50</p>');

                $parity_now = '';
                // 1-я неделя
                if ((int)$parity == 1) {
                    $result = $db->prepare("SELECT * FROM groups, lesson WHERE groups.name = :groupName AND 
                                    lesson.id_group = groups.id AND (lesson.parity_week = :parity1 OR lesson.parity_week = :parity3)  
                                    ORDER BY lesson.day,lesson.number,lesson.subgroup ASC");
                    $result->bindParam(':groupName', $groupName);
                    $result->bindValue(':parity1', 1);
                    $result->bindValue(':parity3', 3);
                    $result->execute();
                    $searchedGroup = $result->fetchAll();
                    $result = null;
                    $parity_now = $parityList[0];
                } // 2-я неделя
                elseif ((int)$parity == 2) {
                    $result = $db->prepare("SELECT * FROM groups, lesson WHERE groups.name = :groupName AND 
                                    lesson.id_group = groups.id AND (lesson.parity_week = :parity2 OR lesson.parity_week = :parity3)  
                                    ORDER BY lesson.day,lesson.number,lesson.subgroup ASC");
                    $result->bindParam(':groupName', $groupName);
                    $result->bindValue(':parity2', 2);
                    $result->bindValue(':parity3', 3);
                    $result->execute();
                    $searchedGroup = $result->fetchAll();
                    $result = null;
                    $parity_now = $parityList[1];
                }

                $feb_days = 28;
                // Високосный ли год?
                if (date('L'))
                    $feb_days = 29;
                $months_info = array(
                    array('January', 31, 'января'),
                    array('February', $feb_days, 'февраля'),
                    array('March', 31, 'марта'),
                    array('April', 30, 'апреля'),
                    array('May', 31, 'мая'),
                    array('June', 30, 'июня'),
                    array('July', 31, 'июля'),
                    array('August', 31, 'августа'),
                    array('September', 30, 'сентября'),
                    array('October', 31, 'октября'),
                    array('November', 30, 'ноября'),
                    array('December', 31, 'декабря'),
                );

                $snuffeen = '';
                $empty_td = 1;

                $days_in_month = 0;
                $next_month = array();

                $now_month = date('F');
                for ($i = 0; $i <= 11; $i++)
                    if ($months_info[$i][0] == $now_month) {
                        $now_month = $months_info[$i][2];
                        $days_in_month = $months_info[$i][1];
                        if ($i != 11)
                            $next_month = $months_info[$i + 1];
                        else
                            $next_month = $months_info[0];
                        break;
                    }

                $now_day = date('j');
                $now_day_name = date('N') - 1;
                $diff_date = 0;

                $lower_day = $now_day - $now_day_name;
                $last_date = $lower_day;
                $first_week_days = array();
                $second_week_days = array();
                $check = -1;
                for ($i = 0; $i < 13; $i++) {
                    $check++;
                    $day = $lower_day + $check;
                    if ($i < 6)
                        array_push($first_week_days, $day);
                    elseif ($i > 6)
                        array_push($second_week_days, $day);

                    if ($day == $days_in_month) {
                        $days_in_month = $next_month[1];
                        $lower_day = 1;
                        $check = -1;
                    }
                }


                // По дням
                foreach ($daysList as $key => $day) {
                    if ((int)$parity == 1)
                        $diff_date = $first_week_days[$key];
                    elseif ((int)$parity == 2)
                        $diff_date = $second_week_days[$key];

                    if ($last_date > $diff_date)
                        $now_month = $next_month[2];
                    $last_date = $diff_date;

                    $snuffeen .= '<div class="swiper-slide">';
                    $snuffeen .= '<div class="tableplace" id="table' . ($key + 1) . '">';
                    if ($diff_date == $now_day) {
                        $snuffeen .= '<h4 id="tableDay" style="background: #126984; color: #e8edf4;">';
                        $snuffeen .= $day . " | Сегодня, " . $now_day . " " . $now_month;
                    } else {
                        $snuffeen .= '<h4 id="tableDay">';
                        $snuffeen .= $day . " | " . $diff_date . " " . $now_month;
                    }
                    $snuffeen .= '</h4>';
                    $snuffeen .= '<table><tbody>';
                    // По парам
                    foreach ($numbersList as $keyn => $number) {
                        $isFind = false;
                        $is_lection = false;
                        $snuffeen .= '<tr>';
                        // Проход по списку запрошенных пар
                        // Общая пара
                        foreach ($searchedGroup as $keys => $searched) {
//                    echo $keys." <---)";
                            if ($searched["subgroup"] == "3") {
                                if ((int)$searched["day"] === ($key + 1)) {
                                    if ((int)$searched["number"] === $keyn + 1) {
                                        foreach ($subjectsList as $subj) {
                                            if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                if ((int)$searched["lesson_type"] - 1 == 0 and (int)$searched["day"] == $key + 1 and (int)$searched["number"] == $keyn + 1 and $is_lection == false) {
                                                    $snuffeen .= '<td class="lection-yes"><p>' . '</p></td>';
                                                } elseif ($is_lection == false) {
                                                    $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                }
                                                $is_lection = true;
                                                $snuffeen .= '<td class="info" colspan="3" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                $isFind = true;
                                                break;
                                            }
                                        }
                                        foreach ($teachersList as $teacher) {
                                            if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a></p></td>';
                                                break;
                                            }
                                        }
                                        foreach ($cabinetsList as $cabinet) {
                                            if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                $snuffeen .= '<td class="place" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                break;
                                            }
                                        }
                                    }
                                }
                            } elseif ($searched["subgroup"] == "1") {
                                // Если у 1 и 2 пг есть пары
                                if (($keys + 1) < (int)count($searchedGroup)) {
                                    if ((int)$searchedGroup[$keys + 1]["number"] == (int)$searched["number"]) {
                                        if ((int)$searched["day"] === ($key + 1)) {
                                            if ((int)$searched["number"] === $keyn + 1) {
                                                foreach ($subjectsList as $subj) {
                                                    if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                        $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                        if ((int)$searched["lesson_type"] - 1 == 0 and (int)$searched["day"] == $key + 1 and (int)$searched["number"] == $keyn + 1 and $is_lection == false) {
                                                            $snuffeen .= '<td class="lection-yes"><p>' . '</p></td>';
                                                        } elseif ($is_lection == false) {
                                                            $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                        }
                                                        $is_lection = true;
                                                        $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                        break;
                                                    }
                                                }
                                                $departmentName = "";
                                                if (!$searched["id_department"] == null) {
                                                    foreach ($departmentsList as $department) {
                                                        if ($searched["id_department"] == $department["id"]) {
                                                            $departmentName = $department["name"];
                                                            break;
                                                        }
                                                    }
                                                }
                                                foreach ($teachersList as $teacher) {
                                                    if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                        $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                        break;
                                                    }
                                                }
                                                foreach ($cabinetsList as $cabinet) {
                                                    if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                        $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    } // Если у 1 пг есть пара, у 2 пг нет пары
                                    elseif ((int)$searchedGroup[$keys + 1]["number"] != (int)$searched["number"]) {
                                        if ((int)$searched["day"] === ($key + 1)) {
                                            if ((int)$searched["number"] === $keyn + 1) {
                                                foreach ($subjectsList as $subj) {
                                                    if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                        $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                        if ((int)$searched["lesson_type"] - 1 == 0 and (int)$searched["day"] == $key + 1 and (int)$searched["number"] == $keyn + 1 and $is_lection == false) {
                                                            $snuffeen .= '<td class="lection-yes"><p>' . '</p></td>';
                                                        } elseif ($is_lection == false) {
                                                            $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                        }
                                                        $isFind = true;
                                                        $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                        break;
                                                    }
                                                }
                                                $departmentName = "";
                                                if (!$searched["id_department"] == null) {
                                                    foreach ($departmentsList as $department) {
                                                        if ($searched["id_department"] == $department["id"]) {
                                                            $departmentName = $department["name"];
                                                            break;
                                                        }
                                                    }
                                                }
                                                foreach ($teachersList as $teacher) {
                                                    if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                        $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                        break;
                                                    }
                                                }
                                                foreach ($cabinetsList as $cabinet) {
                                                    if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                        $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                        break;
                                                    }
                                                }
                                                if ($isFind) {
                                                    $snuffeen .= '<td class="info1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 2 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold">' . '</p>';
                                                    $snuffeen .= '<p><a href="#">' . '</a></p></td>';
                                                    $snuffeen .= '<td class="place1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 2 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyplace' . $empty_td . '"><p>' . '</p></td>';
                                                    $empty_td += 1;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                } elseif ($keys + 1 == (int)count($searchedGroup)) {
                                    if ((int)$searched["day"] === ($key + 1)) {
                                        if ((int)$searched["number"] === $keyn + 1) {
                                            foreach ($subjectsList as $subj) {
                                                if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                    $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                    if ((int)$searched["lesson_type"] - 1 == 0 and (int)$searched["day"] == $key + 1 and (int)$searched["number"] == $keyn + 1 and $is_lection == false) {
                                                        $snuffeen .= '<td class="lection-yes"><p>' . '</p></td>';
                                                    } elseif ($is_lection == false) {
                                                        $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                    }
                                                    $isFind = true;
                                                    $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                    break;
                                                }
                                            }
                                            $departmentName = "";
                                            if (!$searched["id_department"] == null) {
                                                foreach ($departmentsList as $department) {
                                                    if ($searched["id_department"] == $department["id"]) {
                                                        $departmentName = $department["name"];
                                                        break;
                                                    }
                                                }
                                            }
                                            foreach ($teachersList as $teacher) {
                                                if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                    $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                    break;
                                                }
                                            }
                                            foreach ($cabinetsList as $cabinet) {
                                                if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                    $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                    break;
                                                }
                                            }
                                            if ($isFind) {
                                                $snuffeen .= '<td class="info1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 2 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold">' . '</p>';
                                                $snuffeen .= '<p><a href="#">' . '</a></p></td>';
                                                $snuffeen .= '<td class="place1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 2 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyplace' . $empty_td . '"><p>' . '</p></td>';
                                                $empty_td += 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                            } // Если у 1 и 2 пг есть пары
                            elseif ($searched["subgroup"] == "2") {
                                if (($keys - 1) > -1) {
                                    if ((int)$searchedGroup[$keys - 1]["number"] == (int)$searched["number"]) {
                                        if ((int)$searched["day"] === ($key + 1)) {
                                            if ((int)$searched["number"] === $keyn + 1) {
                                                foreach ($subjectsList as $subj) {
                                                    if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                        $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                        $isFind = true;
                                                        break;
                                                    }
                                                }
                                                $departmentName = "";
                                                if (!$searched["id_department"] == null) {
                                                    foreach ($departmentsList as $department) {
                                                        if ($searched["id_department"] == $department["id"]) {
                                                            $departmentName = $department["name"];
                                                            break;
                                                        }
                                                    }
                                                }
                                                foreach ($teachersList as $teacher) {
                                                    if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                        $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                        break;
                                                    }
                                                }
                                                foreach ($cabinetsList as $cabinet) {
                                                    if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                        $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    } // Если у 1 пг нет пары, у 2 пг есть пара
                                    elseif ((int)$searchedGroup[$keys - 1]["number"] != (int)$searched["number"]) {
                                        if ((int)$searched["day"] === ($key + 1)) {
                                            if ((int)$searched["number"] === $keyn + 1) {
                                                foreach ($subjectsList as $subj) {
                                                    if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                        $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                        $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                        $snuffeen .= '<td class="info1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 1 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold"></p>';
                                                        $snuffeen .= '<p><a href="#"></a></p></td>';
                                                        $snuffeen .= '<td class="place1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 1 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyplace' . $empty_td . '"><p></p></td>';
                                                        $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                        $isFind = true;
                                                        $empty_td += 1;
                                                        break;
                                                    }
                                                }
                                                $departmentName = "";
                                                if (!$searched["id_department"] == null) {
                                                    foreach ($departmentsList as $department) {
                                                        if ($searched["id_department"] == $department["id"]) {
                                                            $departmentName = $department["name"];
                                                            break;
                                                        }
                                                    }
                                                }
                                                foreach ($teachersList as $teacher) {
                                                    if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                        $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                        break;
                                                    }
                                                }
                                                foreach ($cabinetsList as $cabinet) {
                                                    if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                        $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } elseif ($keys - 1 == -1) {
                                    if ((int)$searched["day"] === ($key + 1)) {
                                        if ((int)$searched["number"] === $keyn + 1) {
                                            foreach ($subjectsList as $subj) {
                                                if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                                    $snuffeen .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                    $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                                                    $snuffeen .= '<td class="info1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 1 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold"></p>';
                                                    $snuffeen .= '<p><a href="#"></a></p></td>';
                                                    $snuffeen .= '<td class="place1" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 1 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyplace' . $empty_td . '"><p></p></td>';
                                                    $snuffeen .= '<td class="info1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                    $isFind = true;
                                                    $empty_td += 1;
                                                    break;
                                                }
                                            }
                                            $departmentName = "";
                                            if (!$searched["id_department"] == null) {
                                                foreach ($departmentsList as $department) {
                                                    if ($searched["id_department"] == $department["id"]) {
                                                        $departmentName = $department["name"];
                                                        break;
                                                    }
                                                }
                                            }
                                            foreach ($teachersList as $teacher) {
                                                if ((int)$searched["id_teacher"] === (int)$teacher["id"]) {
                                                    $snuffeen .= '<p><a href="#" id="teacher' . $searched["id"] . '">' . $teacher["name"] . '</a><span id="department' . $searched["id_department"] . '">' . $departmentName . '</span></p></td>';
                                                    break;
                                                }
                                            }
                                            foreach ($cabinetsList as $cabinet) {
                                                if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                    $snuffeen .= '<td class="place1" onclick="modal(' . $searched["id"] . ',' . ($key + 1) . ',' . (int)$parity . ',' . (int)$searched["subgroup"] . ',' . (int)$searched["lesson_type"] . ',' . (int)$searched["parity_week"] . ',' . $keyn . ',' . $groupNameId . ');" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Если пары нет вообще
                        if ($isFind == false) {
                            $snuffeen .= '<td class="time">' . $timesList[$keyn] . '</td>';
                            $snuffeen .= '<td class="lection"><p>' . '</p></td>';
                            $snuffeen .= '<td class="info" colspan="3" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 3 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold"></p>';
                            $snuffeen .= '<p><a href="#"></a></p></td>';
                            $snuffeen .= '<td class="place" onclick="modal(' . $empty_td . ',' . ($key + 1) . ',' . (int)$parity . ',' . 3 . ', ' . 0 . ', ' . 0 . ',' . $keyn . ',' . $groupNameId . ')" id="emptyplace' . $empty_td . '"><p></p></td>';
                            $empty_td += 1;
                        }
                        $snuffeen .= '</tr>';
                    }
                    // Конец дня
                    $snuffeen .= '</tbody></table>';
                    $snuffeen .= '</div></div>';
                }

                // Конец вывода дней
                $outputArray = array(
                    'now_day' => $now_day_name,
                    'content' => $snuffeen,
                    'par' => date('W'),
                );

                echo json_encode($outputArray);
            }
        } else echo("Spam");
    } elseif ($_POST['type'] == 'teacherSearch') {
        function getCaptcha($SecretKey)
        {
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc9JawUAAAAAI73s_3SH9GnZNKwlYq3FfOOEW2L&response={$SecretKey}");
            $Return = json_decode($Response);
            return $Return;
        }

        $Return = getCaptcha($_POST['g-recaptcha-response']);
        if ($Return->success == true && $Return->score > 0.5) {
            $parity = $_POST['parity'];
            $teacher = $_POST['teacher'];
            $is_teacher_exist = false;
            $teacher_id = 0;

            $db = DB::getDb();

            $result = $db->prepare("SELECT * FROM teachers ORDER BY `id` ASC");
            $result->execute();
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $teachersList = $result->fetchAll();
            $result = null;

            foreach ($teachersList as $item) {
                if ($item['name'] === $teacher) {
                    $is_teacher_exist = true;
                    $teacher_id = $item['id'];
                    break;
                }
            }

            if ($is_teacher_exist) {
                $result = $db->prepare('SELECT * FROM groups ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $groupsList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $subjectsList = $result->fetchAll();
                $result = null;

                $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
                $result->execute();
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                $cabinetsList = $result->fetchAll();
                $result = null;

//            $daysList = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');
                $daysList = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
                $groupsPartyList = array();

                $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

                $typesList = array('Лекция', 'Практика');

                $numbersList = array(1, 2, 3, 4, 5, 6, 7);

                $timesList = array('<p>8:00 - 9:30</p>', '<p>9:40 - 11:10</p>', '<p>11:30 - 13:00</p>',
                    '<p>13:10 - 14:40</p>', '<p>15:00 - 16:30</p>', '<p>16:40 - 18:10</p>', '<p>18:20 - 19:50</p>');

                $parity_now = '';

                if ((int)$parity == 1) {
                    $result = $db->prepare("SELECT * FROM teachers, lesson WHERE teachers.name = :teacherName AND 
                                    lesson.id_teacher = teachers.id AND (lesson.parity_week = :parity1 OR lesson.parity_week = :parity3)  
                                    ORDER BY lesson.day,lesson.number,lesson.subgroup ASC");
                    $result->bindParam(':teacherName', $teacher);
                    $result->bindValue(':parity1', $parity);
                    $result->bindValue(':parity3', $parity + 2);
                    $result->execute();
                    $searchedGroup = $result->fetchAll();
                    $result = null;
                    $parity_now = $parityList[0];
                } elseif ((int)$parity == 2) {
                    $result = $db->prepare("SELECT * FROM teachers, lesson WHERE teachers.name = :teacherName AND 
                                    lesson.id_teacher = teachers.id AND (lesson.parity_week = :parity2 OR lesson.parity_week = :parity3)  
                                    ORDER BY lesson.day,lesson.number,lesson.subgroup ASC");
                    $result->bindParam(':teacherName', $teacher);
                    $result->bindValue(':parity2', $parity);
                    $result->bindValue(':parity3', $parity + 1);
                    $result->execute();
                    $searchedGroup = $result->fetchAll();
                    $result = null;
                    $parity_now = $parityList[1];
                }

                $last_day = '1';
                $last_number = 1;
                $groups = array(array(), array(), array(), array(), array(), array(), array());

                foreach ($searchedGroup as $searchedItem) {
                    if ($searchedItem['day'] !== $last_day) {
                        array_push($groupsPartyList, $groups);
                        $groups = array(array(), array(), array(), array(), array(), array(), array());
                        $difference = (int)$searchedItem['day'] - $last_day;
//                    echo $difference;
                        if ($difference > 1) {
                            for ($i = 1; $i < $difference; $i++)
                                array_push($groupsPartyList, $groups);
                        }
                        $last_day = $searchedItem['day'];
                        $last_number = (int)$searchedItem['number'];
                    } elseif ((int)$searchedItem['number'] !== $last_number)
                        $last_number = (int)$searchedItem['number'];

                    foreach ($groupsList as $groupItem) {
                        if ($searchedItem['id_group'] === $groupItem['id']) {
                            $now_group = $groupItem['name'];
                            if (!in_array($now_group, $groups[$last_number - 1])) {
//                                if ((in_array()))
                                array_push($groups[$last_number - 1], $now_group);
                                break;
                            }
                        }
                    }
                }
                array_push($groupsPartyList, $groups);

                $feb_days = 28;
                // Високосный ли год?
                if (date('L'))
                    $feb_days = 29;
                $months_info = array(
                    array('January', 31, 'января'),
                    array('February', $feb_days, 'февраля'),
                    array('March', 31, 'марта'),
                    array('April', 30, 'апреля'),
                    array('May', 31, 'мая'),
                    array('June', 30, 'июня'),
                    array('July', 31, 'июля'),
                    array('August', 31, 'августа'),
                    array('September', 30, 'сентября'),
                    array('October', 31, 'октября'),
                    array('November', 30, 'ноября'),
                    array('December', 31, 'декабря'),
                );

                $snuffeen = '';
                $days_in_month = 0;
                $next_month = array();

                $now_month = date('F');
                for ($i = 0; $i <= 11; $i++)
                    if ($months_info[$i][0] == $now_month) {
                        $now_month = $months_info[$i][2];
                        $days_in_month = $months_info[$i][1];
                        if ($i != 11)
                            $next_month = $months_info[$i + 1];
                        else
                            $next_month = $months_info[0];
                        break;
                    }

                $now_day = date('j');
                $now_day_name = date('N') - 1;
                $diff_date = 0;

                $lower_day = $now_day - $now_day_name;
                $last_date = $lower_day;
                $first_week_days = array();
                $second_week_days = array();
                $check = -1;
                for ($i = 0; $i < 13; $i++) {
                    $check++;
                    $day = $lower_day + $check;
                    if ($i < 6)
                        array_push($first_week_days, $day);
                    elseif ($i > 6)
                        array_push($second_week_days, $day);

                    if ($day == $days_in_month) {
                        $days_in_month = $next_month[1];
                        $lower_day = 1;
                        $check = -1;
                    }
                }

                $snuffeen .= '<div class="table-teacher-place"><table><tbody><tr><td class="table-teacher-day-empty"></td>';
                foreach ($timesList as $time) {
                    $snuffeen .= '<td class="table-teacher-time">' . $time . '</td>';
                }
                $snuffeen .= '</tr>';
                // По дням
                foreach ($daysList as $key => $day) {
                    if ((int)$parity == 1)
                        $diff_date = $first_week_days[$key];
                    elseif ((int)$parity == 2)
                        $diff_date = $second_week_days[$key];
                    if ($last_date > $diff_date)
                        $now_month = $next_month[2];
                    $last_date = $diff_date;

                    $last_number = 0;
                    $snuffeen .= '<tr>';
                    if ($diff_date == $now_day)
                        $snuffeen .= '<td class="table-teacher-today"><p class="teacher-day-name">' . $day . '</p><p class="teacher-day-date">' . $now_day . ' ' . $now_month . '</p></td>';
                    else
                        $snuffeen .= '<td class="table-teacher-day"><p class="teacher-day-name">' . $day . "</p><p class='teacher-day-date'>" . $diff_date . " " . $now_month . '</p></td>';
//              По парам
                    foreach ($numbersList as $keyn => $number) {
                        $isFind = false;
                        foreach ($searchedGroup as $keys => $searched) {
                            if ((int)$searched['day'] === ($key + 1)) {
                                if ((int)$searched['number'] === ($keyn + 1) and (int)$searched['number'] !== $last_number) {
                                    foreach ($subjectsList as $key_subj => $subj) {
                                        if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                            $subj_name = $subj['name'];
                                            $last_number = $number;
                                            $groups_array = '';
                                            if (count($groupsPartyList[$key]) !== 0) {
                                                $count = (int)count($groupsPartyList[$key][$keyn]);
                                                if ($count < 4)
                                                    for ($i = 0; $i < $count; $i++) {
                                                        $groups_array .= (string)$groupsPartyList[$key][$keyn][$i];
                                                        if ($count == 1) {
                                                            if ((int)$searched['subgroup'] !== 3)
                                                                $groups_array .= '/' . $searched['subgroup'] . ' группа, ';
                                                            else
                                                                $groups_array .= ' группа, ';
                                                        } elseif ($i + 1 != $count)
                                                            $groups_array .= ', ';
                                                        else
                                                            $groups_array .= ' группы, ';
                                                    }
                                                else {
                                                    $groups_array .= (string)$groupsPartyList[$key][$keyn][0] . ' - ' . (string)$groupsPartyList[$key][$keyn][$count - 1] . ' группы, ';
                                                }
                                                if ((int)$searched['lesson_type'] - 1 == 0)
                                                    $snuffeen .= '<td class="table-teacher-info-lection">';
                                                else
                                                    $snuffeen .= '<td class="table-teacher-info">';
                                                $snuffeen .= '<span class="table-teacher-group">' . $groups_array . '</span>';

                                                foreach ($cabinetsList as $cabinetItem) {
                                                    if ($searched['id_cabinet'] == $cabinetItem['id']) {
                                                        $snuffeen .= '<span class="table-teacher-cabinet">' . $cabinetItem["name"] . " каб." . '</span>';
                                                        $isFind = true;
                                                        break;
                                                    }
                                                }

                                                $snuffeen .= '<p class="table-teacher-lesson">' . $subj_name . '</p>';
                                                $snuffeen .= '</td>';
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($isFind == false) {
                            $snuffeen .= '<td class="table-teacher-info">';
                            $snuffeen .= '<span class="table-teacher-group"></span>';
                            $snuffeen .= '<span class="table-teacher-cabinet"></span>';
                            $snuffeen .= '<p class="table-teacher-lesson"></p>';
                            $snuffeen .= '</td>';
                        }
                    }
                    $snuffeen .= '</tr>';
                }

                $snuffeen .= '</tbody></table></div>';

                // Для свайпера
                $timesList = array('<p id="t1">8:00</p><p id="t2">9:30</p>', '<p id="t1">9:40</p><p id="t2">11:10</p>', '<p id="t1">11:30</p><p id="t2">13:00</p>',
                    '<p id="t1">13:10</p><p id="t2">14:40</p>', '<p id="t1">15:00</p><p id="t2">16:30</p>',
                    '<p id="t1">16:40</p><p id="t2">18:10</p>', '<p id="t1">18:20</p><p id="t2">19:50</p>');

                $for_swiper = '';
                $empty_td = 1;

                $days_in_month = 0;
                $next_month = array();

                $now_month = date('F');
                for ($i = 0; $i <= 11; $i++)
                    if ($months_info[$i][0] == $now_month) {
                        $now_month = $months_info[$i][2];
                        $days_in_month = $months_info[$i][1];
                        if ($i != 11)
                            $next_month = $months_info[$i + 1];
                        else
                            $next_month = $months_info[0];
                        break;
                    }

                $now_day = date('j');
                $now_day_name = date('N') - 1;
                $diff_date = 0;

                $lower_day = $now_day - $now_day_name;
                $last_date = $lower_day;
                $first_week_days = array();
                $second_week_days = array();
                $check = -1;
                for ($i = 0; $i < 13; $i++) {
                    $check++;
                    $day = $lower_day + $check;
                    if ($i < 6)
                        array_push($first_week_days, $day);
                    elseif ($i > 6)
                        array_push($second_week_days, $day);

                    if ($day == $days_in_month) {
                        $days_in_month = $next_month[1];
                        $lower_day = 1;
                        $check = -1;
                    }
                }


                // По дням
                foreach ($daysList as $key => $day) {
                    $last_number = 0;
                    if ((int)$parity == 1)
                        $diff_date = $first_week_days[$key];
                    elseif ((int)$parity == 2)
                        $diff_date = $second_week_days[$key];

                    if ($last_date > $diff_date)
                        $now_month = $next_month[2];
                    $last_date = $diff_date;

                    $for_swiper .= '<div class="swiper-slide">';
                    $for_swiper .= '<div class="tableplace" id="table' . ($key + 1) . '">';
                    if ($diff_date == $now_day) {
                        $for_swiper .= '<h4 id="tableDay" style="background: #126984; color: #e8edf4;">';
                        $for_swiper .= $day . " | Сегодня, " . $now_day . " " . $now_month;
                    } else {
                        $for_swiper .= '<h4 id="tableDay">';
                        $for_swiper .= $day . " | " . $diff_date . " " . $now_month;
                    }
                    $for_swiper .= '</h4>';
                    $for_swiper .= '<table><tbody>';
                    // По парам
                    foreach ($numbersList as $keyn => $number) {
                        $isFind = false;
                        $is_lection = false;
                        $for_swiper .= '<tr>';
                        // Проход по списку запрошенных пар

                        foreach ($searchedGroup as $keys => $searched) {
                            // Общая пара
                            if ((int)$searched["day"] === ($key + 1)) {
                                if ((int)$searched["number"] === $keyn + 1 and (int)$searched['number'] !== $last_number) {
                                    foreach ($subjectsList as $subj) {
                                        if ((int)$searched["id_subject"] === (int)$subj["id"]) {
                                            $last_number = $number;
                                            $groups_array = '';
                                            if (count($groupsPartyList[$key]) !== 0) {
                                                $count = (int)count($groupsPartyList[$key][$keyn]);
                                                if ($count < 4)
                                                    for ($i = 0; $i < $count; $i++) {
                                                        $groups_array .= (string)$groupsPartyList[$key][$keyn][$i];
                                                        if ($count == 1) {
                                                            if ((int)$searched['subgroup'] !== 3)
                                                                $groups_array .= '/' . $searched['subgroup'] . ' группа';
                                                            else
                                                                $groups_array .= ' группа';
                                                        } elseif ($i + 1 != $count)
                                                            $groups_array .= ', ';
                                                        else
                                                            $groups_array .= ' группы';
                                                    } else {
                                                    $groups_array .= (string)$groupsPartyList[$key][$keyn][0] . ' - ' . (string)$groupsPartyList[$key][$keyn][$count - 1] . ' группы, ';
                                                }
                                                $for_swiper .= '<td class="time" id="time' . $searched["id"] . '">' . $timesList[$keyn] . '</td>';
                                                if ((int)$searched["lesson_type"] - 1 == 0 and (int)$searched["day"] == $key + 1 and (int)$searched["number"] == $keyn + 1 and $is_lection == false) {
                                                    $for_swiper .= '<td class="lection-yes"><p>' . '</p></td>';
                                                } elseif ($is_lection == false) {
                                                    $for_swiper .= '<td class="lection"><p>' . '</p></td>';
                                                }
                                                $is_lection = true;
                                                $for_swiper .= '<td class="info" colspan="3" id="' . $searched["id"] . '">' . '<p style="font-weight: bold" id="subject' . $searched["id"] . '">' . $subj["name"] . '</p>';
                                                $for_swiper .= '<p class="groupsList">' . $groups_array . '</p></td>';

                                                foreach ($cabinetsList as $cabinet) {
                                                    if ((int)$searched["id_cabinet"] === (int)$cabinet["id"]) {
                                                        $for_swiper .= '<td class="place" id="place' . $searched["id"] . '"><p id="cabinet' . $searched["id"] . '">' . $cabinet["name"] . '</p></td>';
                                                        $isFind = true;
                                                        break;
                                                    }
                                                }
                                                if ($isFind) {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Если пары нет вообще
                        if ($isFind == false) {
                            $for_swiper .= '<td class="time">' . $timesList[$keyn] . '</td>';
                            $for_swiper .= '<td class="lection"><p>' . '</p></td>';
                            $for_swiper .= '<td class="info" colspan="3" id="emptyinfo' . $empty_td . '">' . '<p style="font-weight: bold"></p>';
                            $for_swiper .= '<p><a href="#"></a></p></td>';
                            $for_swiper .= '<td class="place" id="emptyplace' . $empty_td . '"><p></p></td>';
                            $empty_td += 1;
                        }
                        $for_swiper .= '</tr>';
                    }

                    // Конец дня
                    $for_swiper .= '</tbody></table>';
                    $for_swiper .= '</div></div>';
                }

                $outputArray = array(
                    'now_day' => $now_day_name,
                    'content' => $snuffeen,
                    'swiper_content' => $for_swiper,
                );

                echo json_encode($outputArray);
            } else
                echo "Undefined teacher";
        } else echo("Spam");
    } elseif ($_POST['type'] == 'deleteLesson') {
        $delete_id = (int)$_POST['id'];
        $db = DB::getDb();

        $result = $db->prepare("DELETE FROM `lesson` WHERE `id` = :id");
        $result->bindParam(':id', $delete_id);
        $result->execute();
        $result = null;

        echo("Deleted successfully");

    } elseif ($_POST['type'] == 'demoAdd') {
        $demo_id = $_POST['id'];
        $day = $_POST['day'];
        $subgroup = $_POST['subgroup'];
        $number = $_POST['number'];
        $global_parity = $_POST['parity-global'];
        $groupID = $_POST['groupID'];

        $selects = '';

        $db = DB::getDB();
        $result = $db->prepare('SELECT * FROM groups ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $groupsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $subjectsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $teachersList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM sub_groups ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $subsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $cabinetsList = $result->fetchAll();
        $result = null;

        $daysList = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');

        $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

        $typesList = array('Лекция', 'Практика');

        $timesArray = array('8:00 - 9:30', '9:40 - 11:10', '11:30 - 13:00', '13:10 - 14:40', '15:00 - 16:30', '16:40 - 18:10', '18:20 - 19:50');

        $selects .= '<p class="whatDay" id="whatDay' . $demo_id . '">' . $daysList[$day - 1] . '</p>';

        $selects .= '<div id="divForSelectTeachers" class="divTeachers">';
        $selects .= '<select id="teachersList" class="selectWhatTeacher">';
        $selects .= '<option selected disabled hidden value="0">Преподаватель</option>';
        foreach ($teachersList as $keyt => $teacher) {
            $selects .= '<option value="' . $teacher["id"] . '">' . $teacher["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        if ((int)$subgroup === 3) {
            $selects .= '<div id="divForSelectSubgroup" class="divSubgroups">';
            $selects .= '<select id="subgroupsList" class="selectWhatSubgroup">';
            $selects .= '<option selected disabled hidden value="0">П/гр</option>';
            foreach ($subsList as $keysu => $sub) {
                if ((int)$sub["id"] != 3)
                    $selects .= '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
                else
                    $selects .= '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
            }
            $selects .= '</select>';
            $selects .= '</div>';
        } elseif ((int)$subgroup === 1) {
            $selects .= '<div id="divForSelectSubgroup" class="divHoldSubgroup">';
            $selects .= '<p class="whatHoldSubgroup" id="whatSubgroup' . $demo_id . '" style="padding-top: 1%">1 пг</p>';
            $selects .= '</div>';
        } elseif ((int)$subgroup === 2) {
            $selects .= '<div id="divForSelectSubgroup" class="divHoldSubgroup">';
            $selects .= '<p class="whatHoldSubgroup" id="whatSubgroup' . $demo_id . '" style="padding-top: 1%">2 пг</p>';
            $selects .= '</div>';
        }

        $selects .= '<div id="divForSelectCabinets" class="divCabinets">';
        $selects .= '<select id="cabinetsList" class="selectWhatCabinet">';
        $selects .= '<option selected disabled hidden value="0">Ауд</option>';
        foreach ($cabinetsList as $keyca => $cabinet) {
            $selects .= '<option value="' . $cabinet["id"] . '">' . $cabinet["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectSubjects" class="divSubjects">';
        $selects .= '<select id="subejctsList" class="selectWhatSubject">';
        $selects .= '<option selected disabled hidden value="0">Предмет</option>';
        foreach ($subjectsList as $keys => $subject) {
            $selects .= '<option value="' . $subject["id"] . '">' . $subject["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectTime" class="divTime">';
        $selects .= '<p class="whatTimeAdd" id="whatTime' . $demo_id . '">' . ($number + 1) . " пара (" . $timesArray[$number] . ")" . '</p>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectTypes" class="divTypes">';
        $selects .= '<select id="typesList" class="selectWhatType">';
        $selects .= '<option selected disabled hidden value="0">Тип</option>';
        foreach ($typesList as $keyt => $type) {
            $selects .= '<option value="' . ($keyt + 1) . '">' . $type . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectParity" class="divParity">';
        $selects .= '<select id="parityList" class="selectWhatParity">';
        $selects .= '<option selected disabled hidden value="0">Неделя</option>';
        foreach ($parityList as $keyp => $parity) {
            $selects .= '<option value="' . ($keyp + 1) . '">' . $parity . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<button id="saveLesson" onclick="save(' . $day . ',' . $global_parity . ',' . (int)$subgroup . ',' . $number . ',' . $groupID . ')">Сохранить</button>';
        $selects .= '<button id="cancel" onclick="cancel()" >Отменить</button>';

        echo $selects;

    } elseif ($_POST['type'] == 'demoUpdate') {
        $demo_id = $_POST['id'];
        $day = $_POST['day'];
        $number = $_POST['number'];
        $groupID = $_POST['groupID'];

        $subgroup_name = $_POST['subgroup'];
        $type_lesson_name = $_POST['type_lesson'];
        $parity_name = $_POST['parity'];
        $old_teacher_name = $_POST['teacher'];
        $old_subject_name = $_POST['subject'];
        $old_cabinet_name = $_POST['cabinet'];

        $selects = '';

        $db = DB::getDB();
        $result = $db->prepare('SELECT * FROM groups ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $groupsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $subjectsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $teachersList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM sub_groups ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $subsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $cabinetsList = $result->fetchAll();
        $result = null;

        $daysList = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');

        $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

        $typesList = array('Лекция', 'Практика');

        $timesArray = array('8:00 - 9:30', '9:40 - 11:10', '11:30 - 13:00', '13:10 - 14:40', '15:00 - 16:30', '16:40 - 18:10', '18:20 - 19:50');

        if ($subgroup_name == '1 пг') {
            $subgroup_name = '1';
        } elseif ($subgroup_name == '2 пг') {
            $subgroup_name = '2';
        }

        $selects .= '<p class="whatDay" id="whatDay' . $demo_id . '">' . $daysList[$day - 1] . '</p>';

        $selects .= '<div id="divForSelectTeachers" class="divTeachers">';
        $selects .= '<select id="teachersList" class="selectWhatTeacher">';
        $selects .= '<option selected disabled hidden value="0">' . $old_teacher_name . '</option>';
        foreach ($teachersList as $keyt => $teacher) {
            $selects .= '<option value="' . $teacher["id"] . '">' . $teacher["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectSubgroup" class="divSubgroups">';
        $selects .= '<select id="subgroupsList" class="selectWhatSubgroup">';
        $selects .= '<option selected disabled hidden value="0">' . $subgroup_name . '</option>';
        foreach ($subsList as $keysu => $sub) {
            if ((int)$sub["id"] != 3)
                $selects .= '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
            else
                $selects .= '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectCabinets" class="divCabinets">';
        $selects .= '<select id="cabinetsList" class="selectWhatCabinet">';
        $selects .= '<option selected disabled hidden value="0">' . $old_cabinet_name . '</option>';
        foreach ($cabinetsList as $keyca => $cabinet) {
            $selects .= '<option value="' . $cabinet["id"] . '">' . $cabinet["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectSubjects" class="divSubjects">';
        $selects .= '<select id="subejctsList" class="selectWhatSubject">';
        $selects .= '<option selected disabled hidden value="0">' . $old_subject_name . '</option>';
        foreach ($subjectsList as $keys => $subject) {
            $selects .= '<option value="' . $subject["id"] . '">' . $subject["name"] . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectTime" class="divTime">';
        $selects .= '<p class="whatTimeAdd" id="whatTime' . $demo_id . '">' . ($number + 1) . " пара (" . $timesArray[$number] . ")" . '</p>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectTypes" class="divTypes">';
        $selects .= '<select id="typesList" class="selectWhatType">';
        $selects .= '<option selected disabled hidden value="0">' . $type_lesson_name . '</option>';
        foreach ($typesList as $keyt => $type) {
            $selects .= '<option value="' . ($keyt + 1) . '">' . $type . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<div id="divForSelectParity" class="divParity">';
        $selects .= '<select id="parityList" class="selectWhatParity">';
        $selects .= '<option selected disabled hidden value="0">' . $parity_name . '</option>';
        foreach ($parityList as $keyp => $parity) {
            $selects .= '<option value="' . ($keyp + 1) . '">' . $parity . '</option>';
        }
        $selects .= '</select>';
        $selects .= '</div>';

        $selects .= '<button id="updateLesson">Сохранить</button>';
        $selects .= '<button id="cancel" onclick="cancel()" >Отменить</button>';

        echo $selects;

    } elseif ($_POST['type'] == 'Update') {

        $db = DB::getDB();

        $result = $db->prepare('SELECT * FROM subjects ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $subjectsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM teachers ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $teachersList = $result->fetchAll();
        $result = null;

        $result = $db->prepare('SELECT * FROM cabinets ORDER BY id ASC');
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $cabinetsList = $result->fetchAll();
        $result = null;

        $parityList = array('1-я неделя', '2-я неделя', 'Обе недели');

        $update_id = (int)$_POST['id'];
        $parity_name = $_POST['parity'];
        if ($parity_name == $parityList[0]) {
            $parity_name = 1;
        } elseif ($parity_name == $parityList[1]) {
            $parity_name = 2;
        } elseif ($parity_name == $parityList[2]) {
            $parity_name = 3;
        }
        $groupID = (int)$_POST['groupID'];
        $day = (int)$_POST['day'];
        $subgroup = $_POST['subgroup'];
        if ($subgroup == '1 пг') {
            $subgroup = 1;
        } elseif ($subgroup == '2 пг') {
            $subgroup = 2;
        } elseif ($subgroup == 'Общая') {
            $subgroup = 3;
        }
        $type = $_POST['type_lesson'];

        if ($type == 'Лекция') {
            $type = 1;
        } elseif ($type == 'Практика') {
            $type = 2;
        }

        $number = (int)$_POST['number'];
        $subject_name = $_POST['subject'];
        foreach ($subjectsList as $subj) {
            if ($subject_name == $subj['name']) {
                $subject_name = (int)$subj['id'];
                break;
            }
        }
        $teacher_name = $_POST['teacher'];
        foreach ($teachersList as $teach) {
            if ($teacher_name == $teach['name']) {
                $teacher_name = (int)$teach['id'];
                break;
            }
        }
        $cabinet_name = $_POST['cabinet'];
        foreach ($cabinetsList as $cab) {
            if ($cabinet_name == $cab['name']) {
                $cabinet_name = (int)$cab['id'];
                break;
            }
        }

        $result = $db->prepare('UPDATE lesson SET `id_group` = :groupID, `day` = :dayID, `parity_week` = :parityID, `lesson_type` = :typeID, 
                                     `number` = :numberID, `id_subject` = :subjectID, `id_teacher` = :teacherID, `subgroup` = :subgroupID, `id_cabinet` = :cabinetID WHERE `id` = :id');
        $result->bindParam(':id', $update_id);
        $result->bindParam(':groupID', $groupID);
        $result->bindParam(':dayID', $day);
        $result->bindParam(':parityID', $parity_name);
        $result->bindParam(':typeID', $type);
        $result->bindParam(':numberID', $number);
        $result->bindParam(':subjectID', $subject_name);
        $result->bindParam(':teacherID', $teacher_name);
        $result->bindParam(':subgroupID', $subgroup);
        $result->bindParam(':cabinetID', $cabinet_name);
        $result->execute();
        $result = null;
    } elseif ($_POST['type'] == 'autocomplete') {
        $db = DB::getDb();
        $groupsArray = array();
        $teachersArray = array();

        $result = $db->prepare("SELECT * FROM `groups` ORDER BY id ASC");
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $groupsList = $result->fetchAll();
        $result = null;

        $result = $db->prepare("SELECT * FROM `teachers` ORDER BY id ASC");
        $result->execute();
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $teachersList = $result->fetchAll();
        $result = null;

        foreach ($groupsList as $group)
            array_push($groupsArray, $group['name']);
        foreach ($teachersList as $teacher)
            array_push($teachersArray, $teacher['name']);

        sort($groupsArray);
        sort($teachersArray);

        $resultArray = array(
            'groups' => $groupsArray,
            'teachers' => $teachersArray,
        );

        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }
} else {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/PHPExcel.php";
    $allowed_file_formats = array('xlsx', 'xls', 'csv');
    $filename = $_FILES[0]['name'];
    $format = pathinfo($filename, PATHINFO_EXTENSION);

    if (in_array($format, $allowed_file_formats)) {
        $excel = \PHPExcel_IOFactory::load($_FILES[0]['tmp_name']);

        $blackList = array('B', 'D', 'E');
        $blackListWords = array('доцент', 'профессор', 'професссор', 'ст.', 'преподаватель');

        $directionsArray = array();
        $departmentsArray = array();
        $lessonsArray = array();
        $groupsArray = array();
        $subjectsArray = array();
        $teachersArray = array("", "n/a");
        $cabinetsArray = array("n/a", "Зал/Стадион");

        $infoArray = array();
        $lastInfoArray = array("Subject" => "");

        $globalGroup = 0;
        $globalGroupName = "";
        $globalSubGroup = 0;
        $globalDepartment = "";
        $prevGlobalDepartment = "";
        $nowGroupCellValue = "";
        $lastGroupCellValue = "";

        $prevDayStartIndex = 0;
        $nextDayStartIndex = 0;
        $daysIndex = 0;

        $prevNumberStartIndex = 0;
        $nextNumberStartIndex = 0;
        $numbersIndex = 0;

        $skipThisColumnGroup = false;
        $skipThisColumnDepartment = false;
        $list = -1;

        foreach ($excel->getWorksheetIterator() as $worksheet) {
            $list++;
            $excel->setActiveSheetIndex($list);
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $numbersBorders = array();
            $daysBorders = array();
            foreach ($worksheet->getColumnIterator() as $keyCol => $column) {
//        if (!$skipThisColumnGroup) {
                $nowColumnIndex = $column->getColumnIndex();
//        if ($nowColumnIndex != "H" && $list != -1)
                if (!in_array($nowColumnIndex, $blackList)) {
                    $cellIterator = $column->getCellIterator();

//-------------- Запоминание границ дней
                    if ($nowColumnIndex == 'A') {
                        foreach ($cellIterator as $keyCell => $cell) {
                            if ($cell->isMergeRangeValueCell()) {
                                $cellValue = $cell->getValue();
                                $cellValue = trim($cellValue);
                                if ($cellValue !== '' && $cellValue !== 'Дни недели') {
                                    array_push($daysBorders, $keyCell);
                                }
                            }
                        }
                        var_dump($daysBorders);
                    }

//-------------- Запоминание границ пар
                    if ($nowColumnIndex == 'C') {
                        $num = 0;
                        $nowDayStartIndex = $daysBorders[$num];
                        $nextDayStartIndex = $daysBorders[$num + 1];
                        $nowNumberBorders = array();
                        foreach ($cellIterator as $keyCell => $cell) {
                            if ($keyCell == $nextDayStartIndex) {
                                $num++;
                                if ($num < 5) {
                                    $nowDayStartIndex = $daysBorders[$num];
                                    $nextDayStartIndex = $daysBorders[$num + 1];
                                } else {
                                    $nextDayStartIndex = $highestRow + 1;
                                }
                                array_push($numbersBorders, $nowNumberBorders);
                                $nowNumberBorders = array();
                            }
                            if ($cell->isMergeRangeValueCell()) {
                                $cellValue = $cell->getValue();
                                $cellValue = trim($cellValue);
                                if ($cellValue !== '' && $cellValue !== 'Пары часов' && $keyCell >= $nowDayStartIndex && $keyCell < $nextDayStartIndex) {
                                    array_push($nowNumberBorders, $keyCell);
                                }
                            }
                        }
                        array_push($numbersBorders, $nowNumberBorders);
                        var_dump($numbersBorders);
                    }

//-------------- Проход по группам (основная информация)
                    if ($nowColumnIndex !== 'A' && $nowColumnIndex !== 'C') {
                        $daysIndex = 0;
                        $nextDayStartIndex = $daysBorders[$daysIndex];
                        $numbersIndex = 0;
                        $nextNumberStartIndex = $numbersBorders[$daysIndex][$numbersIndex];
                        foreach ($cellIterator as $keyCell => $cell) {
                            if ($keyCell == 6) {
                                if ($prevGlobalDepartment == $globalDepartment && $globalDepartment != "") {
                                    if (preg_match("@/@u", $lastGroupCellValue) && preg_match("@/@u", $nowGroupCellValue)) {
                                        $lastGroupInfo = explode("/", $lastGroupCellValue);
                                        $nowGroupInfo = explode("/", $nowGroupCellValue);
                                        if ((int)$lastGroupInfo[0] == (int)$nowGroupInfo[0]) {
                                            $skipThisColumnGroup = true;
                                            $skipThisColumnDepartment = true;
                                        } else {
                                            $globalSubGroup = 1;
                                        }
                                    } elseif (is_numeric($lastGroupCellValue) && is_numeric($nowGroupCellValue)) {
                                        if ((int)$lastGroupCellValue == (int)$nowGroupCellValue) {
                                            $skipThisColumnGroup = true;
                                            $skipThisColumnDepartment = true;
                                        } else {
                                            $globalSubGroup = 1;
                                        }
                                    }
                                } elseif ($prevGlobalDepartment != $globalDepartment && $globalDepartment != "") {
                                    if (preg_match("@/@u", $lastGroupCellValue) && preg_match("@/@u", $nowGroupCellValue)) {
                                        $lastGroupInfo = explode("/", $lastGroupCellValue);
                                        $nowGroupInfo = explode("/", $nowGroupCellValue);
                                        if ((int)$lastGroupInfo[0] == (int)$nowGroupInfo[0]) {
                                            if ((int)$nowGroupInfo[1] == 1) {
                                                $globalDepartment = $prevGlobalDepartment;
                                                $nowGroupCellValue = $lastGroupCellValue;
                                                $skipThisColumnGroup = true;
                                                $skipThisColumnDepartment = true;
                                            } else {
                                                $globalSubGroup = 2;
                                            }
                                        }
                                    } elseif (is_numeric($lastGroupCellValue) && is_numeric($nowGroupCellValue)) {
                                        if ((int)$lastGroupCellValue == (int)$nowGroupCellValue) {
                                            $globalSubGroup = 2;
                                        } else {
                                            $globalSubGroup = 1;
                                        }
                                    }
                                }
                            }
                            if (!$skipThisColumnGroup || !$skipThisColumnDepartment) {
                                if ($keyCell !== 1 && $keyCell < $highestRow) {
                                    $cellValue = $cell->getValue();
                                    if ($keyCell == 2) {
                                        $cellValue = trim($cellValue);
                                        if ($cellValue != "" && !in_array($cellValue, $directionsArray)) {
                                            array_push($directionsArray, $cellValue);
                                        }
                                    } else {
                                        echo $nowColumnIndex . $keyCell . "\n";
                                        // Если ячейка пустая запускается проверка
                                        // "Не является ли эта ячейка объединенной и НЕ главной"
                                        // (т.к. getValue объединенной ячейки возвращает пустую строку)
                                        if ($cellValue == '') {
                                            $cellIteratorMerged = $excel->getActiveSheet()->getMergeCells();
                                            foreach ($cellIteratorMerged as $keyCellMerged => $cellMerged) {
                                                if ($cell->isInRange($cellMerged)) {
                                                    $currMergedCellsArray = \PHPExcel_Cell::splitRange($cellMerged);
                                                    $cell = $excel->getActiveSheet()->getCell($currMergedCellsArray[0][0]);
                                                    $cellValue = $cell->getValue();
                                                    break;
                                                }
                                            }
                                        }

//------------------------------ Приведение информации к удобному виду
                                        foreach ($blackListWords as $blackListWord) {
                                            $cellValue = str_replace($blackListWord, "", $cellValue);
                                        }

                                        $cellValue = str_replace("ПолетайкинА.Н.", "Полетайкин А.Н.", $cellValue);
                                        $cellValue = str_replace("М а т е м а т и ч е с к а я    л о г и к а    и    д и с к р е т н а я    м а т е м а т и к а",
                                            "Математическая логика и дискретная математика", $cellValue);
                                        $cellValue = str_replace("М а т е м а т и ч е с к а я   л о г и к а   и   д и с к р е т н а я   м а т е м а т и к а ",
                                            "Математическая логика и дискретная математика", $cellValue);
                                        $cellValue = str_replace("А л  г е б р а   и   а н а л и т и ч е с к а я   г е о м е т р и я",
                                            "Алгебра и аналитическая геометрия", $cellValue);
                                        $cellValue = str_replace("А л  г е б р а   и   а н а л и т и ч е с ка я   г е о м е т р и я",
                                            "Алгебра и аналитическая геометрия", $cellValue);
                                        $cellValue = str_replace("А л г е б р а   и   а н а л и т и ч е с к а я   г е о м е т р и я",
                                            "Алгебра и аналитическая геометрия", $cellValue);
                                        $cellValue = str_replace("А л г е б р а", "Алгебра", $cellValue);
                                        $cellValue = str_replace("П р а в о в а я    к у л ь т у р а", "Правовая культура", $cellValue);
                                        $cellValue = str_replace("П р а в о в е д е н и е", "Правоведение", $cellValue);
                                        $cellValue = str_replace("Э к с п е р т н ы е    с и с т е м ы", "Экспертные системы", $cellValue);
                                        $cellValue = str_replace("Б  е  з  о  п  а  с  н  о  с  т  ь      ж  и  з  н  е  д  е  я  т  е  л  ь  н  о  с  т  и",
                                            "Безопасность жизнедеятельности", $cellValue);
                                        $cellValue = str_replace("Э л е к т и в н ы е   д и с ц и п л и н ы   п о   ф и з и ч е с к о й   к у л ь т у р е   и   с п о р т у",
                                            "Элективные дисциплины по физической культуре и спорту", $cellValue);
                                        $cellValue = str_replace("О с н о в ы  п р о г р а м м и р о в а н и я ", "Основы программирования", $cellValue);
                                        $cellValue = str_replace("О с н о в ы   п р о г р а м м и р о в а н и я", "Основы программирования", $cellValue);
                                        $cellValue = str_replace("О с н о в ы    п р о г р а м м и р о в а н и я", "Основы программирования", $cellValue);
                                        $cellValue = str_replace("Ф и з и к а", "Физика", $cellValue);
                                        $cellValue = str_replace("Ф  и  з  и  к  а", "Физика", $cellValue);

                                        $cellValue = str_replace("\n", "", $cellValue);
                                        $cellValue = preg_replace("/  +/", " ", $cellValue);

                                        $cellValue = str_replace("яык", "язык", $cellValue);
                                        $cellValue = str_replace("Ауд", "ауд", $cellValue);
                                        $cellValue = str_replace("ауд ", "ауд.", $cellValue);
                                        $cellValue = str_replace("вычислительных", "выч.", $cellValue);
                                        $cellValue = str_replace("технологических", "техн.", $cellValue);
                                        $cellValue = str_replace("экологических", "эколог.", $cellValue);
                                        $cellValue = str_replace("экономических", "эконом.", $cellValue);

                                        $cellValue = trim($cellValue);

//------------------------------ Проверка текущей пары
                                        if ($keyCell == $nextDayStartIndex) {
                                            $numbersIndex = 0;
                                            $daysIndex++;
                                            $prevDayStartIndex = $nextDayStartIndex;
                                            if ($daysIndex < 6) {
                                                $nextDayStartIndex = $daysBorders[$daysIndex];
                                            } else {
                                                $nextDayStartIndex = $daysBorders[0];
                                            }
                                        }

//------------------------------ Проверка текущего дня недели
                                        if ($keyCell == $nextNumberStartIndex) {
                                            $numbersIndex++;
                                            $prevNumberStartIndex = $nextNumberStartIndex;
                                            if ($numbersIndex < 7) {
                                                $nextNumberStartIndex = $numbersBorders[$daysIndex - 1][$numbersIndex];
                                            } else {
                                                $nextNumberStartIndex = $nextDayStartIndex;
                                            }
                                        }

                                        if ($globalDepartment != "") {
                                            $globalDepartmentIndex = array_search($globalDepartment, $departmentsArray) + 1;
                                        } else {
                                            $globalDepartmentIndex = null;
                                        }

                                        $infoArray = array(
                                            "Subject" => "",
                                            "Teacher" => 1,
                                            "Type" => "",
                                            "Parity" => "",
                                            "Group" => $globalGroup,
                                            "Subgroup" => $globalSubGroup,
                                            "Cabinet" => 1,
                                            "Day" => $daysIndex,
                                            "Number" => $numbersIndex,
                                            "Department" => $globalDepartmentIndex,
                                        );

// -------------------------------------------------------------------------------------------------------------------------
// -----------------------------  Если лекция
                                        if (preg_match('@лк@u', $cellValue)) {
                                            $isCabExist = true;
                                            if (substr($cellValue, -7) != "ауд.") {
                                                $cellValue = str_replace("ауд.", "лк", $cellValue);
                                            } else {
                                                $cellValue = str_replace(" ауд.", "", $cellValue);
                                                $isCabExist = false;
                                            }
                                            $cellValue = str_replace("лк", " лк ", $cellValue);
                                            $cellValue = preg_replace("/  +/", " ", $cellValue);
                                            echo $cellValue;
                                            $infoArrayDemo = explode(" лк ", $cellValue);
                                            $infoArrayDemo[1] = str_replace(",", "", $infoArrayDemo[1]);
                                            $infoArrayDemo[1] = trim($infoArrayDemo[1]);

                                            if (substr($infoArrayDemo[1], -1) != ".") {
                                                $infoArrayDemo[1] = $infoArrayDemo[1] . ".";
                                            }

                                            if (!in_array($infoArrayDemo[0], $subjectsArray)) {
                                                echo "___" . $infoArrayDemo[0] . " " . $nowColumnIndex . " " . $keyCell;
                                                array_push($subjectsArray, $infoArrayDemo[0]);
                                            }
                                            $infoArray["Subject"] = array_search($infoArrayDemo[0], $subjectsArray) + 1;
                                            if (!in_array($infoArrayDemo[1], $teachersArray)) {
                                                array_push($teachersArray, $infoArrayDemo[1]);
                                            }
                                            $infoArray["Teacher"] = array_search($infoArrayDemo[1], $teachersArray) + 1;
                                            $infoArray["Type"] = 1;
                                            var_dump($infoArrayDemo);
                                            if ($globalGroupName == "42" && $globalDepartment == "КММ" && $infoArrayDemo[0] == "Статический и многомерный анализ данных") {
                                                $infoArray["Subgroup"] = 1;
                                            } else {
                                                $infoArray["Subgroup"] = 3;
                                            }

                                            // Так как лекция всегда на целую группу (или несколько групп), то не важна кафедра
                                            $infoArray["Department"] = null;

                                            if ($isCabExist) {
                                                if (!in_array($infoArrayDemo[2], $cabinetsArray)) {
                                                    array_push($cabinetsArray, $infoArrayDemo[2]);
                                                }
                                                $infoArray["Cabinet"] = array_search($infoArrayDemo[2], $cabinetsArray) + 1;
                                            } else {
                                                array_push($infoArrayDemo, "n/a");
                                            }

                                            var_dump($infoArrayDemo);
// ----------------------------- Если лабораторная
                                        } elseif (preg_match('@лаб@u', $cellValue)) {
                                            $isCabExist = true;
                                            echo $cellValue;
                                            if (substr($cellValue, -7) != "ауд.") {
                                                $cellValue = str_replace("ауд.", "лаб", $cellValue);
                                            } else {
                                                $cellValue = str_replace(" ауд.", "", $cellValue);
                                                $isCabExist = false;
                                            }
                                            $cellValue = str_replace("лаб", " лаб ", $cellValue);
                                            $cellValue = preg_replace("/  +/", " ", $cellValue);
                                            $infoArrayDemo = explode(" лаб ", $cellValue);
                                            $infoArrayDemo[1] = str_replace(",", "", $infoArrayDemo[1]);
                                            $infoArrayDemo[1] = trim($infoArrayDemo[1]);
                                            if (substr($infoArrayDemo[1], -1) != ".") {
                                                $infoArrayDemo[1] = $infoArrayDemo[1] . ".";
                                            }
                                            if (!in_array($infoArrayDemo[0], $subjectsArray)) {
                                                echo "___" . $infoArrayDemo[0] . " " . $nowColumnIndex . " " . $keyCell;
                                                array_push($subjectsArray, $infoArrayDemo[0]);
                                            }
                                            $infoArray["Subject"] = array_search($infoArrayDemo[0], $subjectsArray) + 1;
                                            if (!in_array($infoArrayDemo[1], $teachersArray)) {
                                                array_push($teachersArray, $infoArrayDemo[1]);
                                            }
                                            $infoArray["Teacher"] = array_search($infoArrayDemo[1], $teachersArray) + 1;
                                            $infoArray["Type"] = 2;

                                            if ($isCabExist) {
                                                if (!in_array($infoArrayDemo[2], $cabinetsArray)) {
                                                    array_push($cabinetsArray, $infoArrayDemo[2]);
                                                }
                                                $infoArray["Cabinet"] = array_search($infoArrayDemo[2], $cabinetsArray) + 1;
                                            } else {
                                                array_push($infoArrayDemo, "n/a");
                                            }

                                            $infoArray["Subgroup"] = $globalSubGroup;

                                            var_dump($infoArrayDemo);
// -----------------------------  Если ПР))
                                        } elseif (preg_match('@пр@u', $cellValue)) {
                                            $isCabExist = true;
                                            if (substr($cellValue, -7) != "ауд.") {
                                                $cellValue = str_replace("ауд.", "пр", $cellValue);
                                            } else {
                                                $cellValue = str_replace(" ауд.", "", $cellValue);
                                                $isCabExist = false;
                                            }
                                            $cellValue = str_replace("пр", " пр ", $cellValue);
                                            $cellValue = preg_replace("/  +/", " ", $cellValue);
                                            $infoArrayDemo = explode(" пр ", $cellValue);
                                            $infoArrayDemo[1] = str_replace(",", "", $infoArrayDemo[1]);
                                            $infoArrayDemo[1] = trim($infoArrayDemo[1]);
                                            if (substr($infoArrayDemo[1], -1) != ".") {
                                                $infoArrayDemo[1] = $infoArrayDemo[1] . ".";
                                            }
                                            if (!in_array($infoArrayDemo[0], $subjectsArray)) {
                                                array_push($subjectsArray, $infoArrayDemo[0]);
                                            }
                                            $infoArray["Subject"] = array_search($infoArrayDemo[0], $subjectsArray) + 1;
                                            if (!in_array($infoArrayDemo[1], $teachersArray)) {
                                                array_push($teachersArray, $infoArrayDemo[1]);
                                            }
                                            $infoArray["Teacher"] = array_search($infoArrayDemo[1], $teachersArray) + 1;
                                            $infoArray["Type"] = 2;

                                            // Так как практика (ПР) всегда на целую группу (или несколько групп), то не важна кафедра
                                            $infoArray["Department"] = null;

                                            if ($isCabExist) {
                                                if (!in_array($infoArrayDemo[2], $cabinetsArray)) {
                                                    array_push($cabinetsArray, $infoArrayDemo[2]);
                                                }
                                                $infoArray["Cabinet"] = array_search($infoArrayDemo[2], $cabinetsArray) + 1;
                                            } else {
                                                array_push($infoArrayDemo, "n/a");
                                            }

                                            $infoArray["Subgroup"] = 3;

                                            var_dump($infoArrayDemo);
// -----------------------------  Если верхняя ячейка группы
                                        } elseif (preg_match('@/@u', $cellValue) || is_numeric($cellValue)) {
                                            if ($lastGroupCellValue != $cellValue) {
                                                $lastGroupCellValue = $nowGroupCellValue;
                                                $nowGroupCellValue = $cellValue;
                                                if (!is_numeric($nowGroupCellValue)) {
                                                    $groupInfoArray = explode("/", $nowGroupCellValue);
                                                    if (!in_array($groupInfoArray[0], $groupsArray)) {
                                                        array_push($groupsArray, $groupInfoArray[0]);
                                                    }
                                                    $globalGroup = array_search($groupInfoArray[0], $groupsArray) + 1;
                                                    $globalGroupName = $groupInfoArray[0];
                                                    $globalSubGroup = (int)$groupInfoArray[1];
                                                } else {
                                                    if (!in_array($nowGroupCellValue, $groupsArray)) {
                                                        array_push($groupsArray, $nowGroupCellValue);
                                                    }
                                                    $globalGroup = array_search($nowGroupCellValue, $groupsArray) + 1;
                                                    $globalGroupName = $nowGroupCellValue;
                                                    $globalSubGroup = 3;
                                                }
                                            } else {
                                                $skipThisColumnGroup = true;
                                            }
// -----------------------------  Иначе (если нет указания типа пары, например, у Физ-ры) заполняем по дефолту
                                        } else {
                                            echo $cellValue;
                                            if (!in_array($cellValue, $directionsArray) && $keyCell != 3 && $keyCell != 4 && $cellValue != "." && $cellValue != "" && $cellValue != "КСРС") {
                                                if (!in_array($cellValue, $subjectsArray))
                                                    array_push($subjectsArray, $cellValue);
                                                $infoArray["Subject"] = array_search($cellValue, $subjectsArray) + 1;
                                                $infoArray["Type"] = 2;
                                                $infoArray["Subgroup"] = 3;
                                                $infoArray["Parity"] = 3;
                                                if ($cellValue == "Элективные дисциплины по физической культуре и спорту") {
                                                    $infoArray["Cabinet"] = 2;
                                                    $infoArray["Teacher"] = 1;
                                                    $infoArray["Department"] = null;
                                                }
                                            } elseif ($keyCell == 3 || $keyCell == 4) {
                                                if (!in_array($cellValue, $groupsArray) && !in_array($cellValue, $directionsArray)) {
                                                    if (!in_array($cellValue, $departmentsArray)) {
                                                        array_push($departmentsArray, $cellValue);
                                                    }
                                                    $prevGlobalDepartment = $globalDepartment;
                                                    $globalDepartment = $cellValue;
                                                    if ($prevGlobalDepartment == $globalDepartment && $globalDepartment != "") {
                                                        $skipThisColumnDepartment = true;
                                                    }
                                                } else {
                                                    $prevGlobalDepartment = "";
                                                    $globalDepartment = "";
                                                }
                                            }
                                            var_dump($infoArray);
                                        }


// -------------------------------------------------------------------------------------------------------------------------
// -----------------------------  Если есть пара
                                        if ($infoArray["Subject"] != "") {
                                            // Если текущая рассматриваемая строка ПЕРВАЯ в рассматриваемой паре
                                            if ($keyCell == $prevNumberStartIndex) {
                                                // Если рассматриваемая на данный момент строка пары не объединена (состоит из одной строки)
                                                // Тогда четность пары - "Обе недели"
                                                if ($nextNumberStartIndex - $prevNumberStartIndex == 1) {
                                                    $lastInfoArray = array("Subject" => "");
                                                    $infoArray["Parity"] = 3;
                                                    if (!in_array($infoArray, $lessonsArray))
                                                        array_push($lessonsArray, $infoArray);
                                                } // Если состоит из 2-х и более строк - запоминаем текущую пару для следующего сравнения со следующей
                                                else {
                                                    $lastInfoArray = $infoArray;
                                                }
                                            } // Если текущая рассматриваемая строка НЕ ПЕРВАЯ в рассматриваемой паре
                                            elseif ($keyCell > $prevNumberStartIndex && $keyCell < $nextNumberStartIndex) {
                                                // Если предыдущая пара и текущая полностью идентичны, устанавливаем четность "Обе недели" и добавляем единственный экземпляр пары
                                                if ($lastInfoArray === $infoArray) {
                                                    $lastInfoArray = array("Subject" => "");
                                                    $infoArray["Parity"] = 3;
                                                    if (!in_array($infoArray, $lessonsArray)) {
                                                        array_push($lessonsArray, $infoArray);
                                                    }
                                                }
                                                // Если предыдушая пара и текущая не равны, то предыдущей ставится четность "Числитель" (если она не пустая),
                                                // текущей - "Знаменатель", добавляются обе пары (если предыдушая не пустая)
                                                else {
                                                    $infoArray["Parity"] = 2;
                                                    if ($lastInfoArray["Subject"] != "") {
                                                        $lastInfoArray["Parity"] = 1;
                                                        if (!in_array($lastInfoArray, $lessonsArray))
                                                            array_push($lessonsArray, $lastInfoArray);
                                                        if (!in_array($infoArray, $lessonsArray))
                                                            array_push($lessonsArray, $infoArray);
                                                    } else {
                                                        $lastInfoArray = array("Subject" => "");
                                                        if (!in_array($infoArray, $lessonsArray))
                                                            array_push($lessonsArray, $infoArray);
                                                    }
                                                }
                                            }
                                            // ---------------------------- Если нет пары
                                        } else {
                                            // Если текущая рассматриваемая строка ПЕРВАЯ в рассматриваемой паре
                                            if ($keyCell == $prevNumberStartIndex) {
                                                // Если рассматриваемая на данный момент строка пары не объединена (состоит из одной строки)
                                                // Тогда строка просто пропускается
                                                // Иначе запоминается пустая пара
                                                if ($nextNumberStartIndex - $prevNumberStartIndex != 1) {
                                                    $lastInfoArray = $infoArray;
                                                } else {
                                                    $lastInfoArray = array("Subject" => "");
                                                }
                                            } // Если текущая рассматриваемая строка НЕ ПЕРВАЯ в рассматриваемой паре
                                            elseif ($keyCell > $prevNumberStartIndex && $keyCell < $nextNumberStartIndex) {
                                                if ($lastInfoArray["Subject"] != "") {
                                                    $lastInfoArray["Parity"] = 1;
                                                    if (!in_array($lastInfoArray, $lessonsArray))
                                                        array_push($lessonsArray, $lastInfoArray);
//                                            array_push($lessonsArray, $lastInfoArray);
                                                } else {
                                                    $lastInfoArray = array("Subject" => "");
                                                }
                                            }
                                        }
// -------------------------------------------------------------------------------------------------------------------------

                                        if ($infoArray["Subject"] != "")
                                            var_dump($infoArray);

                                    }
                                }
                            }
                        }
                        $skipThisColumnGroup = false;
                        $skipThisColumnDepartment = false;
                    }
                }
            }
        }

        var_dump($lessonsArray);
        var_dump($groupsArray);
        var_dump($subjectsArray);
        var_dump($teachersArray);
        var_dump($directionsArray);
        var_dump($departmentsArray);

//        die();

        $db = DB::getDb();
// Очистка таблиц от текущих данных
        $result = $db->prepare('TRUNCATE TABLE groups');
        $result->execute();
        $result = null;

        $result = $db->prepare('TRUNCATE TABLE teachers');
        $result->execute();
        $result = null;

        $result = $db->prepare('TRUNCATE TABLE subjects');
        $result->execute();
        $result = null;

        $result = $db->prepare('TRUNCATE TABLE cabinets');
        $result->execute();
        $result = null;

        $result = $db->prepare('TRUNCATE TABLE lesson');
        $result->execute();
        $result = null;

        $result = $db->prepare('TRUNCATE TABLE departments');
        $result->execute();
        $result = null;

// Добавление спрашненной информации
        foreach ($groupsArray as $group) {
            $result = $db->prepare("INSERT INTO `groups` (`name`) VALUES (:group_name)");

            $result->bindParam(':group_name', $group);
            $result->execute();
            $result = null;
        }

        foreach ($teachersArray as $teacher) {
            $result = $db->prepare("INSERT INTO `teachers` (`name`) VALUES (:teacher_name)");

            $result->bindParam(':teacher_name', $teacher);
            $result->execute();
            $result = null;
        }

        foreach ($subjectsArray as $subject) {
            $result = $db->prepare("INSERT INTO `subjects` (`name`) VALUES (:subject_name)");

            $result->bindParam(':subject_name', $subject);
            $result->execute();
            $result = null;
        }

        foreach ($cabinetsArray as $cabinet) {
            $result = $db->prepare("INSERT INTO `cabinets` (`name`) VALUES (:cabinet_name)");

            $result->bindParam(':cabinet_name', $cabinet);
            $result->execute();
            $result = null;
        }

        foreach ($departmentsArray as $department) {
            $result = $db->prepare("INSERT INTO `departments` (`name`) VALUES (:department_name)");

            $result->bindParam(':department_name', $department);
            $result->execute();
            $result = null;
        }

        foreach ($lessonsArray as $lesson) {
            $result = $db->prepare("INSERT INTO `lesson` (`id_group`, `day`, `parity_week`, `lesson_type`, `number`,`id_subject`, `id_teacher`, `subgroup`, `id_cabinet`,`id_department`)
                       VALUES (:groupName,:dayName,:parityName,:lesson_typeName,:numberName,:subject_id,:teacher_id,:subgroup,:cabinet_id,:department_id)");

            $result->bindParam(':groupName', $lesson["Group"]);
            $result->bindParam(':dayName', $lesson["Day"]);
            $result->bindParam(':parityName', $lesson["Parity"]);
            $result->bindParam(':lesson_typeName', $lesson["Type"]);
            $result->bindParam(':numberName', $lesson["Number"]);
            $result->bindParam(':subject_id', $lesson["Subject"]);
            $result->bindParam(':teacher_id', $lesson["Teacher"]);
            $result->bindParam(':subgroup', $lesson["Subgroup"]);
            $result->bindParam(':cabinet_id', $lesson["Cabinet"]);
            $result->bindParam(':department_id', $lesson["Department"]);
            $result->execute();
            $result = null;
        }
    } else {
        echo "Wrong file's format!";
    }
}

