<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
# TODO: Die Suchergebnisse müssen noch nach den freigegebenen Standorten gefiltert werden.
?>
<div class="container-fluid border__left--dotted-gray border__right--dotted-gray border__bottom--dotted-gray bg__white p-0">
    <h3 class="font-weight-300 bg__blue-gray--12 px-5 py-3">Suchergebnis</h3>
    <div class="px-5">
        <div class="row">
            <div class="col-2 border__right--dotted-gray">
                <div class="pe-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_partDescription'] ?> (<span class="text-warning"><?= $t1 ?></span>)
                    </p>
                    <?php
                    if($t1 > 0):
                        foreach($pd as $row):
                            $id = Functions::encrypt($row->bid);
                            $nr = ChangeManagementDatabase::dspParameter('base','nr','id', $row->bid);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->bid);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->part_description);
                    ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                    <?php
                    endforeach;
                    else:
                    Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-2 border__right--dotted-gray">
                <div class="px-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_aenderungsbeschreibung'] ?> (<span class="text-warning"><?= $t2 ?></span>)
                    </p>
                    <?php
                    if($t2 > 0):
                        foreach($cd as $row):
                            $id = Functions::encrypt($row->bid);
                            $nr = ChangeManagementDatabase::dspParameter('base','nr','id', $row->bid);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->bid);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->change_description);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                        <?php
                        endforeach;
                    else:
                        Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-2 border__right--dotted-gray">
                <div class="px-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_ersteller'] ?> (<span class="text-warning"><?= $t3 ?></span>)
                    </p>
                    <?php
                    if($t3 > 0):
                        foreach($er as $row):
                            $id = Functions::encrypt($row->id);
                            $nr = ChangeManagementDatabase::dspParameter('base','nr','id', $row->id);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->id);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->name);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                        <?php
                        endforeach;
                    else:
                        Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-2 border__right--dotted-gray">
                <div class="ps-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_nummer'] ?> (<span class="text-warning"><?= $t4 ?></span>)
                    </p>
                    <?php
                    if($t4 > 0):
                        foreach($nr as $row):
                            $id = Functions::encrypt($row->id);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->nr);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->id);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $row->nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                        <?php
                        endforeach;
                    else:
                        Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-2 border__right--dotted-gray">
                <div class="ps-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_partIn'] ?> (<span class="text-warning"><?= $t4 ?></span>)
                    </p>
                    <?php
                    if($t5 > 0):
                        foreach($alt as $row):
                            $id = Functions::encrypt($row->bid);
                            $nr = ChangeManagementDatabase::dspParameter('base','nr','id', $row->bid);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->bid);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->alt);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                        <?php
                        endforeach;
                    else:
                        Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-2">
                <div class="ps-3">
                    <p class="border__bottom--dotted-gray pb-1 mb-1 italic">
                        <?= $_SESSION['text']['h_partOut'] ?> (<span class="text-warning"><?= $t4 ?></span>)
                    </p>
                    <?php
                    if($t6 > 0):
                        foreach($neu as $row):
                            $id = Functions::encrypt($row->bid);
                            $nr = ChangeManagementDatabase::dspParameter('base','nr','id', $row->bid);
                            $cc = ChangeManagementDatabase::dspParameter('base','location','id', $row->bid);
                            $name = preg_replace('/('.$parameter.')/i', "<strong class='text-warning'>$1</strong>", $row->neu);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 hover pointer" onclick="top.location.href='/changeManagement/details?id=<?= $id ?>&loc=<?= $cc ?>'"><b><?= $nr ?></b><br><span class="font-size-11"><?= $name ?></span></div>
                        <?php
                        endforeach;
                    else:
                        Functions::alert($_SESSION['text']['keinSuchergebnis']);
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
