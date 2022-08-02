<?php

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase; ?>
<div class="row address py-4" id="daddress">
    <div class="col-md-4 pt-3 text-center">
        <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $ersteller->bild ?>"
             class="img-fluid rund img-thumbnail pointer" <?php if($row->status < 6): ?>onclick="dspFormChange()"<?php endif; ?>>
    </div>
    <div class="col-md-8">
        <p class="pt-2">
            <span class="font-size-12"><?= $_SESSION['text']['h_verantwortlich'] ?>:</span><br>
            <span class="font-size-24 oswald text-uppercase">
                <?= $ersteller->vorname ?> <?= $ersteller->name ?>
            </span><br>
            <small class="text-muted italic">
                <?= Functions::dspAbteilung($ersteller->abteilung) ?>
            </small>
        </p>
        <?php IndexDatabase::dspLocationAddress($ersteller->citycode) ?>
        <div class="mb-2">
            <small class="text-muted italic font-size-12"><?= $_SESSION['text']['h_telefon'] ?></small><br>
            <a href="tel: <?php IndexDatabase::dspPhoneUser($ersteller->id, 'office') ?>"
               class="hiddenLink"><?php IndexDatabase::dspPhoneUser($ersteller->id, 'office') ?></a><br>
        </div>
        <div class="">
            <small class="text-muted italic font-size-12"><?= $_SESSION['text']['h_mail'] ?></small><br>
            <a href="mailto:<?= $ersteller->email ?>"
               class="hiddenLink"><?= $ersteller->email ?></a><br>
        </div>
    </div><!-- col-8 -->
</div>
<div class="address py-4 dspnone" id="eaddress">
    <div class="row">
        <div class="col-12 m-0 p-0">
            <h3 class="font-weight-300 pointer" onclick="dspFormChange()"><?= $_SESSION['text']['h_verantwortlichTausch'] ?></h3>
            <?php Functions::alert($_SESSION['text']['t_verantwortlichTausch']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-5 p-0 m-0">
            <div class="border__bottom--dotted-gray">
                <select name="mitarbeiter" class="invisible-formfield my-3" id="ma">
                    <option value=""><?= $_SESSION['text']['i_selectMitarbeiter'] ?></option>
                    <?php
                    foreach (IndexDatabase::selectMaWork($ersteller->id) as $select_usr):
                        ?>
                        <option value="<?= $select_usr->id ?>"><?= $select_usr->name ?>
                            , <?= $select_usr->vorname ?></option>
                    <?php
                    endforeach;
                    ?>
                </select>
            </div>
        </div>
        <div class="col-7 p-0 m-0">
            <?php if ($ersteller->id != $_SESSION['user']['id']): ?>
                <button class="btn btn-primary float-end mt-3"
                        onclick="setVerantwortung(<?= $_SESSION['user']['id'] ?>,<?= $id ?>);"><?= $_SESSION['text']['b_verantwortung'] ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>
