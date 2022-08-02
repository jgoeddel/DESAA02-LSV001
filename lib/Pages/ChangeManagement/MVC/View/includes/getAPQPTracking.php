<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\ChangeManagement\ChangeManagementDatabase;
$apqp = ChangeManagementDatabase::getOneApqpBereich($apqpid,$bid, $part);
ChangeManagementDatabase::showAPQPTracking($bid, $part, $apqp, $loc);