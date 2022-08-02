<?php
# Klassen
use App\Functions\Functions;
$x = rand(1,12);

?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <head>
        <?php
        # Basis-Head Elemente einbinden
        Functions::getHeadBase();
        ?>
        <title><?= $_SESSION['page']['version'] ?></title>
    </head>
</head>
<body class="d-flex flex-column h-100 errorpage<?= $x ?>" id="body">
<div class="fixed-top z3">

</div>
<main class="w-100" id="main">
    <div class="container-fluid pt-5">
        <div class="row mt-5">
            <div class="col-4 bg__white--75 mt-5 text-center">
                <div class="p-5">
                    <h1 class="oswald hero2">
                        <?= $_SESSION['text']['h_seiteNichtGefunden'] ?>
                    </h1>
                    <p class="font-size-24 oswald text-gray font-weight-300 py-4"><?= $_SESSION['text']['e_seiteNichtGefunden'] ?></p>
                    <button class="btn btn-primary btn-lg oswald font-size-30 font-weight-300 text-uppercase"
                            onclick="top.location.href='<?= $_SESSION['page']['url'] ?>'"><?= $_SESSION['text']['b_zurStartseite'] ?></button>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/js/base.js"></script>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript">
    heightMainContainer();
</script>
</body>
</html>