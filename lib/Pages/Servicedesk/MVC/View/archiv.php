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

?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
</head>
<body class="d-flex flex-column h-100 servicedesk" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation('', 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_serviceDesk']}");
?>
<main class="w-100 bg__white--99 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid p-0">
        <table class="table table-bordered table-striped">
            <thead class="bg__blue-gray--6 font-size-12 oswald">
            <tr>
                <th class="font-weight-400">ID</th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_abteilung'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_bereich'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_titel'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_ersteller'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_bearbeiter'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['s_gestartet'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['s_beendet'] ?></th>
                <th class="text-center font-weight-400"><?= $_SESSION['text']['shortTage'] ?></th>
                <th class="text-center font-weight-400"><?= $_SESSION['text']['shortStunden'] ?></th>
                <th class="text-center font-weight-400"><?= $_SESSION['text']['shortMinuten'] ?></th>
                <th class="text-center font-weight-400"><?= $_SESSION['text']['shortSekunden'] ?></th>
                <th class="font-weight-400"><?= $_SESSION['text']['h_abgeschlossen'] ?></th>
            </tr>
            </thead>
            <tbody class="font-size-12">
            <?php
            foreach($eintrag AS $row):
                # Bearbeiter
                $bid = ServicedeskDatabase::getBearbeiter($row->id);
                $usr = AdministrationDatabase::getUserInfo($bid);
                # Bearbeitungszeit ermitteln
                $start = new DateTime(servicedeskDatabase::getDateQuery($row->id, 'Bearbeitung gestartet', 'ASC'));
                $ende = new DateTime(servicedeskDatabase::getDateQuery($row->id, 'Bearbeitung beendet', 'DESC'));
                $intvl = $start->diff($ende);
                $dspStart = $start->format('d.m.Y H:i');
                $dspEnde = $ende->format('d.m.Y H:i');

                # Parameter
                $tage = $intvl->days;
                $stunden = $intvl->h;
                $minuten = $intvl->i;
                $sekunden = $intvl->s;
                # Ersteller
                $vorname = (isset($usr->vorname)) ? $usr->vorname : 'k. ';
                $name = (isset($usr->name)) ? $usr->name : 'A.';

                # Abgeschlossen
                $abgeschlossen = ServicedeskDatabase::getInfo($row->id, "{$_SESSION['text']['t_serviceauftragAbgeschlossen']}");
                $abgs = ($abgeschlossen == '-') ? '-' : Functions::germanDate($abgeschlossen->tag);

                # ID
                $id = Functions::encrypt($row->id);
            ?>
            <tr class="pointer" onclick="top.location.href='/servicedesk/details?id=<?= $id ?>'">
                <td class="text-end"><?= $row->id ?></td>
                <td><?= $_SESSION['text'][''.AdministrationDatabase::getOneAbt('b_abteilung_rlms', $row->aid).''] ?></td>
                <td><?= $_SESSION['text'][''.AdministrationDatabase::getOneBereich('b_bereich_rlms', $row->bid).''] ?></td>
                <td><?= $row->titel ?></td>
                <td><?= $row->user ?></td>
                <td><?= $vorname ?> <?= $name ?></td>
                <td><?= $dspStart ?></td>
                <td><?= $dspEnde ?></td>
                <td class="text-center"><?= $tage ?></td>
                <td class="text-center"><?= $stunden ?></td>
                <td class="text-center"><?= $minuten ?></td>
                <td class="text-center"><?= $sekunden ?></td>
                <td><?= $abgs ?></td>
            </tr>
            <?php
            endforeach;
            ?>
            </tbody>
        </table>
    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>

<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
</script>
</body>
</html>