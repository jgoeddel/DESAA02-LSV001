<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Formular;

class Formular
{
    /** Input Felder (Text) ******************************************/
    public static function input($type, $name, $val, $aktion, $req, $placeholder="", $min="")
    {
        echo "<input type='$type' class='form-control' name='$name' id='$name' value='$val' min='$min' $aktion $req placeholder='$placeholder' autocomplete='off'>";
    }
    /** Inputfeld mit Label */
    public static function labelInput($i18n, $type, $name, $val, $aktion, $req, $placeholder="",$min="")
    {
        echo "<label class='form-label font-size-10 text-muted italic p-0 m-0'>$i18n ";
        if($req == 'required'){
            Formular::req();
        }
        echo "</label>";
        echo "<input type='$type' class='form-control' name='$name' id='$name' value='$val' min='$min' $aktion $req placeholder='$placeholder' autocomplete='off'>";
    }
    /** Inputfeld mit Label */
    public static function labelInvisibleInput($i18n, $type, $name, $val, $aktion, $req, $placeholder="",$min="",$class="")
    {
        echo "<label class='form-label font-size-10 text-muted italic p-0 m-0'>$i18n ";
        if($req == 'required'){
            Formular::req();
        }
        echo "</label>";
        echo "<input type='$type' class='invisible-formfield $class' name='$name' id='$name' value='$val' min='$min' $aktion $req placeholder='$placeholder' autocomplete='off'>";
    }

    /** Input Felder (Datalist) **************************************/
    public static function datalist($list, $name, $val, $aktion, $req, $placeholder="")
    {
        echo "<input type='search' class='form-control' name='$name' id='$name' value='$val' $aktion $req list='$list' placeholder='$placeholder'>";
    }

    /** Checkbox     ************************************************/
    public static function checkboxFormCheck($name, $id, $onchange, $i18n, $value, $req, $sts, $class="") # logbuch
    {
        echo "<input class='form-check-input $class' type='checkbox' value='$value' id='$id' name='$name' onchange='$onchange' $sts>";
        echo "<label class='form-check-label ml-3 pointer' for='$id'>";
        if($i18n) {
            echo $_SESSION['text']['' . $i18n . ''];
        }
        echo "</label>";
    }
    public static function checkboxFormCheckNoLabel($name, $id, $onchange, $i18n, $value, $req, $sts, $class="") # logbuch
    {
        echo "<input class='form-check-input $class' type='checkbox' value='$value' id='$id' name='$name' onchange='$onchange' $sts style='margin-left: 5px; margin-right: -10px;'>";
    }
    /** Checkbox mit Label */
    public static function labelCheckboxFormCheck($name, $id, $onchange, $i18n, $value, $req, $sts, $show = 0)
    {
        echo "<label class='form-label font-size-10 text-muted italic text-center p-0 m-0'>$i18n ";
        if($req == 'required'){
            Formular::req();
        }
        echo "</p>";
        echo "<input class='form-check-input font-size-16' type='checkbox' value='$value' id='$id' name='$name' onchange='$onchange' $sts>";
        echo "<label class='form-check-label ml-3 pointer' for='$id'>";
        if($show == 1) {
            echo $i18n;
        }
        echo "</label>";
    }

    /** Radio        *************************************************/
    public static function radioFormCheck($name, $id, $aktion, $i18n, $value, $req, $sts) # Logbuch
    {
        echo "<input class='form-check-input' type='radio' name='$name' id='$id' value='$value' onchange='$aktion' $sts>";
        echo "<label class='form-check-label ml-3' for='$id'>";
        echo $_SESSION['text']['' . $i18n . ''];
        echo "</label>";
    }

    /** Select       *************************************************/
    public static function selectArtEintrag($checked, $req) # Logbuch
    {
        // Selected Status
        $mpty = ($checked == '') ? "selected" : "";
        $val1 = ($checked == "1") ? "selected" : "";
        $val2 = ($checked == "2") ? "selected" : "";
        $val3 = ($checked == "3") ? "selected" : "";
        $val4 = ($checked == "4") ? "selected" : "";
        $val5 = ($checked == "5") ? "selected" : "";
        // Select Feld
        echo "<select name='icon' class='no-border mb-2 w-100' $req id='icon' onchange='getFrageLinestop(this.value)'>";
        # OPTION VALUE
        echo "<option value='' $mpty>";
        echo $_SESSION['text']['f_select_option'];
        echo "</option>";
        # OPTION VALUE 1
        echo "<option value='1' $val1>";
        echo $_SESSION['text']['h_information'];
        echo "</option>";
        # OPTION VALUE 2
        echo "<option value='2' $val2>";
        echo $_SESSION['text']['h_stoerung'];
        echo "</option>";
        # OPTION VALUE 3
        echo "<option value='3' $val3>";
        echo $_SESSION['text']['h_einsatz'];
        echo "</option>";
        # OPTION VALUE 4
        echo "<option value='4' $val4>";
        echo $_SESSION['text']['h_nacharbeit_intern'];
        echo "</option>";
        # OPTION VALUE 5
        echo "<option value='5' $val5>";
        echo $_SESSION['text']['h_nacharbeit_extern'];
        echo "</option>";
        echo "</select>";
    }

    public static function selectAbteilungen($req)
    {
        echo "<select name='abteilung' class='no-border mb-2 w-100' $req id='abteilung'>";
        # OPTION VALUE
        echo "<option value=''>";
        echo $_SESSION['text']['s_abteilung'];
        echo "</option>";
        foreach($_SESSION['seite']['abteilungen'] AS $abt){
            echo "<option value='$abt->id'>";
            echo $abt->abteilung;
            echo "</option>";
        }
        echo "</select>";
    }

    public static function selectSchulungsart($req) # Schulungen
    {
        echo "<select name='art' class='no-border mb-2 w-100' $req id='art'>";
        # OPTION VALUE
        echo "<option value=''>";
        echo $_SESSION['text']['f_select_option'];
        echo "</option>";
        foreach($_SESSION['seite']['schulungsart'] AS $art){
            echo "<option value='$art->id'>";
            echo $art->art;
            echo "</option>";
        }
        echo "</select>";
    }

    public static function selectSchichten($req)
    {
        echo "<select name='schicht' class='no-border mb-2 w-100' $req id='abteilung'>";
        # OPTION VALUE
        echo "<option value='0'>";
        echo $_SESSION['text']['t_alle'];
        echo "</option>";
        for($i = 1; $i <= $_SESSION['seite']['schichten']; $i++){
            echo "<option value='$i'>";
            echo $_SESSION['text']['t_schicht'] . " $i";
            echo "</option>";
        }
        echo "</select>";
    }

    public static function selectJaChecked($id, $status)
    {
        $ja = '';
        $nein = '';
        ($status == 0) ? $nein = 'selected' : $ja = 'selected';
        echo "<select name='$id' class='no-border mb-2 w-100' required id='$id'>";
        echo "<option value='0' $nein>" . $_SESSION['text']['nein'] . "</option>";
        echo "<option value='1' $ja>" . $_SESSION['text']['ja'] . "</option>";
        echo "</select>";

    }
    public static function selectJaNein($name,$required)
    {
        echo '<select name="'.$name.'" id="'.$name.'" class="invisible-formfield pt-1" required="'.$required.'">';
        echo '<option value="">'. $_SESSION['text']['i_selectOption'] .'</option>';
        echo '<option value="1">'. $_SESSION['text']['ja'] .'</option>';
        echo '<option value="0">'. $_SESSION['text']['nein'] .'</option>';
        echo '</select>';
    }

    /** Textarea     *************************************************/
    public static function txtarea($name, $id, $class, $req, $value = "", $placeholder = "")
    {
        echo "<textarea name='$name' class='$class' id='$id' $req placeholder='$placeholder'>$value</textarea >";
    }

    /** Submit */
    public static function submit($type, $val, $class)
    {
        echo "<input type='$type' class='$class oswald font-weight-300 text-uppercase' value='$val'>";
    }

    /** Datalist */
    public static function dlRaum($list)
    {
        echo "<datalist id='$list'>";
        foreach($_SESSION['seite']['schulungsraeume'] AS $raum){
            echo "<option value='$raum->raum'>";
        }
        echo "</datalist>";
    }
    public static function dlMitarbeiter($list)
    {
        echo "<datalist id='$list'>";
        foreach($_SESSION['seite']['mitarbeiter'] AS $ma){
            echo "<option value='$ma->vorname $ma->name'>";
        }
        echo "</datalist>";
    }

    /** Sonstiges */
    public static function req()
    {
        echo '<span class="text-warning">*</span>';
    }
}