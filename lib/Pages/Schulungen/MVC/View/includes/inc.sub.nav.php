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
                echo "<a class=\"nav-link pe-3\" aria-current=\"page\" href=\"/servicedesk\">" . $_SESSION['text']['h_startseite'] . "</a>\n";
                echo "</li>\n";
                foreach (AdministrationDatabase::getAllSubPages(7) as $sub) {
                    echo "<li class=\"nav-item border__right--dotted-gray\">";
                    echo "<a class=\"nav-link px-3\" aria-current=\"page\" href=\"$sub->link\">" . $_SESSION['text']['' . $sub->i18n . ''] . "</a>";
                    echo "</li>";
                }
                echo "</ul>\n";
                echo "</div>\n"; # collapse
                ?>
            </ul>
        </div><!-- navbar-collapse -->
    </div><!-- container-fluid -->
</nav>