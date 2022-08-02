<?php
/** (c) Joachim Göddel . RLMS */
# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

# Parameter
require_once "includes/parameter.inc.php";
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
<body class="d-flex flex-column h-100" id="body">
    <div class="fixed-top z3">
        <?php
        # Basis Header einbinden
        Functions::getHeaderBasePage();
        Functions::dspNavigation($dspedit,"details",$_POST['id']);
        ?>
    </div>
    <main class="w-100 bg__white--95 flex-shrink-0" id="main">
        <div class="container-fluid">
            <div class="px-4 py-5"><!-- Inhalt -->

            </div>
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
        heightHeader();
    });
</script>
</body>
</html>