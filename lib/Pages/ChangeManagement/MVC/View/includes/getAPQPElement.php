<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\ChangeManagement\ChangeManagementDatabase;
$apqp = ChangeManagementDatabase::getOneApqpBereich($apqpid,$bid, $part);
ChangeManagementDatabase::showAPQP($bid, $part, $apqp, $loc);