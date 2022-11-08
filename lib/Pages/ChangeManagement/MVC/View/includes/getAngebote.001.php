<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$edit = 0;
$seitelesen = 0;

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(36);

# Parameter setzen
$seiteadmin = $pb[2];

$a = ChangeManagementDatabase::checkMaAngebot($_SESSION['user']['id'], $id);
if ($a === true && $a->aread == 1) $seitelesen = 1;
if ($seiteadmin == 1) $edit = 1;
if ($a === true && $a->write == 1): $seiteschreiben = 1; $edit = 1; endif;

if ($edit == 1 || $seitelesen == 1):
    if (count($dateien) > 0):
        foreach ($dateien as $fl):
            ?>
            <div class="border__solid--gray_25 border__radius--5 p-2 linear__top--gray mb-2">
                <div class="row p-0 m-0">
                    <div class="col-1 text-center pointer"
                         onclick="window.open('<?= Functions::getBaseURL() ?>lib/Pages/ChangeManagement/MVC/View/files/<?= $fl->datei ?>');">
                        <div class="p-1"><?php Functions::dspFileType($fl->typ); ?></div>
                    </div>
                    <div class="col-11">
                        <div class="px-2">
                            <p class="font-size-14 font-weight-300 oswald p-0 m-0"><?= $fl->datei ?></p>
                            <p class="font-size-11 p-0 m-0">
                                <i class="fa fa-user text-muted pe-2"></i> <?= $fl->user ?>
                                <i class="fa fa-calendar text-muted px-2"></i> <?= $fl->tag ?>
                                <i class="fa fa-clock text-muted px-2"></i> <?= $fl->zeit ?>
                                <?php if ($edit === 1): ?>
                                    <span class="float-end">
                                    <i class="fa fa-trash text-danger ps-3 pointer"
                                       onclick="deleteFile(<?= $fl->id ?>, <?= $id ?>,'<?= $fl->datei ?>');"></i>
                                </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        endforeach;
    else:
        Functions::alert($_SESSION['text']['t_keineRechteAngebot']);
    endif;
else:
    Functions::alert($_SESSION['text']['i_keineAngebote']);
endif;