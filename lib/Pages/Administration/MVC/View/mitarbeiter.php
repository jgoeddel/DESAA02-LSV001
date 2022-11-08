<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 23;
$_SESSION['seite']['name'] = 'mitarbeiter';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
($seiteschreiben == 1) ? $col = '9' : $col = '12';
# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /?e=1');
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
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_userverwaltung']}");
?>
<main class="w-100 bg__white--99 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->

            <div class="row">

                <div class="col-12 col-md-3 border__right--dotted-gray <?= $dspedit ?>">
                    <div class="pe-3">
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3">
                            <?= $_SESSION['text']['h_userverwaltung'] ?>
                        </h3>
                        <div class="font-size-12 border__bottom--dotted-gray mb-3 pb-3">
                            <?= $_SESSION['text']['t_userverwaltung'] ?>
                        </div>

                        <form id="mitarbeiterNeu" method="post" class="needs-validation">
                            <?php
                            # Passwort
                            $pw = (new App\Functions\Functions)->generatePassword(10, 1, 4);
                            # Datenbankvariablen
                            $shw = ($user != '') ? 1 : 0;
                            $id = ($shw == 1) ? $user->id : '';
                            $vorname = ($shw == 1) ? $user->vorname : '';
                            $name = ($shw == 1) ? $user->name : '';
                            $citycode = ($shw == 1) ? $user->citycode : '';
                            $abteilung = ($shw == 1) ? $user->abteilung : '';
                            $username = ($shw == 1) ? $user->username : '';
                            $email = ($shw == 1) ? $user->email : '';
                            $pw = ($shw == 1) ? '' : $pw;
                            ?>
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-6">
                                    <div class="border__right--dotted-gray pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_vorname'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "vorname", "", "$vorname", "", "required", "{$_SESSION['text']['h_vorname']}"); ?>
                                    </div><!-- pe-3 -->
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_name'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "name", "", "$name", "", "required", "{$_SESSION['text']['h_name']}"); ?>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-6">
                                    <div class="border__right--dotted-gray pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            Citycode <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "citycode", "", "$citycode", "onblur='generateValues()'", "required", "Citycode"); ?>
                                    </div><!-- pe-3 -->
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_abteilung'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <select name="abteilung" class="invisible-formfield">
                                            <option value="21"><?= $_SESSION['text']['h_abteilung'] ?>?</option>
                                            <?php
                                            foreach ($ab as $abt):
                                                $slctd = ($abteilung == $abt->id) ? 'selected' : '';
                                                echo "<option value='$abt->id' $slctd>$abt->abteilung</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                            </div><!-- row -->

                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-12">
                                    <div class="">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_mail'] ?> <span class="text-warning">*</span>
                                            <span class="korrekt ms-2"></span>
                                            <span class="float-end"><i class="fa fa-question-circle pointer"
                                                                       onclick="generateValues()"></i></span>
                                        </label>
                                        <?php Functions::invisibleInput("email", "email", "", "$email", "", "required", "{$_SESSION['text']['h_mail']}"); ?>
                                    </div><!-- pe-3 -->
                                </div><!-- col -->
                            </div><!-- row -->
                            <?php Functions::alert("Die beiden nachfolgenden Felder sind ausschließlich für den Standort <b>DESAA02</b> relevant!"); ?>
                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-6">
                                    <div class="border__right--dotted-gray pe-3">
                                        <label class="font-size-12 text-muted italic" for="wrk_schicht">
                                            <?= $_SESSION['text']['h_schicht'] ?> (<?= $_SESSION['text']['h_rotationsplan'] ?>)
                                        </label>
                                        <?php Functions::invisibleInput("number", "wrk_schicht", "", "", "", "", "{$_SESSION['text']['h_schicht']}"); ?>
                                    </div><!-- pe-3 -->
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_abteilung'] ?> (<?= $_SESSION['text']['h_rotationsplan'] ?>)
                                        </label>
                                        <select name="wrk_abteilung" class="invisible-formfield">
                                            <option value="21"><?= $_SESSION['text']['h_abteilung'] ?>?</option>
                                            <?php
                                            foreach ($ab2 as $abt):
                                                $slctd = ($abteilung == $abt->id) ? 'selected' : '';
                                                echo "<option value='$abt->rotationsplan' $slctd>$abt->abteilung</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                            </div><!-- row -->

                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-6">
                                    <div class="pe-3 border__right--dotted-gray">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_username'] ?> <span class="text-warning">*</span>
                                            <span class="korrekt ms-2"></span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "username", "", "$username", "", "required", "{$_SESSION['text']['h_username']}"); ?>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_passwort'] ?> <?php if (!isset($id)): ?><span
                                                    class="text-warning">*</span><?php endif; ?>
                                        </label>
                                        <?php
                                        $req = ($pw != '') ? 'required' : '';
                                        Functions::invisibleInput("text", "password", "", "$pw", "", "$req", "{$_SESSION['text']['h_passwort']}"); ?>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                            </div><!-- row -->

                            <div class="text-end">
                                <button class="btn btn-primary oswald font-weight-300 text-uppercase">
                                    <?= $_SESSION['text']['b_maSpeichern'] ?>
                                </button>
                            </div>
                        </form>


                    </div><!-- pe-3 -->
                </div><!-- /col -->
                <div class="col-12 col-md-<?= $col ?>">
                    <div class="ps-3">
                        <div class="row m-0 p-0" id="usr">
                            <?php
                            foreach ($ma as $row):
                                $letter = substr($row->name, 0, 1);
                                $vletter = substr($row->vorname, 0, 1);
                                $mid = Functions::encrypt($row->id);
                                $rfid = ($row->rfid) ? '<i class="fa fa-rss text-primary ps-1"></i>' : '';
                                $ml = ($row->email) ? '<i class="fa fa-envelope text-gray ps-1"></i>' : '';
                                $lg = (!$row->datum) ? '<i class="fa fa-times text-danger ps-1"></i>' : '';
                                $dsplogin = (!$row->datum) ? ''.$_SESSION['text']['i_keineAnmeldung'].'' : $row->login.' Uhr';
                                $login = ($row->status == 1) ? ''.$dsplogin.'' : '<i class="fa fa-exclamation-triangle text-warning me-2"></i><b>'.$_SESSION['text']['i_mitarbeiterGesperrt'].'</b>';
                                if ($row->bild == '') $row->bild = "avatar.jpg";
                                if (IndexDatabase::checkRechteCitycode($row->citycode) > 0):
                                    ?>
                                    <div class="col-2 pointer all filter<?= $letter ?> filter<?= $row->citycode ?>"
                                         <?php if ($seiteschreiben == 1): ?>onclick="top.location.href='/administration/mitarbeiter/details?id=<?= $mid ?>'"<?php endif; ?>>
                                        <div class="mx-2">
                                            <div class="kommentar row border__bottom--dotted-gray m-0 p-0 pb-2">
                                                <div class="col-4 text-center pt-2">
                                                    <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $row->bild ?>"
                                                         class="rund_small img-thumbnail img-fluid">
                                                </div>
                                                <div class="col-8 font-size-14 pt-2 pe-2">
                                                    <p class="p-0 m-0 pt-2">
                                                    <span class="oswald">
                                                        <?= $vletter ?>. <?= $row->name ?>
                                                    </span></p>
                                                    <p class="font-size-11 text-warning p-0 m-0">
                                                        <?= $row->citycode ?>
                                                        <span class="float-end">
                                                            <?= $lg ?>
                                                            <?= $ml ?>
                                                            <?= $rfid ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="kommentar border__bottom--dotted-gray text-center font-size-11 m-0 p-0 mb-2 bg__blue-gray--6">
                                                <?= $login ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endif;
                            endforeach;
                            ?>
                        </div><!-- ps-3 -->
                    </div><!-- row -->
                </div><!-- col -->
            </div><!-- row -->

        </div><!-- px-4 -->
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