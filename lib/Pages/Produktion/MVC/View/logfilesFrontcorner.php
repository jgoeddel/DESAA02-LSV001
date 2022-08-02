<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 51;
$_SESSION['seite']['name'] = 'logfilesFrontcorner';
$subid = 0;
$n_suche = '';
$dspKalender = false;
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

# Ordnername
$ordner = '\\\\172.16.101.101\\logfilesFC\\*'; # auch komplette Pfade möglich ($ordner = "download/files";)
# Ordner auslesen und Array in Variable speichern
if(is_dir($ordner)){
    echo "Hach wie schön";
} else {
    echo "Hach wie schade";
}
$i = 1;
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
<body class="d-flex flex-column h-100 ford" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_logfilesFC']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <?php

    ?>
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