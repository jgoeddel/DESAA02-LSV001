<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();
$heute = DATE('Y-m-d');
?>
<nav class="navbar navbar-expand-xxl navbar-light border__bottom--solid-gray_25 p-0 w-100 z10 bg__white"
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
                    <a class="nav-link <?= $n_rotationsplan ?>" href="/rotationsplan"><i class="fa fa-home"></i> <span
                                class="sr-only">(current)</span></a>
                </li>
                <?php
                foreach ($_SESSION['menu']['rotationsplan'] as $data):
                    # Dropdown
                    if ($data->sub == 1):
                        echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='verwaltung' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Verwaltung</a>
                                    <ul class='dropdown-menu' aria-labelledby='verwaltung'>";
                        foreach ($_SESSION['menu']['verwaltung'] as $vwl):
                            echo "<a class='dropdown-item' href='#' onclick='getVerwaltung(\"$vwl->datum\")'>$vwl->tag</a>";
                        endforeach;
                        echo "
                                    </ul>
                            </li>        
                        ";
                        echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='archiv' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Archiv</a>
                                <ul class='dropdown-menu' aria-labelledby='verwaltung'>    
                                    <li class='dropdown-item'>
                                        <small class='font-size-10 italic'>Bitte tragen Sie das gewünschte Datum ein</small><br>
                                        <input type='date' class='no-border w-100 font-size-12 my-2 py-2' name='archivDatum' id='archivDatum' max='$heute'><br>
                                        <button class='btn btn-primary btn-sm font-size-11 oswald' onclick='getArchiv()'>ANZEIGEN</button>
                                    </li>
                                </ul>
                            </li>
                        ";
                    else:
                        # Aktiver Link
                        $cls = ($subid == $data->id) ? 'active' : '';
                        echo "
                        <li class='nav-item border__right--dotted-gray'>
                            <a href='$data->link' class='nav-link $cls px-3'>" . $_SESSION['text']['' . $data->i18n . ''] . "</a>
                        </li>
                    ";
                    endif;
                endforeach;
                # Mitarbeiter
                if ($subid && $subid == 28): ?>
                    <li class="nav-item" onclick="filterListAll()">
                        <a href="#" class="nav-link px-3 border__right--dotted-gray">Alle</a>
                    </li>
                    <?php
                    for ($i = 0; $i < count($_SESSION['parameter']['abc']); $i++):
                        $anzahl[$i] = $db->countAnfangsBuchstabe($i);
                        if ($anzahl[$i] > 0):
                            echo '
                            <li class="nav-item" onclick="filterList(\'' . $_SESSION['parameter']['abc'][$i] . '\')">
                                <a href="#" class="nav-link px-3 border__right--dotted-gray">' . $_SESSION['parameter']['abc'][$i] . '</a>
                            </li>
                        ';
                        endif;
                    endfor;
                endif;
                # Stationen
                if ($subid && $subid == 29):
                    echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='stationen' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Stationen</a>
                                    <ul class='dropdown-menu font-size-12' aria-labelledby='stationen'>";
                    foreach ($db->getStationAbteilung() as $sn):
                        $eid = Functions::encrypt($sn->id);
                        ?>
                        <li class="nav-item">
                            <a href="#" onclick="top.location.href='/rotationsplan/stationDetails?id=<?= $eid ?>'"
                               class="dropdown-item px-3 border__right--dotted-gray">
                                <?= $sn->station ?>
                            </a>
                        </li>
                    <?php
                    endforeach;
                    echo "
                                    </ul>
                            </li>        
                        ";
                endif;
                # Wechsel Abteilung und Schicht
                if ($seiteadmin === 1):
                    echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='abteilung' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Abteilung</a>
                                    <ul class='dropdown-menu font-size-12' aria-labelledby='abteilung'>";
                    foreach (RotationsplanDatabase::getAbteilungenRotationsplan() as $abt):
                        echo "<a class='dropdown-item' href='#' onclick='setAbteilung(\"$abt->rotationsplan\")'>$abt->abteilung</a>";
                    endforeach;
                    echo "
                                    </ul>
                            </li>        
                        ";
                    echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='schicht' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Schicht</a>
                                    <ul class='dropdown-menu font-size-12' aria-labelledby='schicht'>";
                    echo "<a class='dropdown-item' href='#' onclick='setSchicht(1)'>Schicht 1</a>";
                    echo "<a class='dropdown-item' href='#' onclick='setSchicht(2)'>Schicht 2</a>";
                    echo "
                                    </ul>
                            </li>        
                        ";
                endif;
                ?>
            </ul>
        </div><!-- navbar-coolapse -->
    </div><!-- container-fluid -->
</nav>