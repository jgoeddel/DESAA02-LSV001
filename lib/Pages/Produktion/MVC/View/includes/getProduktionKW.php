<div class="row">
    <?php
    /** (c) Joachim Göddel . RLMS */
    // Erster Tag im Monat (einfache Ausgabe)
    use App\Pages\Produktion\ProduktionDatabase;

    $begin = new DateTime('' . $_SESSION['wrk']['jahr'] . '-' . $_SESSION['wrk']['monat'] . '-01');
    $bgn = $begin->format('Y-m-d');
    // Letzter Tag im Monat
    $endd = new DateTime('' . $_SESSION['wrk']['jahr'] . '-' . $_SESSION['wrk']['monat'] . '-01');
    $ende = $endd->format('Y-m-t');
    $letzter = $endd->format('t');
    $end = new DateTime('' . $ende . '');
    $end = $end->modify('+1 day');
    // Kalenderwoche des ersten und letzen Tages im ausgewählten Monat
    $kw_begin = $begin->format("W");
    $kw_end = $end->format("W");
    // Interval
    $interval = new DateInterval('P1D');
    // Zeitraum der ausgegeben werden soll (ganzer Monat)
    $daterange = new DatePeriod($begin, $interval, $end);
    // Counter
    $a = 0;
    // Variablen für die Chartanzeige
    $cv = '';
    $cf = '';
    $cl = '';
    foreach ($daterange as $date):
        // Kelnderwoche des Datensatzes
        $kw = $date->format("W");
        // Wochentag des Datum
        $wochentag[$kw][$a] = $date->format("w");
        // Deutsches Datum
        $dtage[$kw][$a] = $date->format('d.m');
        // Datum für die Datenbankabfrage
        $sqlDate[$kw][$a] = $date->format('Y-m-d');
        $a++;
    endforeach;
    // Ergebnisse in KW Array schreiben
    $group = array();
    foreach ($sqlDate as $key => $value):
        $group[$key] = $value;
    endforeach;
    foreach ($group as $key => $kw):
        ($key != $kw_end) ? $border = 'border__right--dotted-gray_25' : $border = '';
        echo '<div class="col w-20 font-size-12 '.$border.'">';
        echo '<div class="p-2">';
        ($key != $kw_end) ? $border = 'border__right--dotted-gray_25' : $border = '';
        echo '<h5 class="text-primary font-weight-100 p-2">'.$_SESSION['text']['h_kw'].' '.$key.'</h5>'; ?>
        <table class="table table-sm table-bordered">
            <thead class="bg__blue-gray--12 text-gray">
            <tr>
                <th class="text-center"><?= $_SESSION['text']['h_tag'] ?></th>
                <th class="text-end"><?= $_SESSION['text']['h_vorgabe'] ?></th>
                <th class="text-end"><?= $_SESSION['text']['h_ergebnis'] ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($group[$key] as $b => $row):
                // Vorgabe für den jeweiligen Tag
                $v[$b] = ProduktionDatabase::getVorgabeTag($sqlDate[$key][$b]);
                // Ergebnis des Tages
                $fzg[$b] = ProduktionDatabase::getOneProductionDay($sqlDate[$key][$b]);
                ($wochentag[$key][$b] == 0 || $wochentag[$key][$b] == 6) ? $bg = 'bg-light-lines' : $bg = '';
                if($sqlDate[$key][$b] == DATE('Y-m-d')) $bg = 'bg__blue-gray--6 text-primary font-weight-600';
                ($v[$b] == 0 && $fzg[$b] == 0) ? $dsptr = 2 : $dsptr = 1;
                if(empty($v[$b])) $v[$b] = 0;
                if(empty($fzg[$b])) $fzg[$b] = 0;
                $cv.= str_replace(".","",$v[$b]).",";
                $cf.= str_replace(".","",$fzg[$b]).",";
                $cl.= ($b+1).",";
                if($dsptr == 1):
                    ?>
                    <tr class="<?= $bg ?>">
                        <td><?= $dtage[$key][$b] ?></td>
                        <td class="text-end"><?= $v[$b] ?></td>
                        <td class="text-end"><?= $fzg[$b] ?></td>
                        <td class="text-center">
                            <?php if(!empty($fzg[$b])):
                                echo ProduktionDatabase::getStatusIcon($v[$b],$fzg[$b]);
                            endif; ?>
                        </td>
                    </tr>
                <?php
                else: ?>
                    <tr class="<?= $bg ?>">
                        <td><?= $dtage[$key][$b] ?></td>
                        <td colspan="3" class="text-end text-gray"><?= $_SESSION['text']['t_keineDatenP'] ?></td>
                    </tr>
                <?php
                endif;
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
        echo '</div>';
        echo '</div>';
    endforeach;
    ?>
</div>
