<?php
/** (c) Joachim Göddel . RLMS */

$x = 0;
for ($c = 0; $c < count($y); $c++):
    foreach ($y[$c]->attributes() as $d => $e):
        $$d[$c] = $e;
    endforeach;
    $color[$c] = (!isset($rot[$c]) || $rot[$c] == '') ? 'text-muted' : 'text-black';
    if(!isset($rot[$c]) || $rot[$c] == '') $rot[$c] = '&nbsp;';
    if(!isset($vin[$c]) || $vin[$c] == '') $vin[$c] = '&nbsp;';
    $fehler[$c] = ($fault[$c] == 'false') ? 'text-success' : 'text-danger';
    if($fault[$c] == 'true') $rot[$c] = '&nbsp;';
    if($fault[$c] == 'true') $vin[$c] = '&nbsp;';
    ?>
    <div class="col">
        <div class="p-1">
            <div class="col border__solid--gray_50">
                <div class="row p-0 m-0">
                    <div class="col-9 border__right--solid-gray">
                        <p class="p-0 m-0 oswald font-size-16 text-center line-height-10 pt-2">&nbsp;<?= $rot[$c] ?>&nbsp;</p>
                        <h3 class="font-size-40 oswald text-center p-0 m-0 line-height-24 <?= $color[$c] ?>"><?= $name[$c] ?></h3>
                        <p class="p-0 m-0 oswald font-size-16 text-center pb-2 pt-1">&nbsp;<?= $vin[$c] ?>&nbsp;</p>
                    </div><!-- col-8 -->
                    <div class="col-3 bg__blue-gray--25">
                        <div class="ps-2">
                            <p class="font-size-12 oswald italic text-muted p-0 m-0 pt-2 pb-1 ">Betriebsart</p>
                            <p class="font-size-18 oswald p-0 m-0"><?= $mode[$c] ?></p>
                            <div class="row pt-2">
                                <div class="col-4 text-center">
                                    <i class="fa fa-circle <?= $fehler[$c] ?>"></i>
                                </div>
                                <div class="col-4 text-center">
                                    <i class="fa fa-circle text-white"></i>
                                </div>
                            </div><!-- row -->
                        </div><!-- ps-2 -->
                    </div><!-- col-4 -->
                </div><!-- row -->
            </div><!-- col -->
        </div><!-- p-1 -->
    </div><!-- col -->
    <?php
    $x++;
    if($x == 5): echo "<div class='w-100'></div>"; $x = 0; endif;
endfor;
?>
