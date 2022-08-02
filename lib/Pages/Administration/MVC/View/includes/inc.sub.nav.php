<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$id = (isset($_GET['id'])) ? 1 : 0;
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
                <?php
                echo "<div class=\"collapse navbar-collapse\" id=\"navbarABC\">\n";
                echo "<ul class=\"navbar-nav me-auto w-100\">\n";
                echo "<li class=\"nav-item border__right--dotted-gray\">\n";
                echo "<a class=\"nav-link pe-3\" aria-current=\"page\" href=\"/administration\">" . $_SESSION['text']['h_startseite'] . "</a>\n";
                echo "</li>\n";
                foreach(AdministrationDatabase::getAllSubPages(1) AS $sub) {
                    if (Functions::checkBerechtigung($sub->id) > 0) {
                        if ($_SESSION['seite']['id'] === 23 && $_SESSION['seite']['name'] === 'mitarbeiter' && $sub->id == 23) {
                            if (Functions::checkBerechtigung(23) > 0) {
                                echo "<li class=\"nav-item border__right--dotted-gray\" onclick=\"filterListAll()\">";
                                echo "<a class=\"nav-link px-3\" aria-current=\"page\" href=\"javascript:;\">" . $_SESSION['text']['b_alle'] . "</a>";
                                echo "</li>";
                                for ($a = 0; $a <= 28; $a++) {
                                    if (AdministrationDatabase::checkLetter($_SESSION['parameter']['abc'][$a]) > 0) {
                                        echo "<li class=\"nav-item border__right--dotted-gray\" onclick=\"filterList('{$_SESSION['parameter']['abc'][$a]}');\">";
                                        echo "<a class=\"nav-link px-2\" aria-current=\"page\" href=\"javascript:;\">{$_SESSION['parameter']['abc'][$a]}</a>";
                                        echo "</li>";
                                    }
                                }
                                echo "
                            <li class='nav-item dropdown border__right--dotted-gray'>
                                <a class='nav-link dropdown-toggle px-3' href='#' id='citycode' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Citycode</a>
                                    <ul class='dropdown-menu font-size-12' aria-labelledby='citycode'>";
                                foreach (AdministrationDatabase::getCCMA() as $c) {
                                    if (IndexDatabase::checkRechteCitycode($c->citycode) > 0) {
                                        echo "<li class=\"nav-item border__right--dotted-gray\" onclick=\"filterList('$c->citycode')\">";
                                        echo "<a class=\"nav-link px-3\" aria-current=\"page\" href=\"javascript:;\">$c->citycode</a>";
                                        echo "</li>";
                                    }
                                }
                                echo "</ul>
                            </li>";
                            } else {
                                echo "<li class=\"nav-item border__right--dotted-gray\">";
                                echo "<a class=\"nav-link px-3\" aria-current=\"page\" href=\"/administration/mitarbeiter\">" . $_SESSION['text']['h_mitarbeiter'] . "</a>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li class=\"nav-item border__right--dotted-gray\">";
                            echo "<a class=\"nav-link px-3\" aria-current=\"page\" href=\"$sub->link\">" . $_SESSION['text'][''.$sub->i18n.''] . "</a>";
                            echo "</li>";
                        }
                    }
                }
                echo "</ul>\n";
                echo "</div>\n"; # collapse
                ?>
            </ul>
        </div><!-- navbar-coolapse -->
    </div><!-- container-fluid -->
</nav>