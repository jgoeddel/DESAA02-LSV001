<?php
# Seitenparameter
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(10);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;

# Datum schreiben
$newdate = date_create($_POST['newdate']);
$stationen = count($_POST['station']);
$anwesend = count($_POST['anwesend']);
$plandate = date_format($newdate, "Y-m-d");
$_SESSION['wrk']['datum'] = $plandate;

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();
# Ergonomie
unset($_SESSION['ergonomie']);

if ($pln > 0): else:
# Springer
    $springer = $db->getSpringerID();
# Zeitzone
    $zzone = Functions::getZeitschieneSchicht($plandate);
# Zeitschiene
    $zschiene = ($zzone === 1) ? array(1, 2, 3) : array(4, 5, 6);
# Endkontrolle
    $idEndkontrolle = $db->getStationIdByName("Endkontrolle");

    #echo "<h6>Anwesende Mitarbeiter:</h6>";
# Anwesenheit speichern
    #echo "<pre>"; print_r($post); echo "</pre>";
    foreach ($post['anwesend'] as $key => $value):
        $a = $db->setAnwesenheit($key, $post['station'][$key]);
    endforeach;
    #echo "Springer: $springer<br>";
# Alle Stationen der angemeldeten Abteilung unbrufen
    $stn = $db->getStationAbteilung();
# Tabelle tmp_station füllen
    for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
        # Alle Mitarbeiter, die in der Schicht anwesend sind
        $anwesend = $db->getAnwesend();
        if ($anwesend):
            foreach ($anwesend as $a):
                if ($stn):
                    foreach ($stn as $station):
                        # Qualifikation und Abwesenheit - Mitarbeiter die mögliche Station zuordnen, wenn die Quali vorhanden ist,
                        # der Mitarbeiter nicht abwesend ist oder einer festen Station zugeordnet ist
                        $qfk = ($db->getQuali($a->uid, $station->id) === true) ? 'Ja' : 'Nein';
                        $bws = ($db->getAbwesend($a->uid) === true) ? 'Ja' : 'Nein';
                        if ($qfk == 'Ja' && $bws == 'Nein' && $a->sid === 0):
                            # Anzahl der Einsätze des Mitarbeiters an dieser Station
                            $einsaetze = $db->getAnzahlEinsatz($a->uid, $station->id);
                            # Prüfen, ob der Eintrag bereits vorhanden ist
                            if (empty($db->getTmpStation($station->id, $a->uid, $zschiene[$i]))):
                                # Eintrag speichern
                                $db->setTmpUser2Station($station->id, $a->uid, $einsaetze, $zschiene[$i]);
                            endif; # Eintrag vorhanden
                        endif; # Qualifikation prüfen
                    endforeach; # station
                endif; # stationen
            endforeach; # anwesend
        endif; #anwesend
    endfor; # Tabelle tmp_station

# Anzahl der Mitarbeiter zu den Stationen in tmp_anzahl_user schreiben
    if ($stn):
        foreach ($stn as $station):
            # Anzahl Mitarbeiter an Station
            $anzahl = $db->getAnzahlMaStation($station->id, $zschiene[0]);
            # Wenn noch kein temporärer Eintrag vorhanden ist soll dieser erstellt werden
            if (!$db->getTmpAnzStation($station->id)):
                # speichern
                $db->setTmpAnzStation($station->id, $anzahl);
            endif;
        endforeach; # $stn
    endif; #$stn

# Temporären Rotationsplan erstellen
    if ($stn):
        foreach ($stn as $station):
            # Wieviele Mitarbeiter können die Station
            $anzahl = $db->getAnzTmpAnzStation($station->id);
            # Wieviele Mitarbeiter werden an der Station benötigt
            $mitarbeiter = $db->getAnzMaStation($station->id);
            # Zeitschienen durchlaufen
            for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
                # Anzahl der Mitarbeiter an Station
                for ($m = 1; $m <= $mitarbeiter; $m++):
                    $db->setTmpRotationsplan($station->id, $zschiene[$i], $m, $anzahl, $springer);
                endfor;
            endfor; # Zeitschiene
        endforeach; # $stn
    endif; #$stn
    #echo "<br><hr><h6>Temporärer Rotationsplan wurde erstellt.</h6><hr><br>";


# Mitarbeiter an festen Stationen
    $fs = $db->getMaFesteStation();
    if ($fs):
        foreach ($fs as $user):
            if($user->sid != '999') {
                # Einsätze des Mitarbeiters an dieser Station
                $anzEinsatzStation = $db->getMaAnzEinsatzStation($user->uid, $user->sid);
                # Anzahl der eingesetzten Mitarbeiter an dieser Station
                $anzMaStation = $db->getAnzMaStation($user->sid);
                # Position ermitteln, wenn Anzahl > 1
                $mitarbeiter = ($anzMaStation > 1) ? $db->getMaxMaStation($user->sid, $springer) : 1;
                # In alle Zeischienen schreiben
                for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
                    # Eintrag speichern
                    $db->setUpdateTmpRotationsplan($user->uid, $user->sid, $zschiene[$i], $mitarbeiter);
                    # Eintrag aus tmp_station und tmp_anz_station löschen
                    $db->deleteTmpStation($user->uid, $user->sid, $zschiene[$i]);
                    $db->deleteTmpAnzStation($user->sid);
                endfor; # zeitschiene
            } else {
                for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
                    $db->setInsertTmpRotationsplan($user->uid, '999', $zschiene[$i], 1);
                endfor;
                $db->deleteMa3($user->uid);
            }
        endforeach; # user
    endif; # feste Station

# Mitarbeiter an die Endkontrolle schreiben
    $ekntrl = $db->getMaStation($idEndkontrolle, 3);
    $ma = array();
    if ($ekntrl):
        foreach ($ekntrl as $userEkntrl):
            $ma[] = $userEkntrl->uid;
        endforeach; #ekntrl
        # Wieviele Mitarbeiter haben die Qualifikation
        $anzahl = count($ma);
        $zfl = rand(0, 1);
        $folge = match ($anzahl) {
            3 => array(0, 1, 2),
            2 => array(0, 1, $zfl),
            1 => array(0, 0, 0),
        };
        shuffle($folge);
        # Zeitschienen
        for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
            # Eintrag speichern
            $db->setUpdateTmpRotationsplan($ma[$folge[$i]], $idEndkontrolle, $zschiene[$i], 1);
            # Eintrag aus tmp_station löschen
            $db->deleteTmpStation($ma[$folge[$i]], $idEndkontrolle, $zschiene[$i]);
        endfor; # Zeitschienen
        # Eintrag aus tmp_anzahl_station löschen
        $db->deleteTmpAnzStation($idEndkontrolle);
    endif; #ekntrl;

# Mitarbeiter ohne feste Station
    $grp = $db->getMaGrp();
    if ($grp):
        foreach ($grp as $user):
            # Anzahl der möglichen Stationen in die Datenbank schreiben
            $ps = $db->getAnzStationMa($user->uid);
            $db->setSummeStationMa($user->uid, $ps);
        endforeach; # grp
    endif; # grp

    # Die niedrigste Anzahl an Qualifikationen. Wenn die kleiner gleich 3 ist geht es hier weiter
    $mn = $db->getMinAnzStationen();
    if($mn != 0):
        unset($ma);
        # Qualifikationen abrufen - welche Stationen kann der KNecht
        foreach($mn AS $usr):
            $mitarbeiterID = $usr->uid;
            $q = $db->getAllQuali($usr->uid);
            foreach($q AS $s):
                $ma[] = $s->sid;
            endforeach;
            # Stationen
            $anzahl = count($ma);
            echo "<pre>"; var_dump($anzahl); echo "</pre>";
            $zfl = rand(0, 1);
            $folge = match ($anzahl) {
                2 => array(0, 1, $zfl),
                1, 0 => array(0, 0, 0),
                default => array(0, 1, 2)
            };
            shuffle($folge);
            # Zeitschienen
            for ($i = 0; $i < $_SESSION['parameter']['zeitschienen']; $i++):
                # Eintrag speichern
                $db->setUpdateTmpRotationsplan($mitarbeiterID, $ma[$folge[$i]], $zschiene[$i], 1);
                # Eintrag aus tmp_station löschen
                $db->deleteTmpStation($mitarbeiterID, $ma[$folge[$i]], $zschiene[$i]);
            endfor; # Zeitschienen
            # Eintrag aus tmp_anzahl_station löschen
            //$db->deleteTmpAnzStation($ma[$folge[$i]]); # Änderung 2022-06-15
        endforeach;
    endif;

# Alle Mitarbeiter aus der temporären Tabelle löschen, die bereits 3x im Einsatz sind
    $ma3 = $db->getMaRotationsplanGrp();
    if ($ma3):
        foreach ($ma3 as $user):
            $db->deleteMa3($user->uid);
        endforeach;
    endif; # request

# Restliche Mitarbeiter auf die Stationen verteilen
    $offen = $db->getOffenRotationsplan($springer);
    if ($offen):
        foreach ($offen as $station):
            #echo "Station: $station->sid &bull; $station->mitarbeiter &bull; $station->zeitschiene &bull; ";
            # Mitarbeiter mit den wenigsten Einsätzen abrufen
            $ma = $db->getAnzahlEinsatzStation($station->sid);
            if ($ma):
                foreach ($ma as $user):
                    # Ergonomie (aus Details der Station)
                    if (!isset($_SESSION['ergonomie']['' . $user->uid . ''])) $_SESSION['ergonomie']['' . $user->uid . ''] = 0;
                    $thsStatioin = $db->getStation($station->sid);
                    $ergo = ($thsStatioin->ergo == 'danger') ? 1 : 0;
                    #echo "$ergo &bull; {$_SESSION['ergonomie']['' . $user->uid . '']}<br>";
                    # Mitarbeiter anwesend ?
                    if ($db->getAnwesendMa($user->uid) === true && $db->getEinsatzMA($user->uid) < 3 && $db->getFreiMaZeit($user->uid, $station->zeitschiene) === false):
                        if ($_SESSION['ergonomie']['' . $user->uid . ''] > 0 && $ergo == 1) break;
                        if ($ergo == 1) $_SESSION['ergonomie']['' . $user->uid . '']++;
                        $db->setUpdateTmpRotationsplan($user->uid, $station->sid, $station->zeitschiene, $station->mitarbeiter);
                        $db->setUpdateQualifikation($user->uid, $station->sid);
                        $db->deleteEintrag($user->uid, $station->sid, $station->zeitschiene);
                        $name = AdministrationDatabase::getUserInfo($user->uid);
                        #echo "$name->vorname $name->name arbeitet in der Zeitschiene $station->zeitschiene an Station $station->sid<br>";
                        break;
                    endif; # Mitarbeiter anwesend
                endforeach; # $ma
            endif; #ma
        endforeach; # offen
    endif;

    # Alle Mitarbeiter aus der temporären Tabelle löschen, die bereits 3x im Einsatz sind
    $ma3 = $db->getMaRotationsplanGrp();
    if ($ma3):
        foreach ($ma3 as $user):
            $db->deleteMa3($user->uid);
        endforeach;
    endif; # request

    # Rotationsplan anlegen und temporäre Tabellen leeren
    $db->setRotationsplan($plandate);
    $db->deleteTmpTables($plandate);
    # Logfile schreiben
    # Functions::logfile("Rotationsplan", "", "", "Rotationsplan für den $plandate - {$_SESSION['user']['wrk_abteilung']}.{$_SESSION['user']['wrk_abteilung']} erstellt");
    # Ab auf die Startseite
    header(header: 'Location: /rotationsplan/verwaltung');

endif;
# Logfile schreiben
# Functions::logfile('Rotationsplan', '', '', 'Seite aufgerufen');