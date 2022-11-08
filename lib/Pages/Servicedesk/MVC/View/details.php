<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Servicedesk\ServicedeskDatabase;

$_SESSION['seite']['id'] = 7;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';
$dspKalender = true;
$dspTag = false;

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;

# Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';

# Abteilung
$abteilung = AdministrationDatabase::getOneAbt('b_abteilung_rlms', $eintrag->aid);
# Status
$status = AdministrationDatabase::getStatusBadge($eintrag->status);
# Bereich
$bereich = AdministrationDatabase::getOneBereich('b_bereich_rlms', $eintrag->bid);
# Wenn die Bearbeitung gestartet wurde
if ($eintrag->status > 1):
    $jsstartdate = ServicedeskDatabase::getStartDate($eintrag->id);
endif;
# Bearbeiter
$bid = ServicedeskDatabase::getBearbeiter($eintrag->id);
if (!empty($bid)):
    $usr = AdministrationDatabase::getUserInfo($bid);
    $user = $usr->vorname . ' ' . $usr->name;
    $log = ServicedeskDatabase::getRowBearbeiten($eintrag->id);
    # Ausgabe
    if ($log->user == $user):
        $loginfo = sprintf($_SESSION['text']['i_userService1'], $log->user);
    else:
        $loginfo = sprintf($_SESSION['text']['i_userService2'], $log->user, $user);
    endif;
endif;

?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
    <link rel="stylesheet" type="text/css"
          href="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.css">
    <link rel="stylesheet" href="<?= Functions::getBaseURL() ?>/skin/plugins/other/dropzone/dropzone.min.css">
</head>
<body class="d-flex flex-column h-100 servicedesk" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_serviceDesk']}");
?>
<main class="w-100 bg__white--99 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid py-3">
        <div class="px-4">
            <div class="row">
                <div class="col-3 border__right--dotted-gray">
                    <div class="pe-3">
                        <h3 class="oswald font-weight-100 pb-2 mb-2 border__bottom--dotted-gray">ID: <?= $eintrag->id ?>
                            &bull; <?= $eintrag->titel ?></h3>
                        <div class="border__bottom--dotted-gray mb-2 pb-2">
                            <p class="m-0 p-0">
                                <small class="font-size-11 text-muted"><?= $_SESSION['text']['t_eingetragenAm'] ?></small><br>
                                <?= Functions::germanDate($eintrag->eintrag) ?>
                            </p>
                        </div><!-- border -->
                        <div class="border__bottom--dotted-gray mb-2 pb-2 row">
                            <div class="col-6 border__right--dotted-gray">
                                <div class="pe-3">
                                    <p class="m-0 p-0">
                                        <small class="font-size-11 text-muted"><?= $_SESSION['text']['t_eingetragenVon'] ?></small><br>
                                        <?= $eintrag->user ?>
                                    </p>
                                </div><!-- pe-3 -->
                            </div><!-- col -->
                            <div class="col-6">
                                <div class="ps-3">
                                    <p class="m-0 p-0">
                                        <small class="font-size-11 text-muted"><?= $_SESSION['text']['h_abtBuero'] ?></small><br>
                                        <?= $_SESSION['text']['' . $abteilung . ''] ?>
                                    </p>
                                </div><!-- pe-3 -->
                            </div><!-- col -->
                        </div><!-- row -->
                        <div class="border__bottom--dotted-gray mb-2 pb-2 row">
                            <div class="col-6 border__right--dotted-gray">
                                <div class="pe-3">
                                    <p class="m-0 p-0">
                                        <small class="font-size-11 text-muted"><?= $_SESSION['text']['h_bereich'] ?></small><br>
                                        <?= $_SESSION['text']['' . $bereich . ''] ?>
                                    </p>
                                </div><!-- pe-3 -->
                            </div><!-- col -->
                            <div class="col-6">
                                <div class="ps-3">
                                    <p class="m-0 p-0">
                                        <small class="font-size-11 text-muted"><?= $_SESSION['text']['h_status'] ?></small><br>
                                        <?= $status ?>
                                    </p>
                                </div><!-- pe-3 -->
                            </div><!-- col -->
                        </div><!-- row -->
                        <?php
                        if ($eintrag->status < 5 && $eintrag->status > 2):
                            ?>
                            <h3 class="oswald font-weight-100 mt-4 pb-2 mb-2 border__bottom--dotted-gray"><?= $_SESSION['text']['h_bearbeitungsdauer'] ?></h3>
                            <div class="row p-0 m-0 text-muted font-size-30 mt-2 pb-2 pt-2">
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_jahre'] ?></div>
                                    <div id="jahr"></div>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_monate'] ?></div>
                                    <div id="monat"></div>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_tage'] ?></div>
                                    <div id="tag"></div>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_stunden'] ?></div>
                                    <div id="stunde"></div>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_minuten'] ?></div>
                                    <div id="minute"></div>
                                </div><!-- col -->
                                <div class="col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_sekunden'] ?></div>
                                    <div id="sekunde"></div>
                                </div><!-- col -->
                            </div><!-- row -->
                        <?php
                        endif;
                        if ($eintrag->status == 5 || $eintrag->status == 9):
                            # Bearbeitungszeit ermitteln
                            $start = new DateTime(servicedeskDatabase::getDateQuery($eintrag->id, 'Bearbeitung gestartet', 'ASC'));
                            $ende = new DateTime(servicedeskDatabase::getDateQuery($eintrag->id, 'Bearbeitung beendet', 'DESC'));
                            $intvl = $start->diff($ende);
                            # Parameter
                            $tage = $intvl->days;
                            $stunden = $intvl->h;
                            $minuten = $intvl->i;
                            $sekunden = $intvl->s;
                            ?>
                            <h3 class="oswald font-weight-100 mt-4 pb-2 mb-2 border__bottom--dotted-gray"><?= $_SESSION['text']['h_bearbeitungsdauer'] ?></h3>
                            <div class="row p-0 m-0 text-muted font-size-30 mt-2 pb-2 pt-2">
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_tage'] ?></div>
                                    <?= $tage ?>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_stunden'] ?></div>
                                    <?= $stunden ?>
                                </div><!-- col -->
                                <div class="border__right--dotted-gray col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_minuten'] ?></div>
                                    <?= $minuten ?>
                                </div><!-- col -->
                                <div class="col text-center oswald">
                                    <div class="font-size-11 italic"><?= $_SESSION['text']['h_sekunden'] ?></div>
                                    <?= $sekunden ?>
                                </div><!-- col -->
                            </div><!-- row -->
                        <?php
                        endif;
                        ?>
                    </div><!-- pe-3 -->
                </div><!-- col -->
                <div class="col-3 border__right--dotted-gray">
                    <div class="px-3">
                        <h3 class="oswald font-weight-100 pb-2 mb-3 border__bottom--dotted-gray">
                            <?php if($eintrag->status > 2): echo $_SESSION['text']['t_verlauf']; else: echo $_SESSION['text']['t_werKuemmertSich']; endif; ?>
                            </h3>
                        <?php
                        # Ausgabe der Aktionen
                        # Prüfen, ob der Auftrag als n.i.O. gekennzeichnet wurde. Wenn das der Fall ist, muss eine entsprechende Meldung ausgegeben werden.
                        if (!empty($nio)):
                            $x = rand(1, 12);
                            ?>
                            <div class="border__dotted--gray p-5 mb-3 errorpageSub<?= $x ?>">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="oswald font-weight-600 p-0 m-0 text-danger"><?= $_SESSION['text']['t_bearbeitungNIO'] ?>
                                            !</h4>
                                        <p class="m-0 p-0 py-2 text-muted italic font-size-11"><?= $nio->user ?>
                                            &bull; <?= Functions::germanDate($nio->datum) ?> Uhr</p>
                                        <p class="p-0 m-0"><?= $nio->anmerkung ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endif; # n.i.O.
                        # Auswahl der Bearbeiter anzeigen, wenn der Auftrag n.i.O. oder noch nicht zugewiesen ist
                        if ($eintrag->status < 9 && empty($bid)):
                            $adm = AdministrationDatabase::getAllAdmin(7, 1);
                            # Auftrag selbst übernehmen
                            echo "<div class=\"border__bottom--dotted-gray row mb-3\">";
                            echo "<div class=\"col-8\">";
                            echo "<div class=\"pe-3\">";
                            Functions::alert($_SESSION['text']['i_serviceauftrag']);
                            echo "</div>";
                            echo "</div><!-- col -->";
                            echo "<div class=\"col-4 pointer\" onclick=\"meinAuftrag('$cid','{$_SESSION['text']['t_meinAuftrag']}');\">";
                            echo "<p class=\"oswald font-weight-300 font-size-20 p-0 m-0 pt-1 ps-3 text-primary\">";
                            echo $_SESSION['user']['vorname'] . " " . $_SESSION['user']['name'];
                            echo "</p>";
                            echo "<p class=\"p-0 m-0 ps-3 text-warning font-weight-100 font-size-14 oswald\">";
                            echo $_SESSION['text']['abt_' . $_SESSION['user']['abteilung'] . ''];
                            echo "</p>";
                            echo "</div><!-- col -->";
                            echo "</div><!-- row -->";
                            # Auftrag delegieren
                            echo "<div class=\"row mb-3 pb-3\">";
                            Functions::alert($_SESSION['text']['i_serviceauftrag2']);
                            $x = 1;
                            foreach ($adm as $usr):
                                # Userdetails abrufen
                                $name = AdministrationDatabase::getUserInfo($usr->mid);
                                $border = ($x % 3) ? 'border__right--dotted-gray' : '';
                                $vn = substr($name->vorname, 0, 1);
                                # Anzahl Aufträge
                                $anz = ServicedeskDatabase::countAnzahl($usr->mid);
                                echo "<div class=\"col-4 $border pointer\" onclick=\"deinAuftrag('$cid','$usr->mid','{$_SESSION['text']['t_meinAuftrag']}')\">";
                                echo "<div class=\"px-2\">";
                                echo "<div class=\"row border__bottom--dotted-gray\">";
                                echo "<div class=\"col-9\">";
                                echo "<div class=\"px-2\">";
                                echo "<p class=\"oswald font-weight-300 font-size-20 p-0 m-0 pt-1 text-primary\">$vn $name->name</p>";
                                echo "<p class=\"font-size-11 text-warning m-0 p-0\">" . $_SESSION['text']['abt_' . $name->abteilung . ''] . "</p>";
                                echo "</div><!-- px-2 -->";
                                echo "</div><!-- col -->";
                                echo "<div class=\"col-3\">";
                                echo "<div class=\"px-2 py-2\">";
                                echo "<p class=\"smallinit init__primary oswald\">$anz</p>";
                                echo "</div><!-- px-2 -->";
                                echo "</div><!-- col -->";
                                echo "</div><!-- row -->";
                                echo "</div><!-- px-3 -->";
                                echo "</div><!-- col -->";
                                $x++;
                            endforeach;
                            echo "</div><!-- row -->";
                        else: # Bearbeiter eingetragen
                            # Bearbeitung des Auftrages ist noch nicht abgeschlossen
                            if ($eintrag->status > 1 && $eintrag->status < 9):
                                echo "<div class=\"row border__bottom--dotted-gray mb-3 pb-3\">";
                                echo "<div class=\"col-2 border__right--dotted-gray\">";
                                echo "<div class=\"p-3 text-center\">";
                                echo "<i class=\"fa fa-user-circle text-muted fa-2x\"></i>";
                                echo "</div><!-- p-3 -->";
                                echo "</div><!-- col -->";
                                echo "<div class=\"col-10\">";
                                echo "<div class=\"ps-3\">";
                                echo "<p class=\"p-0 m-0\">$loginfo</p>";
                                echo "<p class=\"p-0 m-0 font-size-11 italic text-gray\">";
                                echo Functions::germanDate($log->eintrag);
                                echo "</p>";
                                echo "</div><!-- ps-3 -->";
                                echo "</div><!-- col -->";
                                echo "</div><!-- row -->";
                                # Wenn der Status unter 5 ist Kann die Bearbeitung noch übernommen werden
                                if ($eintrag->status < 5 && $bid != $_SESSION['user']['id']):
                                    echo "<div class=\"border__bottom--dotted-gray row mb-3\">";
                                    echo "<div class=\"col-8\">";
                                    echo "<div class=\"pe-3\">";
                                    Functions::alert($_SESSION['text']['i_serviceauftrag']);
                                    echo "</div>";
                                    echo "</div><!-- col -->";
                                    echo "<div class=\"col-4 pointer\" onclick=\"meinAuftrag('$cid','{$_SESSION['text']['t_meinAuftrag']}');\">";
                                    echo "<p class=\"oswald font-weight-300 font-size-20 p-0 m-0 pt-1 ps-3 text-primary\">";
                                    echo $_SESSION['user']['vorname'] . " " . $_SESSION['user']['name'];
                                    echo "</p>";
                                    echo "<p class=\"p-0 m-0 ps-3 text-warning font-weight-100 font-size-14 oswald\">";
                                    echo $_SESSION['text']['abt_' . $_SESSION['user']['abteilung'] . ''];
                                    echo "</p>";
                                    echo "</div><!-- col -->";
                                    echo "</div><!-- row -->";
                                endif; # Status unter 5
                                # Status 5: kein Wechsel des Bearbeiters mehr möglich
                                if ($eintrag->status >= 5):
                                    Functions::alert($_SESSION['text']['t_keinTausch']);
                                endif;
                                # Zeiten abfragen
                                $gestartet = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungGestartet']}");
                                $beendet = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungBeendet']}");
                                $pause = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungPausiert']}");
                                $wieder = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungFortgesetzt']}");
                                $abgeschlossen = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungBeendet']}");
                                # Bearbeiter angemeldet ?
                                $edit = ($bid == $_SESSION['user']['id']) ? 1 : 0;
                                /*
                                echo "<pre>";
                                var_dump($gestartet);
                                var_dump($beendet);
                                var_dump($pause);
                                var_dump($wieder);
                                var_dump($abgeschlossen);
                                echo "</pre>";
                                */
                                # Ausgabe der jeweiligen Buttons und / oder Anzeigen

                                # Auftrag starten
                                if ($gestartet == '-' && $edit == 1):
                                    Functions::dspStatusButton($_SESSION['text']['t_bearbeitungStarten'], "play", "auftragStarten('$cid')");
                                endif; # Auftrag starten

                                # Anzeige Auftrag gestartet
                                if ($gestartet != '-'):
                                    Functions::dspStatusFeld('' . Functions::germanDate($gestartet->tag) . '', $_SESSION['text']['t_bearbeitungGestartet'], 'play', '' . $gestartet->user . '');
                                endif; # Anzeige Auftrag gestartet

                                # Anzeige Auftrag anhalten
                                if ($pause != '-' && $beendet == '-'):
                                    if ($gestartet != '-' && $pause->status == 1 && $edit == 1):
                                        # Anzeige
                                        Functions::dspStatusFeld('' . Functions::germanDate($pause->tag) . '', $_SESSION['text']['t_bearbeitungPausiert'], 'pause', '' . $pause->user . '');
                                        # Button fortsetzen
                                        Functions::dspStatusButton($_SESSION['text']['t_bearbeitungAufnehmen'], "play", "auftragWeiter('$cid','{$_SESSION['text']['t_bearbeitungFortgesetzt']}')");
                                    endif; # Anzeige Auftrag anhalten
                                else:
                                    if ($pause != '-' && $pause->status == 0 && $beendet == '-'):
                                        Functions::dspStatusButton($_SESSION['text']['t_bearbeitungAnhalten'], "pause", "auftragPause('$cid','{$_SESSION['text']['t_bearbeitungPausiert']}')");
                                    endif;
                                endif;

                                # Auftrag anhalten
                                if ($gestartet != '-' && $edit == 1 && $beendet == '-'):
                                    if ($pause == '-' || $pause->status == 0):
                                        Functions::dspStatusButton($_SESSION['text']['t_bearbeitungAnhalten'], "pause", "auftragPause('$cid','{$_SESSION['text']['t_bearbeitungPausiert']}')");
                                    endif;
                                endif; # Auftrag anhalten

                                # Bearbeitung beeden
                                if ($gestartet != '-' && $edit == 1 && $beendet == '-'):
                                    Functions::dspStatusButton($_SESSION['text']['t_bearbeitungBeenden'], "stop", "auftragEnde('$cid','{$_SESSION['text']['t_bearbeitungBeendet']}')");
                                endif; # Auftrag anhalten

                                # Bearbeitung beendet anzeigen
                                if ($beendet != '-'):
                                    Functions::dspStatusFeld('' . Functions::germanDate($beendet->tag) . '', $_SESSION['text']['t_bearbeitungBeendet'], 'stop', '' . $beendet->user . '');
                                endif;
                                # Abschliessen
                                if ($gestartet != '-' && $beendet != '-'):
                                    Functions::alert($_SESSION['text']['t_beendet']);
                                    Functions::dspStatusButton($_SESSION['text']['t_bearbeitungAbschliessen'], "check", "auftragAbschluss('$cid','{$_SESSION['text']['t_serviceauftragAbgeschlossen']}')");
                                    Functions::dspStatusButton($_SESSION['text']['t_nichtErledigt'], "exclamation", "auftragNIO('$cid','{$_SESSION['text']['t_bearbeitungNIO']}')", "danger");
                                endif;
                            else: # Bearbeitung noch nicht abgeschlossen

                                # Zeiten abfragen
                                $gestartet = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungGestartet']}",0);
                                $beendet = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_bearbeitungBeendet']}",0);
                                $abgeschlossen = ServicedeskDatabase::getInfo($eintrag->id, "{$_SESSION['text']['t_serviceauftragAbgeschlossen']}");
                                Functions::dspStatusFeld('' . Functions::germanDate($gestartet->tag) . '', $_SESSION['text']['t_bearbeitungGestartet'], 'play', '' . $gestartet->user . '');
                                Functions::dspStatusFeld('' . Functions::germanDate($beendet->tag) . '', $_SESSION['text']['t_bearbeitungBeendet'], 'stop', '' . $beendet->user . '');
                                Functions::dspStatusFeld('' . Functions::germanDate($abgeschlossen->tag) . '', $_SESSION['text']['t_serviceauftragAbgeschlossen'], 'check', '' . $beendet->user . '');
                            endif; # Bearbeitung abgeschlossen
                        endif; # kein Bearbeiter
                        ?>
                    </div><!-- px-3 -->
                </div><!-- col -->
                <div class="col-3 border__right--dotted-gray">
                    <div class="px-3">
                        <h3 class="oswald font-weight-100 pb-2 mb-3 border__bottom--dotted-gray">
                            <?= $_SESSION['text']['t_kommentare'] ?>
                            <?php
                            if ($eintrag->status < 9):
                                ?>
                                <span class="float-end">
                                    <i class="fa fa-plus-circle pointer text-primary"
                                       onclick="$('#iKom,#nkom').toggle(800);"></i>
                                </span>
                            <?php
                            endif;
                            ?>
                        </h3>

                        <?php
                        # Kommentare abrufen
                        if ($ak > 0):
                            foreach ($kom as $km):
                                Functions::dspStatusFeld('' . Functions::germanDate($km->datum) . '', $km->anmerkung, 'user', '' . $km->user . '');
                            endforeach;
                        else:
                            echo '<div id="nkom">';
                            Functions::alert($_SESSION['text']['i_keineKommentare']);
                            echo '</div>';
                        endif;
                        ?>

                        <!-- Formular -->
                        <div class="dspnone" id="iKom">
                            <?php Functions::alert($_SESSION['text']['t_kommentarEintrag']); ?>
                            <form action="#" id="sendComment" method="post" novalidate>
                                <textarea class="mb-3" name="kommentar" id="summernote" required></textarea>
                                <input type="hidden" name="id" value="<?= $eintrag->id ?>">
                                <input type="hidden" name="meldung" id="meldung"
                                       value="<?= $_SESSION['text']['i_kommentarGespeichert'] ?>">
                                <p class="text-end">
                                    <input type="submit" class="btn btn-primary mt-2 oswald"
                                           value="<?= $_SESSION['text']['b_kommentarSpeichern'] ?>">
                                </p>
                            </form>
                        </div>
                        <!-- Ende Formular -->
                    </div><!-- px-3 -->
                </div><!-- col -->
                <div class="col-3">
                    <div class="ps-3">
                        <h3 class="oswald font-weight-100 pb-2 mb-3 border__bottom--dotted-gray">
                            <?= $_SESSION['text']['h_dateienBilder'] ?>
                            <?php
                            if ($eintrag->status < 9):
                                ?>
                                <span class="float-end">
                                    <i class="fa fa-plus-circle pointer text-primary"
                                       onclick="$('#pic,#nfiles').toggle(800);"></i>
                                </span>
                            <?php
                            endif;
                            ?>
                        </h3>
                        <?php
                        if ($fl > 0):
                            foreach ($fls as $file):
                                # .xls, .xlsx, .doc, .docx, .pdf, .txt, .jpg, .jpeg, .png, .bmp, .gif, .csv, .msg, .ppt, .pptx
                                $typ = strtolower($file->typ);
                                switch($typ):
                                    case 'xls':
                                    case 'xlsx':
                                        $icn = 'fa-file-excel';
                                        break;
                                    case 'doc':
                                    case 'docx':
                                        $icn = 'fa-file-word';
                                        break;
                                    case 'ppt':
                                    case 'pptx':
                                        $icn = 'fa-file-powerpoint';
                                        break;
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'bmp':
                                    case 'png':
                                    case 'gif':
                                        $icn = 'fa-image';
                                        break;
                                    case 'pdf':
                                        $icn = 'fa-file-pdf';
                                        break;
                                    case 'csv':
                                        $icn = 'fa-file-csv';
                                        break;
                                    case 'msg':
                                        $icn = 'fa-envelope';
                                        break;
                                endswitch;
                                Functions::dspDateiFeld('' . Functions::germanDate($file->datum) . '', ''.$file->datei.'', ''.$icn.'', '' . $file->user . '', '' . $file->id . '');
                            endforeach;
                        else:
                            echo '<div id="nfiles">';
                            Functions::alert($_SESSION['text']['i_keineDateien']);
                            echo '</div>';
                        endif;
                        ?>

                        <!-- Formular -->
                        <div class="dspnone pb-3 mb-3 border__bottom--dotted-gray_25" id="pic">
                            <?php
                            Functions::alert($_SESSION['text']['i_fileUpload']);
                            ?>
                            <form action="#" class="dropzone text-center font-size-12 bg-light-lines p-4"
                                  id="fileUpload">
                                <input type="hidden" name="id" value="<?= $eintrag->id ?>">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                            </form>
                        </div>
                        <!-- Ende Formular -->
                    </div><!-- ps-3 -->
                </div><!-- col -->
            </div>
        </div><!-- px-5 -->
    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/countdown.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/skin/plugins/other/dropzone/dropzone.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Servicedesk/MVC/View/js/submit.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Servicedesk/MVC/View/js/action.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
    <?php if($eintrag->status < 5 && $eintrag->status > 2): ?>
    setInterval(function () {
        var timespan = countdown(new Date("<?= $jsstartdate ?>"), new Date());
        //var timespan = countdown(new Date("2021-10-12T05:30:00"), new Date());
        var sek = document.getElementById('sekunde');
        var min = document.getElementById('minute');
        var std = document.getElementById('stunde');
        var tag = document.getElementById('tag');
        var mnt = document.getElementById('monat');
        var jhr = document.getElementById('jahr');
        sek.innerHTML = timespan.seconds;
        min.innerHTML = timespan.minutes;
        std.innerHTML = timespan.hours;
        tag.innerHTML = timespan.days;
        mnt.innerHTML = timespan.months;
        jhr.innerHTML = timespan.years;
        // Elemente ausblenden
        if (timespan.days < 1) {
            tag.innerHTML = '-';
        }
        if (timespan.months < 1) {
            mnt.innerHTML = '-';
        }
        if (timespan.years < 1) {
            jhr.innerHTML = '-';
        }
        //div.innerHTML = "S " + timespan.years + " years, " + timespan.months + " months, " + timespan.days + " days, " + timespan.hours + " hours, " + timespan.minutes + " minutes, " + timespan.seconds + " seconds."
    }, 1000);
    <?php endif; ?>
    // summernote
    $('#summernote').summernote({
        height: 160,
        lang: 'de-DE',
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['color', ['color']],
            ['para', ['ul', 'ol']]
        ],
        cleaner: {
            action: 'paste',
            keepHtml: false,
            keepClasses: false
        }
    });

    Dropzone.autoDiscover = false;
    <?php if($eintrag->status < 9): ?>
    // DATEIUPLOAD
    $('#fileUpload').dropzone({
        url: "/servicedesk/upload",
        maxFilesize: 30000,
        paramName: "file",
        dictDefaultMessage: "<?= $_SESSION['text']['i_upload'] ?>",
        createImageThumbnails: false,
        acceptedFiles: ".xls, .xlsx, .doc, .docx, .pdf, .txt, .jpg, .jpeg, .png, .bmp, .gif, .csv, .msg, .ppt, .pptx",
        previewTemplate: '<div class="dz-preview dz-file-preview">\n' +
            '  <div class="dz-details">\n' +
            '  </div>\n' +
            '</div>',
        init: function () {
            this.on('success', function (file, json) {
                if (json == 1) {
                    swal.fire({
                        title: '<?= $_SESSION['text']['h_dateiGespeichert'] ?>',
                        text: '<?= $_SESSION['text']['t_dateiGespeichert'] ?>',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        backdrop: 'rgba(0,0,0,0.7)'
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    swal.fire({
                        title: '<?= $_SESSION['text']['h_dateiNichtGespeichert'] ?>',
                        text: '<?= $_SESSION['text']['t_dateiNichtGespeichert'] ?>',
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false,
                        backdrop: 'rgba(0,0,0,0.7)'
                    })
                }
            });
        }
    });
    <?php endif; ?>
</script>
</body>
</html>