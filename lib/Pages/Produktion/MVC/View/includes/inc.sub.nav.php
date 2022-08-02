<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$id = (isset($_GET['id'])) ? 1 : 0;
?>
<nav class="navbar navbar-expand-xxl navbar-light border__bottom--solid-gray_25 p-0 w-100 z10 bg__white px-4" id="sub_nav" data-spy="affix" data-offset-top="183">
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
                echo "<a class=\"nav-link pe-3\" aria-current=\"page\" href=\"/produktion\">" . $_SESSION['text']['h_startseite'] . "</a>\n";
                echo "</li>\n";
                echo "<li class='nav-item dropdown border__right--dotted-gray'>
                      <a class='nav-link dropdown-toggle px-3' href='#' id='verwaltung' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>". $_SESSION['text']['h_verwaltung'] ."</a>
                      <ul class='dropdown-menu font-size-12' aria-labelledby='verwaltung'>";
                foreach (AdministrationDatabase::getAllPages(3, 1) as $page) {
                    # Externer Link
                    $ext = ($page->extern == 1) ? ' target="_blank"' : '';
                    echo "<a class=\"dropdown-item\" $ext href=\"$page->link\">" . $_SESSION['text']['' . $page->i18n . ''] . "</a>";
                }
                echo "</ul>\n";
                # Jahr
                if($dspKalender === true){
                    echo "<li class='nav-item dropdown border__right--dotted-gray'>
                      <a class='nav-link dropdown-toggle px-3' href='#' id='jahre' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>". $_SESSION['text']['h_jahr'] ."</a>
                      <ul class='dropdown-menu font-size-12' aria-labelledby='jahre'>";
                    for($i = $_SESSION['parameter']['start']; $i <= DATE('Y'); $i++){
                        echo "<a class=\"dropdown-item\" $ext href=\"?jahr=$i\">$i</a>";
                    }
                }
                echo "</ul>\n";
                # Monat
                $dspMonat = ($_SESSION['parameter']['jahr'] == DATE('Y')) ? DATE('n') : 12;
                if($dspKalender === true){
                    echo "<li class='nav-item dropdown border__right--dotted-gray'>
                      <a class='nav-link dropdown-toggle px-3' href='#' id='monate' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>". $_SESSION['text']['h_monat'] ."</a>
                      <ul class='dropdown-menu font-size-12' aria-labelledby='monate'>";
                    for($m = 1; $m <= $dspMonat; $m++){
                        $linkMonat = str_pad($m, 2, "0", STR_PAD_LEFT);
                        $s = $m-1;
                        echo "<a class=\"dropdown-item\" $ext href=\"?monat=$linkMonat\">". $_SESSION['text'][$_SESSION['i18n']['monate'][$s]] . "</a>";
                    }
                }
                echo "</ul>\n";
                if($dspTag === true){
                    $tage = cal_days_in_month(CAL_GREGORIAN, $_SESSION['wrk']['monat'], $_SESSION['wrk']['jahr']);
                    if($_SESSION['wrk']['monat'] == $_SESSION['parameter']['monat'] && $_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr']):
                        $tage = $_SESSION['parameter']['tag'];
                    endif;
                    for($d = 1; $d <= $tage; $d++):
                        ($d == $_SESSION['wrk']['tag']) ? $classTag = 'active' : $classTag = '';
                        echo '<li class="nav-item border__right--dotted-gray"><a href="?tag='.$d.'" class="nav-link '.$classTag.' px-3">'.$d.'</a></li>';
                    endfor;
                }
                echo "</div>\n"; # collapse
                ?>
            </ul>
        </div><!-- navbar-coolapse -->
    </div><!-- container-fluid -->
</nav>