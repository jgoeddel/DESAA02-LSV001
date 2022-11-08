<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Servicedesk\ServicedeskDatabase;

$_SESSION['seite']['id'] = 9;
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
    <link rel="stylesheet" type="text/css" href="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.css">
</head>
<body class="d-flex flex-column h-100 schulung" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_schulungen']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <form id="insertSchulung" method="post">
        <div class="container-fluid py-4">
            <div class="row p-0 m-0">
                <div class="px-4">
                    <div class="row">
                        <div class="col-3 border__right--dotted-gray">
                            <div class="pe-3">
                                <h3 class="font-weight-100 border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_neueSchulung'] ?></h3>
                                <?php Functions::alert($_SESSION['text']['i_neueSchulung']); ?>
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-4 border__right--dotted-gray">
                                        <div class="pe-3">
                                            <label class="font-size-12 text-muted italic" for="datum">
                                                <?= $_SESSION['text']['h_datum'] ?> <span class="text-warning">*</span>
                                            </label>
                                            <input type="date" class="invisible-formfield" name="datum" required>
                                        </div><!-- pe-3 -->
                                    </div><!-- col -->
                                    <div class="col-8">
                                        <div class="ps-3">
                                            <label class="font-size-12 text-muted italic" for="abteilung">
                                                <?= $_SESSION['text']['h_abteilung'] ?> <span
                                                        class="text-warning">*</span>
                                            </label>
                                            <select name="abteilung" class="invisible-formfield" required>
                                                <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                                <?php
                                                foreach ($abt as $a):
                                                    ?>
                                                    <option value="<?= $a->id ?>"><?= $_SESSION['text']['abt_' . $a->id . ''] ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                            </select>
                                        </div><!-- ps-3 -->
                                    </div><!-- col -->
                                </div><!-- row -->
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="raum">
                                            <?= $_SESSION['text']['h_raum'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <input type="search" class="invisible-formfield" name="raum" list="room"
                                               placeholder="<?= $_SESSION['text']['h_raum'] ?>?" required>
                                        <datalist id="room">
                                            <?php
                                            foreach ($rms as $b):
                                                ?>
                                                <option value="<?= $b->raum ?>"><?= $b->raum ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </datalist>
                                    </div><!-- col -->
                                </div><!-- row -->
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="art">
                                            <?= $_SESSION['text']['t_artSchulung'] ?> <span
                                                    class="text-warning">*</span>
                                        </label>
                                        <select name="art" class="invisible-formfield" required>
                                            <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                            <?php
                                            foreach ($art as $d):
                                                ?>
                                                <option value="<?= $d->id ?>"><?= $_SESSION['text']['art_' . $d->id . ''] ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- col -->
                                </div><!-- row -->
                            </div><!-- pe-3 -->
                        </div><!-- col-3 -->
                        <div class="col-3 border__right--dotted-gray">
                            <div class="px-3">
                                <?php
                                Functions::alert($_SESSION['text']['i_schulungsleiter']);
                                ?>
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="mid">
                                            <?= $_SESSION['text']['t_werSchulungsleiter'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <input type="search" class="invisible-formfield" name="mid" id="mid"
                                               list="lehrer"
                                               onblur="showLehrer(this.value)"
                                               placeholder="<?= $_SESSION['text']['t_vorname'] ?> <?= $_SESSION['text']['t_name'] ?>?"
                                               required>
                                        <datalist id="lehrer">
                                            <?php
                                            foreach ($ma as $c):
                                                ?>
                                                <option value="<?= $c->vorname ?> <?= $c->name ?>"><?= $c->vorname ?> <?= $c->name ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </datalist>
                                        <div id="dspLehrer">

                                        </div>
                                    </div><!-- col -->
                                </div><!-- row -->
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-3 border__right--dotted-gray">
                                        <div class="pe-3">
                                            <label class="font-size-12 text-muted italic" for="schicht">
                                                <?= $_SESSION['text']['h_schicht'] ?> <span
                                                        class="text-warning">*</span>
                                            </label>
                                            <select name="schicht" class="invisible-formfield">
                                                <option value="0"><?= $_SESSION['text']['b_alle'] ?></option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                            </select>
                                        </div><!-- pe-3 -->
                                    </div><!-- col -->
                                    <div class="col-4 border__right--dotted-gray">
                                        <div class="px-3">
                                            <label class="font-size-12 text-muted italic" for="start">
                                                <?= $_SESSION['text']['h_beginn'] ?> <span class="text-warning">*</span>
                                            </label>
                                            <input type="time" class="invisible-formfield" name="start" required>
                                        </div><!-- px-3 -->
                                    </div><!-- col -->
                                    <div class="col-5">
                                        <div class="px-3">
                                            <label class="font-size-12 text-muted italic" for="dauer">
                                                <?= $_SESSION['text']['h_schulungsdauer'] ?> <span class="text-warning">*</span>
                                            </label>
                                            <input type="number" class="invisible-formfield" name="dauer" required>
                                        </div><!-- px-3 -->
                                    </div><!-- col -->
                                </div><!-- row -->
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="art">
                                            <?= $_SESSION['text']['t_festerPersonenkreis'] ?> <span
                                                    class="text-warning">*</span>
                                        </label>
                                        <select name="teilnehmer" class="invisible-formfield" required>
                                            <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                            <option value="1"><?= $_SESSION['text']['ja'] ?></option>
                                            <option value="0"><?= $_SESSION['text']['nein'] ?></option>
                                        </select>
                                    </div><!-- col -->
                                </div><!-- row -->
                            </div><!-- px-3 -->
                        </div><!-- col-3 -->
                        <div class="col-4 border__right--dotted-gray">
                            <div class="px-3">
                                <div class="row pb-3 mb-3 border__bottom--dotted-gray">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="thema">
                                            <?= $_SESSION['text']['t_themaSchulung'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <input type="text" name="thema" class="invisible-formfield" placeholder="<?= $_SESSION['text']['p_themaSchulung'] ?>">
                                    </div><!-- col -->
                                </div><!-- row -->
                                <div class="row pb-3 mb-3">
                                    <div class="col-12">
                                        <label class="font-size-12 text-muted italic" for="inhalt">
                                            <?= $_SESSION['text']['t_inhaltSchulung'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <textarea class="mb-3" name="inhalt" id="summernote" required placeholder="<?= $_SESSION['text']['p_inhaltSchulung'] ?>"></textarea>
                                    </div><!-- col -->
                                </div><!-- row -->
                            </div><!-- px-3 -->
                        </div><!-- col -->
                        <div class="col-2">
                            <div class="ps-2">
                                <?php
                                Functions::alert($_SESSION['text']['i_formSchulung']);
                                ?>
                                <div class="text-end">
                                    <input type="submit" class="btn btn-primary oswald font-weight-300 font-size-16 text-uppercase" value="<?= $_SESSION['text']['b_schulungSpeichern'] ?>">
                                </div>
                            </div>
                        </div><!-- col -->
                    </div><!-- row -->
                </div><!-- px-4 -->
            </div><!-- row -->
        </div><!-- fluid -->
    </form>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>lib/Pages/Schulungen/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>lib/Pages/Schulungen/MVC/View/js/submit.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
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
</script>
</body>
</html>