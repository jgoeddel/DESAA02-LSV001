<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 1;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(1);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
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
<body class="d-flex flex-column h-100 mitarbeiter" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit,'index','',1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_administration']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid">
        <div class="my-4">
            <div class="row">
                <?php
                foreach ($_SESSION['menu']['admin'] as $data):
                    if (AdministrationDatabase::checkRechtePage($_SESSION['user']['id'], $data->id) > 0):
                    $pctr = str_replace("/administration/","",$data->link);
                    Functions::htmlOpenDivNoBorder("3","","","","p-3");
                    ?>
                    <!-- Beginn Card -->
                    <div class="card">
                        <div class="card__side card__side--front">
                            <div class="card__picture card__picture--<?= $pctr ?>">
                                &nbsp;
                            </div>
                            <h4 class="card__heading">
                                    <span class="card__heading-span card__heading-span--2 oswald">
                                        <?= $_SESSION['text']['' . $data->i18n . ''] ?>
                                    </span>
                            </h4>
                        </div><!-- card__side -->
                        <div class="card__side card__side--back card__side--back-1">
                            <div class="card__cta">
                                <a href="<?= $data->link ?>"
                                   class="btn__white oswald"><?= $_SESSION['text']['h_anzeigen'] ?></a>
                            </div>
                        </div><!-- card__side -->
                    </div><!-- card -->

                    <?php
                    endif;
                    Functions::htmlCloseDiv();
                endforeach;
                ?>
                <!-- /Ende Card -->
            </div><!-- row -->
        </div><!-- my-4 -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/action.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        //heightHeader();

    });
</script>
</body>
</html>