<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/PHPExcel.php";

if (count($_FILES) != 0) {
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
} else {
    echo "No files";
}