<?php
/** (c) Joachim Göddel . RLMS */
# Klassen
use App\Functions\Functions;

# Reload
$reload = '';
if(isset($_GET['l']) && $_GET['l'] == 'o'): $reload = 'yes'; endif;
if(isset($_SESSION['rechte'])): $reload = 'start'; endif;
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
</head>
<body class="d-flex flex-column h-100 index" id="body">
<main class="w-100">
    <div class="fixed-top z3" id="fixTop">
        <?php
        # Basis Header einbinden
        Functions::getHeaderBasePage();
        ?>
    </div>
    <div class="container-fluid" id="parallax">
        <div class="white-box-left">
            <h1 class="oswald hero">
                <small class="font-size-16 font-weight-300">INTRANET &bull; CNSHY01</small><br><?= $_SESSION['text']['h_tech_aenderung'] ?>
            </h1>
        </div>
    </div>

    <div class="container-fluid bg__white--85 p-5" id="mainContainer">
        <h2 class="oswald"><?= $_SESSION['text']['h_offene_anforderungen'] ?></h2>
        <p><?= $_SESSION['text']['t_offene_anforderungen'] ?></p>
    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
    <?php if($reload === 'start'): ?>
    $.redirect('/ChangeManagement');
    <?php endif; ?>
    <?php if($reload === 'yes'): ?>
    $.redirect('/');
    <?php endif; ?>
</script>
</body>
</html>
