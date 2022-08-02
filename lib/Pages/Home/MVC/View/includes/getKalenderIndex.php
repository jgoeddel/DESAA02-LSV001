<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\Kalender\KalenderDatabase;
use App\Pages\Kundenaenderungen\KundenaenderungenDatabase;
use App\Pages\Logbuch\LogbuchDatabase;
use App\Pages\Produktion\ProduktionDatabase;
use App\Pages\Servicedesk\ServicedeskDatabase;

?>
<h3 class="text-center font-weight-300 py-3 m-0 border__bottom--dotted-gray">
                <span class="float-start pointer" id="back" onclick="getKalenderIndex(0);">
                    <i class="fa fa-caret-left ms-3"></i>
                </span>
    <?= $_SESSION['text']['h_dieseWoche'] ?> <?= $_SESSION['lang'] ?>
    <span class="float-end pointer" id="weiter" onclick="getKalenderIndex(1);">
                <i class="fa fa-caret-right me-3"></i>
            </span>
</h3>
<div id="getKalender" class="row py-2 m-0 border__bottom--dotted-gray">
    <?php
    // Sontag letzter Woche defineren
    $date = (!isset($_SESSION['datum']['StartKalender'])) ? new DateTime('last sunday') : new DateTime($_SESSION['datum']['StartKalender']);
    # Woche zurück
    if (isset($_POST['k']) && $_POST['k'] == 0) {
        $date = $date->modify('-1 week');
        $_SESSION['datum']['StartKalender'] = $date->format('Y-m-d');
    }
    # Woche vor
    if (isset($_POST['k']) && $_POST['k'] == 1) {
        $date = $date->modify('+1 week');
        $_SESSION['datum']['StartKalender'] = $date->format('Y-m-d');
    }
    # Interval defineren
    $interval = DateInterval::createFromDateString('+1 day');
    # Die Periode erstellen
    $period = new DatePeriod($date, $interval, 7, DatePeriod::EXCLUDE_START_DATE);
    setlocale(LC_TIME, 'deu_deu');
    # Ausgabe
    $i = 0;
    foreach ($period as $dt):
        if ($i == 7) $i = 0;
        $bg[$i] = ($i == 6 || $i == 5) ? 'bg-light-lines' : '';
        $brdr = 'border__right--dotted-gray';
        if ($i == 6) $brdr = '';
        # Datum für die Datenbankabfrage
        $sdate = $dt->format("Y-m-d");
        # Vorgabe Tag
        $v[$i] = Functions::germanNumberNoDez(ProduktionDatabase::getVorgabeTag($sdate));
        ?>
        <div class="col-6 col-lg <?= $brdr ?> <?= $bg[$i] ?>">
            <div class="px-2">
                <h3 class="oswald font-weight-300 font-size-18 p-0 m-0">
                    <?= $_SESSION['text']['' . $_SESSION['i18n']['tage'][$i] . ''] ?>
                    <?php if ($v[$i] > 0): ?>
                        <span class="float-end">
                            <span class="badge badge-primary oswald font-weight-400"><?= $v[$i] ?></span>
                        </span>
                    <?php endif; ?>
                </h3>
                <p class="m-0 p-0 pb-2 mb-2 font-size-12 italic border__bottom--dotted-gray_50 oswald text-muted">
                    <?= $dt->format("d.m.Y") ?>
                </p>
                <div class="font-size-12">
                    <?php
                    # Kalendereinträge
                    $kld[$i] = KalenderDatabase::getKalenderEintrag($sdate);
                    if (!empty($kld[$i])):
                        foreach ($kld[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1\">";
                            echo "<div class=\"col-2\">";
                            echo "<span class=\"badge badge-info\">K</span>";
                            echo "</div>";
                            echo "<div class=\"col-10 pt-1\">";
                            echo "$row->event";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Job 1
                    $ka[$i] = KundenaenderungenDatabase::getKalenderEintrag($sdate);
                    if (!empty($ka[$i])):
                        foreach ($ka[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1\">";
                            echo "<div class=\"col-2\">";
                            echo "<span class=\"badge badge-primary\">J</span>";
                            echo "</div>";
                            echo "<div class=\"col-10 pt-1\">";
                            echo "$row->teilname";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Logbucheinträge
                    $lb[$i] = LogbuchDatabase::getKalenderEintrag($sdate);
                    if (!empty($lb[$i])):
                        foreach ($lb[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1\">";
                            echo "<div class=\"col-2\">";
                            echo "<span class=\"badge badge-warning\">L</span>";
                            echo "</div>";
                            echo "<div class=\"col-10 pt-1\">";
                            echo "$row->titel";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Servicedesk
                    $sd[$i] = ServicedeskDatabase::getKalenderEintrag($sdate);
                    if (!empty($sd[$i])):
                        foreach ($sd[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1\">";
                            echo "<div class=\"col-2\">";
                            echo "<span class=\"badge badge-danger\">S</span>";
                            echo "</div>";
                            echo "<div class=\"col-10 pt-1\">";
                            echo "$row->titel";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Fehlermeldung
                    if (count($lb[$i]) == 0 && count($sd[$i]) == 0 && count($sd[$i]) == 0 && count($ka[$i]) == 0 && count($kld[$i]) == 0 && ($i < 6 && $i > 0) && $i < 5):
                        echo "<div class=\"alert alert-muted font-size-10\">";
                        echo $_SESSION['text']['e_kalender'];
                        echo "</div>";
                    endif;
                    ?>
                </div>
            </div><!-- px-2 -->
        </div><!-- col -->
        <?php
        $i++;
    endforeach;
    ?>
</div><!-- getKalender [AJAX] -->