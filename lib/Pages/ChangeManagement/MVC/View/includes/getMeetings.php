<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$anzMeeting = ChangeManagementDatabase::countCMElements($id, 'meeting');
if ($anzMeeting > 0):
    foreach (ChangeManagementDatabase::getMeetingsGrp($id) as $mt):
        echo '<h4 class="font-size-20 font-weight-300 pb-3 mb-3 border__bottom--dotted-gray oswald text-primary">' . $mt->tag . '</h4>';
        if ($mt->datum === $_SESSION['parameter']['heuteSQL']):
            echo "<div id='eintrag{$mt->id}'>";
            echo "<div class='m-0 row pb-3 mb-3 border__bottom--dotted-gray' id='ma{$mt->id}'>";
            $x = 0;
            foreach (IndexDatabase::selectMaCC($row->location) as $u):
                $border = ($x < 3) ? 'border__right--dotted-gray' : '';
                $vorname = substr($u->vorname, 0, 1);
                if (empty(ChangeManagementDatabase::checkMaMeeting($mt->id, $u->id))):
                    echo "<span class='col-3 mb-2 $border px-3 font-size-12 pointer' onclick='addUserMeeting($mt->id, $id, $u->id);'>";
                    echo "<i class='fa fa-plus-square text-success me-2'></i>";
                    echo "$vorname. $u->name";
                    echo "</span>";
                endif;
                $x++;
                if ($x == 4) $x = 0;
            endforeach;
            echo "</div>";
            echo "</div>";
        endif;
        # Teilnehmer
        echo '<div class="font-size-12 pb-3 mb-3 border__bottom--dotted-gray">';
        echo $_SESSION['text']['h_teilnehmer'] .': ';
        $t = ChangeManagementDatabase::getMaMeeting($mt->id);
        $tnr = '';
        if(!empty($t)):
            foreach($t as $tlnr):
                $u = IndexDatabase::getUserInfo($tlnr->uid);
                $tnr .= ($mt->datum === $_SESSION['parameter']['heuteSQL']) ?  "<i class='fa fa-minus-circle text-danger me-1 ms-3 pointer' onclick='addUserMeeting($mt->id, $id, $u->id)'></i>$u->vorname $u->name" : "$u->vorname $u->name &bull; ";
            endforeach;
            if($mt->datum != $_SESSION['parameter']['heuteSQL']): $tnr = substr($tnr, 0,-8); endif;
            echo $tnr;
        endif;
        echo '</div>';
        # Ausgabe der Einträge
        $i = 1;
        foreach (ChangeManagementDatabase::getMeetings($id, $mt->datum) as $ent):
            echo '<div class="border__bottom--dotted-gray pb-3 mb-3 kom font-size-12">';
            echo '<div class="row m-0 p-0">';
            echo '<div class="col-1 text-end">';
            echo $i . ".";
            echo '</div>';
            echo '<div class="col-11">';
            echo '<div class="ps-1">';
            echo $ent->eintrag;
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $i++;
        endforeach;
    endforeach;
else:
    Functions::alert($_SESSION['text']['i_keinMeeting']);
endif;