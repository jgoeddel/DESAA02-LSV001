<?php
/** (c) Joachim Göddel . RLMS */
# Klassen
use App\Functions\Functions;

?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?> &bull; LOGIN</title>
</head>
<body class="d-flex flex-column h-100 login-index">
<main class="w-100">
    <div class="fixed-top bg__white z3">
        <?php
        # Basis Header einbinden
        Functions::getHeaderBase();
        ?>
    </div>

</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript">
    <?php Functions::dspParallaxImg(Functions::getBaseUrl() . 'skin/files/images/bg001.jpg'); ?>
</script>
</body>
</html>
