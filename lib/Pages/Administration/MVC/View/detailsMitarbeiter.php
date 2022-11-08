<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 23;
$_SESSION['seite']['name'] = 'mitarbeiterDetails';
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
# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /?e=1');
endif;

# Ausgewähler Mitarbeiter
$id = Functions::encrypt($id);
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
        <div class="p-4"><!-- Inhalt -->

            <div class="row">
                <div class="col-12 col-md-3 border__right--dotted-gray">
                    <div class="pe-3">
                        <?php
                        # Passwort
                        $pw = (new App\Functions\Functions)->generatePassword(10, 1, 4);
                        # Datenbankvariablen
                        $id = ($shw == 1) ? $user->id : '';
                        $vorname = ($shw == 1) ? $user->vorname : '';
                        $name = ($shw == 1) ? $user->name : '';
                        $citycode = ($shw == 1) ? $user->citycode : '';
                        $abteilung = ($shw == 1) ? $user->abteilung : '';
                        $username = ($shw == 1) ? $user->username : '';
                        $email = ($shw == 1) ? $user->email : '';
                        $wrk_schicht = ($shw == 1) ? $user->wrk_schicht : '';
                        $wrk_abteilung = ($shw == 1) ? $user->wrk_abteilung : '';
                        $pw = ($shw == 1) ? '' : $pw;
                        ?>
                        <form id="mitarbeiterNeu" method="post" class="needs-validation">
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
                            <?php Functions::alert("Die beiden nachfolgenden Felder sind ausschließlich für den Standort <b>DESAA02</b> relevant!"); ?>
                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-6">
                                    <div class="border__right--dotted-gray pe-3">
                                        <label class="font-size-12 text-muted italic" for="wrk_schicht">
                                            <?= $_SESSION['text']['h_schicht'] ?> (<?= $_SESSION['text']['h_rotationsplan'] ?>)
                                        </label>
                                        <?php Functions::invisibleInput("number", "wrk_schicht", "", "$wrk_schicht", "", "", "{$_SESSION['text']['h_schicht']}"); ?>
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
                                                $slctd = ($wrk_abteilung == $abt->rotationsplan) ? 'selected' : '';
                                                echo "<option value='$abt->rotationsplan' $slctd>$abt->abteilung</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    </div><!-- ps-3 -->
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray mr-0 pb-3 mb-3">
                                <div class="col-10 border__right--dotted-gray">
                                    <div class="pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_mail'] ?> <span class="text-warning">*</span>
                                            <span class="korrekt ms-2"></span>
                                            <span class="float-end"><i class="fa fa-question-circle pointer"
                                                                       onclick="generateValues()"></i></span>
                                        </label>
                                        <?php Functions::invisibleInput("email", "email", "", "$email", "", "required", "{$_SESSION['text']['h_mail']}"); ?>
                                    </div><!-- pe-3 -->
                                </div><!-- col -->
                                <div class="col-2">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="status"><?= $_SESSION['text']['h_status'] ?></label>
                                        <select name="status" class="invisible-formfield">
                                            <?php
                                            $s1 = ($user->status == 1) ? 'selected' : '';
                                            $s2 = ($user->status == 0) ? 'selected' : '';
                                            ?>
                                            <option value="1" <?= $s1 ?>>1</option>
                                            <option value="0" <?= $s2 ?>>0</option>
                                        </select>
                                    </div>
                                </div>
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
                                    <?= $_SESSION['text']['b_aenderungenSpeichern'] ?>
                                </button>
                            </div>
                        </form>
                    </div><!-- pe-3 -->
                </div><!-- /col -->
                <div class="col-12 col-md-9">
                    <div class="ps-3">
                        <div class="row">
                            <div class="col-3 border__right--dotted-gray">
                                <div class="pe-3">
                                    <h4 class="oswald font-weight-300 border__bottom--dotted-gray mb-2 pb-2">Verfügbare
                                        Bereiche</h4>
                                    <ul id="sortable1" class="connectedSortable bg__blue-gray--6 p-2">
                                        <?php
                                        foreach (AdministrationDatabase::getAllAdminPages() as $row):
                                            if (AdministrationDatabase::checkRechtePage($id, $row->id) == 0):
                                                ?>
                                                <li id="<?= $row->id ?>"
                                                    class="ui-state-default"><?= $_SESSION['text']['' . $row->i18n . ''] ?> <small class="font-size-11 text-muted ms-2"><?= $row->id ?></small></li>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-3 border__right--dotted-gray">
                                <div class="px-3">
                                    <h4 class="oswald font-weight-300 border__bottom--dotted-gray mb-2 pb-2">
                                        Leserechte</h4>
                                    <ul id="sortable2" class="connectedSortable bg__blue-gray--6 p-2">
                                        <?php
                                        foreach (AdministrationDatabase::getAllAdminPages() as $row):
                                            if (AdministrationDatabase::checkRechtePageDetail($id, $row->id, 1) > 0):
                                                ?>
                                                <li id="<?= $row->id ?>"
                                                    class="ui-state-default"><?= $_SESSION['text']['' . $row->i18n . ''] ?> <small class="font-size-11 text-muted ms-2"><?= $row->id ?></small></li>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-3 border__right--dotted-gray">
                                <div class="px-3">
                                    <h4 class="oswald font-weight-300 border__bottom--dotted-gray mb-2 pb-2">
                                        Schreibrechte</h4>
                                    <ul id="sortable3" class="connectedSortable bg__blue-gray--6 p-2">
                                        <?php
                                        foreach (AdministrationDatabase::getAllAdminPages() as $row):
                                            if (AdministrationDatabase::checkRechtePageDetail($id, $row->id, 2) > 0):
                                                ?>
                                                <li id="<?= $row->id ?>"
                                                    class="ui-state-default"><?= $_SESSION['text']['' . $row->i18n . ''] ?> <small class="font-size-11 text-muted ms-2"><?= $row->id ?></small></li>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="ps-3">
                                    <h4 class="oswald font-weight-300 border__bottom--dotted-gray mb-2 pb-2">
                                        Adminrechte</h4>
                                    <ul id="sortable4" class="connectedSortable bg__blue-gray--6 p-2">
                                        <?php
                                        foreach (AdministrationDatabase::getAllAdminPages() as $row):
                                            if (AdministrationDatabase::checkRechtePageDetail($id, $row->id, 0) > 0):
                                                ?>
                                                <li id="<?= $row->id ?>"
                                                    class="ui-state-default"><?= $_SESSION['text']['' . $row->i18n . ''] ?> <small class="font-size-11 text-muted ms-2"><?= $row->id ?></small></li>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

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
        $(function () {
            const lesen = [];
            const schreiben = [];
            const admin = [];
            $("#sortable1, #sortable2, #sortable3, #sortable4").sortable({
                connectWith: ".connectedSortable",
            }).disableSelection();
            $("#sortable2").sortable({
                update: function (event, ui) {
                    $.each($(this).sortable('toArray'), function (key, value) {
                        lesen.push(value.replace('el-', ''))
                    });
                    console.log(lesen);
                    $.post("/administration/mitarbeiter/setRechte", { rechte: 1, id: ''+lesen+'', mid: <?= $id ?> },function(){

                    });
                    lesen.length = 0;
                }
            }).disableSelection();
            $("#sortable3").sortable({
                update: function (event, ui) {
                    $.each($(this).sortable('toArray'), function (key, value) {
                        schreiben.push(value.replace('el-', ''))
                    });
                    console.log(schreiben);
                    $.post("/administration/mitarbeiter/setRechte", { rechte: 2, id: ''+schreiben+'', mid: <?= $id ?> },function(){

                    });
                    schreiben.length = 0;
                }
            }).disableSelection();
            $("#sortable4").sortable({
                update: function (event, ui) {
                    $.each($(this).sortable('toArray'), function (key, value) {
                        admin.push(value.replace('el-', ''))
                    });
                    console.log(admin);
                    $.post("/administration/mitarbeiter/setRechte", { rechte: 0, id: ''+admin+'', mid: <?= $id ?> },function(){

                    });
                    admin.length = 0;
                }
            }).disableSelection();
        });
    });
</script>
</body>
</html>