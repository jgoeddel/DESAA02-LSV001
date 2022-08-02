<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$dir = $_SERVER['DOCUMENT_ROOT'] . PATH_PROJECT;


$uploaddir = $dir."/lib/Pages/ChangeManagement/MVC/View/files/";
$filename = Functions::translateFilename(basename($_FILES['file']['name'])); // Sonderzeichen und Leerzeichen ersetzen
$ext = pathinfo($filename, PATHINFO_EXTENSION); // Dateierweiterung

$filename = $id.".".$filename; // ID zu Dateinamen hinzu
$filename_ext = str_replace(".".$ext."", "", $filename); // Endung entfernen
$uploadfile = $uploaddir . $filename; // Ziel

// Prüfen ob es eine "Datei" oder ein Bild ist
$type = explode("/", $_FILES['file']['type']);

//echo $uploadfile;

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)): // Ist die Datei geschrieben worden
    if($type[0] == 'image'):

        // Bild bearbeiten
        $image = new Imagick();
        $image->readImage($uploadfile);
        $watermark = new Imagick();
        $watermark->readImage($dir."/skin/files/images/watermark.png");
        $watermarkResizeFactor = 2;
        $img_Width = $image->getImageWidth();
        $img_Height = $image->getImageHeight();
        $watermark_Width = $watermark->getImageWidth();
        $watermark_Height = $watermark->getImageHeight();
        $watermark->scaleImage($watermark_Width / $watermarkResizeFactor, $watermark_Height / $watermarkResizeFactor);
        $watermark_Width = $watermark->getImageWidth();
        $watermark_Height = $watermark->getImageHeight();
        $x = ($img_Width - $watermark_Width - 20);
        $y = ($img_Height - $watermark_Height - 10);
        $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);
        $image->writeImage($uploaddir."".$filename_ext.".".$image->getImageFormat());
        $newFilename = $filename_ext.".".$image->getImageFormat();
        unlink($uploadfile);
        // Kleinere Version
        $small = new Imagick();
        $small->readImage($uploaddir."".$newFilename);
        $small->resizeImage(600, 480, Imagick::FILTER_LANCZOS,1, true);
        $small->writeImage($uploaddir."small_".$filename_ext.".".$small->getImageFormat());

        // Prüfen ob der Eintrag existiert
        $a = ChangeManagementDatabase::run("SELECT id FROM base2files WHERE bid = '$id' AND datei = '$newFilename'")->fetchColumn();
        if(empty($a)):
            // Daten in die Datenbank schreiben
            ChangeManagementDatabase::run("INSERT INTO base2files SET bid = '$id', datei = '$newFilename', user = '{$_SESSION['user']['dbname']}', datum = now(), typ = '$ext', size = '{$_FILES['file']['size']}', part = '1'");
            ChangeManagementDatabase::setLog('Detailansicht',$id,'Dateien','0',''.$filename.' wurde auf den Server geladen',1);
        endif;

    else:
        // Prüfen ob der Eintrag existiert
        $a = ChangeManagementDatabase::run("SELECT id FROM base2files WHERE bid = '$id' AND datei = '$filename'")->fetchColumn();
        if(empty($a)):
            // Daten in die Datenbank schreiben
            ChangeManagementDatabase::run("INSERT INTO base2files SET bid = '$id', datei = '$filename', user = '{$_SESSION['user']['dbname']}', datum = now(), typ = '$ext', size = '{$_FILES['file']['size']}', part = '2'");
            ChangeManagementDatabase::setLog('Detailansicht',$id,'Dateien','0',''.$filename.' wurde auf den Server geladen',1);
        endif;

    endif;
    echo 1;
else:
    echo 0;
endif;