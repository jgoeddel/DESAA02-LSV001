<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$heute = DATE('Y-m-d');
?>
<nav class="navbar navbar-expand-xxl navbar-light border__bottom--solid-gray_25 p-0 w-100 z10 bg__white px-4"
     data-spy="affix"
     data-offset-top="192" id="sub_nav">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#sub_nav_content" aria-controls="sub_nav_content"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse font-size-12 z9" id="sub_nav_content">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 filter-button-group">
                <li class="nav-item border__right--dotted-gray">
                    <a class="nav-link <?= $n_rotationsplan ?>" href="/changeManagement"><i class="fa fa-home"></i> <span
                                class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item border__right--dotted-gray">
                    <a class="nav-link px-3" href="/changeManagement/neu">Neue Anfrage</a>
                </li>
                <?php
                if ($_SESSION['seite']['name'] == 'details') {
                    # Datensatz abrufen
                    $row = ChangeManagementDatabase::getElement($id);
                    # Parameter abrufen
                    $anzLog = ChangeManagementDatabase::countCMElements($id, 'base2log');
                    $anzFiles = ChangeManagementDatabase::countCMElements($id, 'base2files');
                    $anzLop = ChangeManagementDatabase::countCMElements($id, 'base2lop');
                    $anzMeeting = ChangeManagementDatabase::countCMElements($id, 'meeting');
                    $anzPartNo = ChangeManagementDatabase::countPartNo($id);
                    $pne = ChangeManagementDatabase::checkPart($id,'partno');
                    if (empty($anz_meeting)) $anz_meeting = 0;
                    # Zurück zur Detailansicht
                    echo "<li class=\"nav-item\"><a href=\"/changeManagement/details?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\"><i class=\"fa fa-caret-right px-3\"></i><b>$row->nr</b></a></li>\n";
                    if($pne == 1):
                        # Part Numbers
                        echo "<li class=\"nav-item\"><a href=\"/changeManagement/partno?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['h_partNo'] . " IN / OUT ($anzPartNo)</a></li>\n";
                    endif;
                    # Nachrichten
                    if ($row->status > 6 && $anzLog === 0): else:
                        echo "<li class=\"nav-item\"><a href=\"/changeManagement/nachrichten?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['h_logbuch'] . " ($anzLog)</a></li>\n";
                    endif;
                    # Dateien
                    if ($row->status > 6 && $anzFiles === 0): else:
                        echo "<li class=\"nav-item\"><a href=\"/changeManagement/dateien?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['n_dateien'] . " ($anzFiles)</a></li>\n";
                    endif;
                    # Maßnahmenplan
                    if ($row->status > 6 && $anzLop === 0): else:
                        echo "<li class=\"nav-item\"><a href=\"/changeManagement/lop?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['n_lop'] . " ($anzLop)</a></li>\n";
                    endif;
                    # Meetings
                    if ($row->status > 6 && $anzMeeting === 0): else:
                        echo "<li class=\"nav-item\"><a href=\"/changeManagement/meeting?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['n_meeting'] . " ($anzMeeting)</a></li>\n";
                    endif;
                    # Aktionen
                    echo "<li class=\"nav-item\"><a href=\"/changeManagement/aktionen?id=" . $_SESSION['wrk']['id'] . "&amp;loc=" . $_SESSION['wrk']['loc'] . "\" class=\"nav-link\">" . $_SESSION['text']['n_aktionen'] . "</a></li>\n";

                }
                if ($_SESSION['seite']['name'] != 'details' && $_SESSION['seite']['name'] != 'neu') {
                    foreach(IndexDatabase::getCMCitycode() AS $cc){
                        if(IndexDatabase::checkRechteCitycode($cc->citycode) > 0) {
                        ?>
                        <li class="nav-item border__right--dotted-gray" onclick="filterList('<?= $cc->citycode ?>')">
                            <a class="nav-link px-3" href="#"><?= $cc->citycode ?></a>
                        </li>
                    <?php
                        }
                    }
                }
                ?>
            </ul>
        </div><!-- navbar-coolapse -->
    </div><!-- container-fluid -->
</nav>