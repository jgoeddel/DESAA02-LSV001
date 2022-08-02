<?php
/** (c) Joachim Göddel . RLMS */

# Status
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$ex = ChangeManagementDatabase::getCMParts($_POST['bid'],'evaluation',1,1);
$tx = ChangeManagementDatabase::getCMParts($_POST['bid'],'tracking',1,2);
$g = $ex[1] + $tx[1];
$h = $ex[2] + $tx[2];
$i = $ex[3] + $tx[3];

if($_POST['status'] == 'over'){ ChangeManagementDatabase::dspCMOverStatus($g,$h,$i); }
if($_POST['status'] == 'study'){ ChangeManagementDatabase::dspCMOverStatus($ex[1],$ex[2],$ex[3]); }
if($_POST['status'] == 'introduce'){ ChangeManagementDatabase::dspCMOverStatus($tx[1],$tx[2],$tx[3]); }