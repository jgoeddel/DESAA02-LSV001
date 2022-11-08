<?php


use App\Pages\ChangeManagement\ChangeManagementDatabase;

/** (c) Joachim Göddel . RLMS */
/*
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\PhpSpreadsheet\Spreadsheet;
use App\PhpSpreadsheet\Reader\Xlsx;

$reader = new \App\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($files['file']['tmp_name']);
$rec = $spreadsheet->getActiveSheet()->toArray();
if($rec[0][0] == 'ID'){
    foreach($rec as $row){
        if($row[0] == 'ID' && $row[7] == 'Ziel'){ } else {
            if($row[1] == '') {
                echo 1;
                break;
            } else {
                # Prüfen, ob die Teilenummer bereits eingetragen ist
                if(empty(ChangeManagementDatabase::checkPartNo($id,$row[3],$row[4],$row[2]))) {
                    $lid = ChangeManagementDatabase::checkLieferant($row[6]);
                    if(empty($lid)) $lid = 0; # Wenn der Lieferant noch nicht in der Liste ist
                    ChangeManagementDatabase::insertPartNo($row,$id,$lid,''.$ziel.'');
                }
            }
        }
    }
}
*/
function csvToArray($csvFile): array
{

    $file_to_read = fopen($csvFile, 'r');

    while (!feof($file_to_read)) {
        $lines[] = fgetcsv($file_to_read, 1000, ';');

    }

    fclose($file_to_read);
    return $lines;
}

//read the csv file into an array
$doc = getcwd();
$inputFileName = $files['file']['tmp_name'];
$csvFile = $inputFileName;
$csv = csvToArray($csvFile);

if ($csv[0][0] == 'ID') {
    foreach ($csv as $row) {
        if ($row[0] == 'ID' && $row[7] == 'Ziel') {
        } else {
            if ($row[2] == '') {
                echo 1;
                break;
            } else {
                var_dump($row);
                # Prüfen, ob die Teilenummer bereits eingetragen ist
                if (empty(ChangeManagementDatabase::checkPartNo($id, $row[3], $row[4], $row[2]))) {
                    $lid = ChangeManagementDatabase::checkLieferant($row[6]);
                    if (empty($lid)) $lid = 0; # Wenn der Lieferant noch nicht in der Liste ist
                    ChangeManagementDatabase::insertPartNo($row, $id, $lid, '' . $ziel . '');
                }
            }
        }
    }
}