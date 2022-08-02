<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'details';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(36);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];
// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';

# Sind noch Änderungen möglich ?
$edit = ($seiteschreiben == 1 && $row->status < 6) ? 1 : 0;
$heute = DATE('Y-m-d');
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
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    Functions::dspNavigation($dspedit, 'details', $id, 0);
    ?>
</div>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->
            <h3 class="mb-3 pb-3 border__bottom--dotted-gray">
                <?= $_SESSION['text']['h_lop'] ?>
                <?php if($edit === 1): ?>
                <span class="float-end">
                    <button class="btn btn-sm btn-primary oswald text-uppercase" id="btnneu"  onclick="$('#lopneu,#btnneu').toggle(800)">
                        <i class="fa fa-plus pe-2"></i><?= $_SESSION['text']['b_neuerEintrag'] ?>
                    </button>
                </span>
                <?php endif; ?>
            </h3>
            <form id="lopneu" method="post" class="form needs-validation dspnone border__bottom--dotted-gray pb-3 mb-3">
                <input type="hidden" name="id" id="id" value="<?= $id ?>">
                <?php
                Functions::htmlOpenBorderDiv2("row m-0 pb-3 mb-3","bottom", "dotted");
                    Functions::htmlOpenDiv("4","right","dotted","","","","pe-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_triggeredBy']}","text","trg","","","required","{$_SESSION['text']['h_triggeredBy']}");
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("4","right","dotted","","","","px-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_area']}","text","area","","","required","{$_SESSION['text']['h_area']}");
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("2","right","dotted","","","","px-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_datum']}","date","datum","","","required","{$_SESSION['text']['h_datum']}");
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("2","","","","","","ps-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_dueDate']}","date","due_date","","","required","{$_SESSION['text']['h_dueDate']}");
                    Functions::htmlCloseDiv();
                Functions::htmlCloseSingleDiv();
                Functions::htmlOpenBorderDiv2("row m-0 pb-3 mb-3","bottom", "dotted");
                    Functions::htmlOpenDiv("8","right","dotted","","","","pe-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_openIssue']}","text","issue","","","required","{$_SESSION['text']['h_openIssue']}");
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("4","","","","","","ps-3");
                    ?>
                    <label class='form-label font-size-10 text-muted italic p-0 m-0'><?= $_SESSION['text']['h_supported'] ?></label>
                    <select name="support" class="invisible-formfield" required>
                        <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                        <?php
                        foreach (IndexDatabase::selectMaCC($row->location) as $select_usr):
                            ?>
                            <option value="<?= $select_usr->vorname ?> <?= $select_usr->name ?>"><?= $select_usr->name ?>
                                , <?= $select_usr->vorname ?> </option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                    <?php
                    Functions::htmlCloseDiv();
                Functions::htmlCloseSingleDiv();
                Functions::htmlOpenBorderDiv2("row m-0 pb-3 mb-3","bottom", "dotted");
                    Functions::htmlOpenDiv("8","right","dotted","","","","pe-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_actionExpected']}","text","action","","","required","{$_SESSION['text']['h_actionExpected']}");
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("4","","","","","","ps-3");
                    Formular::labelInvisibleInput("{$_SESSION['text']['h_verantwortlich']}","text","resp","","","required","{$_SESSION['text']['h_verantwortlich']}");
                    Functions::htmlCloseDiv();
                Functions::htmlCloseSingleDiv();
                ?>
                <div class="text-end">
                    <?php
                    Formular::submit("reset", "{$_SESSION['text']['b_abbrechen']}", "btn btn-warning me-2");
                    Formular::submit("submit", "{$_SESSION['text']['b_eintragSpeichern']}", "btn btn-primary");
                    ?>
                </div>
            </form>
            <table class="table table-sm table-bordered table-striped dTable font-size-12">
                <thead class="thead-dark font-weight-300">
                <tr>
                    <th><?= $_SESSION['text']['h_nr'] ?></th>
                    <th><?= $_SESSION['text']['h_datum'] ?></th>
                    <th><?= $_SESSION['text']['h_triggeredBy'] ?></th>
                    <th><?= $_SESSION['text']['h_area'] ?></th>
                    <th><?= $_SESSION['text']['h_openIssue'] ?></th>
                    <th><?= $_SESSION['text']['h_actionExpected'] ?></th>
                    <th><?= $_SESSION['text']['h_dueDate'] ?></th>
                    <th><?= $_SESSION['text']['h_verantwortlich'] ?></th>
                    <th><?= $_SESSION['text']['h_supported'] ?></th>
                    <th class="text-center"><?= $_SESSION['text']['h_status'] ?></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="dspTbodyLOP">
                <?php
                if (!empty($lp)):
                    foreach ($lp as $lop):
                        ($lop->status == 1) ? $del = 'text-decoration-line-through' : $del = '';
                        ?>
                        <tr class="font-weight-300 <?= $del ?>">
                            <td><?= $lop->id ?></td>
                            <td><?= $lop->eintrag ?></td>
                            <td><?= $lop->trg ?></td>
                            <td><?= $lop->area ?></td>
                            <td><?= $lop->open_issue ?></td>
                            <td><?= $lop->action ?></td>
                            <td><?= $lop->due ?></td>
                            <td><?= $lop->resp ?></td>
                            <td><?= $lop->support ?></td>
                            <td class="text-center">
                                <?php ChangeManagementDatabase::statusLOP($lop->id, $heute, $lop->due_date, $lop->status) ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($lop->status == 1): echo '<i class="fa fa-times text-muted"></i>'; endif;
                                if ($lop->status < 9 && $lop->status != 1): ?>
                                    <i class="fa fa-check-square text-muted pointer"
                                       onclick="changeLopStatus(<?= $lop->id ?>, 9)"></i>
                                <?php
                                endif;
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($lop->status == 1): ?>
                                    <i class="fa fa-times text-muted pointer" onclick="changeLopStatus(<?= $lop->id ?>, 5)"></i>
                                <?php
                                endif;
                                if ($lop->status < 9 && $lop->status != 1): ?>
                                    <i class="fa fa-trash text-danger pointer"
                                       onclick="changeLopStatus(<?= $lop->id ?>, 1)"></i>
                                <?php
                                endif;
                                ?>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="12" class="navbar-color text-center font-size-12">
                            <?= $_SESSION['text']['i_keineDaten'] ?>
                        </td>
                    </tr>
                <?php
                endif;
                ?>
                </tbody>
            </table>

        </div><!-- px-4 -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/action.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        heightHeader();
    });
</script>
</body>
</html>