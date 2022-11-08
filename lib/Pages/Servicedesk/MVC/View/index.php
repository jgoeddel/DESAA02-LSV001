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

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
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
                        <form method="post" id="insertEintrag" class="needs-validation">
                            <h3 class="oswald font-weight-100 pb-3 mb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_intServiceW'] ?></h3>
                            <?php Functions::alert($_SESSION['text']['i_intServiceW']); ?>
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-6 border__right--dotted-gray">
                                    <div class="pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['t_woFehler'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <select class="invisible-formfield" name="abteilung" id="abteilung" required>
                                            <option value=""><?= $_SESSION['text']['h_abtBuero'] ?></option>
                                            <?php
                                            foreach (AdministrationDatabase::getAllAbt('b_abteilung_rlms', 'servicedesk') as $row):
                                                echo "<option value='$row->id'>{$_SESSION['text'][''.$row->abteilung.'']}</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- pe -->
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            &nbsp;
                                        </label>
                                        <select class="invisible-formfield" name="bereich" id="bereich" required>
                                            <option value=""><?= $_SESSION['text']['h_area'] ?></option>
                                            <?php
                                            foreach (AdministrationDatabase::getAllBereich('b_bereich_rlms') as $row):
                                                echo "<option value='$row->id'>{$_SESSION['text'][''.$row->bereich.'']}</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- pe -->
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-12">
                                    <label class="font-size-12 text-muted italic" for="start">
                                        <?= $_SESSION['text']['h_vorname'] ?> <span class="text-warning">*</span>
                                    </label>
                                    <?php Functions::invisibleInput("text", "titel", "", "", "", "required", "{$_SESSION['text']['h_titel']}"); ?>
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-12">
                            <textarea class="invisible-formfield" rows="5" name="beschreibung"
                                      placeholder="<?= $_SESSION['text']['i_problemBeschreibung'] ?>"
                                      required></textarea>
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="text-end">
                                <?php Formular::submit("submit", "{$_SESSION['text']['b_serviceauftragSpeichern']}", "btn btn-primary font-weight-300 oswald"); ?>
                            </div>
                        </form>
                    </div><!-- pe -->
                </div><!-- col -->
                <div class="col-9">
                    <div class="ps-3">
                        <h3 class="oswald font-weight-100 pb-3 mb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_meineServiceauftraege'] ?></h3>
                        <?php
                        if (empty($anzE)):Functions::alert($_SESSION['text']['t_keineServiceauftraege']);
                        else:
                            # Was wird angezeigt
                            $dsp = $anzE;
                            include "includes/inc.dsp.block.php";
                        endif;
                        ?>

                        <h3 class="oswald font-weight-100 pb-3 mb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_offeneServiceauftraege'] ?></h3>
                        <?php Functions::alert($_SESSION['text']['i_offeneServiceauftraege']); ?>
                        <?php
                        if (empty($eintrag)): Functions::alert($_SESSION['text']['t_keineServiceauftraege']);
                        else:
                            # Was wird angezeigt
                            $dsp = $eintrag;
                            include "includes/inc.dsp.block.php";
                        endif;
                        ?>
                    </div>
                </div>
            </div><!-- row -->
        </div><!-- px-5 -->
    </div><!-- fluid -->

</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Servicedesk/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
</script>
</body>
</html>