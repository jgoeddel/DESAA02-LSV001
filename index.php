<?php
/** (c) Joachim Göddel . RLMS */
use App\App\Container;

require_once "init.php";

# Container
$Container = new Container;
$router = $Container->build("router");
$pfad = $_SESSION['page']['pfad'];
#$request = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$request = $_SERVER['PATH_INFO'] ?? "/";

# Startseite  -> -> -> ->
if ($request == "/") {
    $router->add("indexController", "index"); # Startseite aufrufen | Wenn nicht angemeldet zurück zur Login Seite
    $router->add("indexController", "i18n"); # Übersetzungen
# Inhalte Startseite
} elseif ($request == "/ajaxGetProduktionIndex") {
    $router->add("indexController", "getProduktionIndex"); # Produktionszahlen (Aktuell)
} elseif ($request == "/ajaxGetKalenderIndex") {
    $router->add("indexController", "getKalenderIndex"); # Kalendereinträge
} elseif ($request == "/ajaxGetProduktionJahr") {
    $router->add("indexController", "getProduktionJahr"); # Produktionszahlen (Jahr)
} elseif ($request == "/ajaxGetAushangIndex") {
    $router->add("indexController", "getAushangIndex"); # Produktionszahlen (Jahr)
# Login
} elseif ($request == "/goLogin") {
    $router->add("loginController", "goLogin"); # Anmelden
# Übersetzungen
} elseif ($request == "/i18n") {
    $router->add("indexController", "i18n"); # Sprache umstellen
# Scanner
} elseif ($request == "/scan") {
    $router->add("scanController", "index"); # Administration: Startseite
# Logout
} elseif ($request == "/logout") {
    $router->add("indexController", "logout"); # Administration: Startseite

# Rotationsplan
} elseif ($request == "/rotationsplan") {
    $router->add("rotationsplanController", "index");
} elseif ($request == "/rotationsplan/ajaxPlanExists") { # Plan existiert ?
    $router->add("rotationsplanController", "planExists");
} elseif ($request == "/rotationsplan/ajaxTableAnwesenheit") { # Tabelle Anwesenheit
    $router->add("rotationsplanController", "tableAnwesenheit");
} elseif ($request == "/rotationsplan/setAnwesenheit") { # Anzeige Anwesenheit speichern
    $router->add("rotationsplanController", "setAnwesenheit");
} elseif ($request == "/rotationsplan/verwaltung") { # Verwaltung Rotationsplan
    $router->add("rotationsplanController", "verwaltung");
}  elseif ($request == "/rotationsplan/archiv") { # Archiv Rotationsplan
    $router->add("rotationsplanController", "archiv");
} elseif ($request == "/rotationsplan/ajaxTablePersonal") { # Tabelle Personal
    $router->add("rotationsplanController", "tablePersonal");
}  elseif ($request == "/rotationsplan/ajaxTablePersonalArchiv") { # Tabelle Personal Archiv
    $router->add("rotationsplanController", "tablePersonalArchiv");
} elseif ($request == "/rotationsplan/ajaxTableZeitschiene") { # Tabelle Zeitschienen
    $router->add("rotationsplanController", "tableZeitschiene");
}  elseif ($request == "/rotationsplan/ajaxTableZeitschieneArchiv") { # Tabelle Zeitschienen Archiv
    $router->add("rotationsplanController", "tableZeitschieneArchiv");
} elseif ($request == "/rotationsplan/ajaxDeleteMitarbeiterPlan") { # Aktion
    $router->add("rotationsplanController", "deleteMitarbeiterPlan");
} elseif ($request == "/rotationsplan/ajaxDeleteMitarbeiterAnwesend") { # Aktion
    $router->add("rotationsplanController", "deleteMitarbeiterAnwesend");
}  elseif ($request == "/rotationsplan/ajaxSetMitarbeiterAnwesend") { # Aktion
    $router->add("rotationsplanController", "setMitarbeiterAnwesend");
} elseif ($request == "/rotationsplan/ajaxSetMitarbeiterStation") { # Mitarbeiter auf Station setzen
    $router->add("rotationsplanController", "setMitarbeiterStation");
} elseif ($request == "/rotationsplan/mitarbeiter") { # Mitarbeiter
    $router->add("rotationsplanController", "mitarbeiter");
} elseif ($request == "/rotationsplan/mitarbeiterDetails") { # Mitarbeiter
    $router->add("rotationsplanController", "mitarbeiterDetails");
} elseif ($request == "/rotationsplan/ajaxSetMitarbeiterHandicap") { # Mitarbeiter
    $router->add("rotationsplanController", "setMitarbeiterHandicap");
} elseif ($request == "/rotationsplan/neuerMitarbeiter") { # Mitarbeiter
    $router->add("rotationsplanController", "neuerMitarbeiter");
} elseif ($request == "/rotationsplan/ajaxSetMitarbeiterAbwesend") { # Mitarbeiter
    $router->add("rotationsplanController", "setMitarbeiterAbwesend");
} elseif ($request == "/rotationsplan/ajaxDeleteAbwesend") { # Mitarbeiter
    $router->add("rotationsplanController", "deleteAbwesend");
} elseif ($request == "/rotationsplan/cronjob") { # Mitarbeiter
    $router->add("rotationsplanController", "cronjob");
} elseif ($request == "/rotationsplan/ajaxDeleteQualiMa") { # Mitarbeiter
    $router->add("rotationsplanController", "deleteQualiMa");
} elseif ($request == "/rotationsplan/ajaxGetFormularQualiMa") { # Mitarbeiter
    $router->add("rotationsplanController", "getFormularQualiMa");
} elseif ($request == "/rotationsplan/ajaxSetTrainingMa") { # Mitarbeiter
    $router->add("rotationsplanController", "setTrainingMa");
} elseif ($request == "/rotationsplan/ajaxSetQualiMa") { # Mitarbeiter
    $router->add("rotationsplanController", "setQualiMa");
}  elseif ($request == "/rotationsplan/ajaxSetQualiStation") { # Mitarbeiter
    $router->add("rotationsplanController", "setQualiStation");
} elseif ($request == "/rotationsplan/ajaxDeleteMa") { # Mitarbeiter
    $router->add("rotationsplanController", "deleteMa");
} elseif ($request == "/rotationsplan/ajaxSetMitarbeiterPassword") { # Mitarbeiter
    $router->add("rotationsplanController", "setMitarbeiterPassword");
} elseif ($request == "/rotationsplan/stationen") { # Mitarbeiter
    $router->add("rotationsplanController", "stationen");
} elseif ($request == "/rotationsplan/stationDetails") { # Mitarbeiter
    $router->add("rotationsplanController", "stationDetails");
} elseif ($request == "/rotationsplan/ajaxChangeStation") { # Mitarbeiter
    $router->add("rotationsplanController", "changeStation");
} elseif ($request == "/rotationsplan/neueStation") { # Mitarbeiter
    $router->add("rotationsplanController", "neueStation");
} elseif ($request == "/rotationsplan/auswertung") { # Mitarbeiter
    $router->add("rotationsplanController", "auswertung");
} elseif ($request == "/rotationsplan/vergleich") { # Mitarbeiter
    $router->add("rotationsplanController", "vergleich");
} elseif ($request == "/rotationsplan/mitarbeiter/vergleich") { # Mitarbeiter
    $router->add("rotationsplanController", "vergleichMitarbeiter");
} elseif ($request == "/rotationsplan/mitarbeiter/getVergleich") { # Mitarbeiter
    $router->add("rotationsplanController", "getVergleich");
} elseif ($request == "/rotationsplan/mitarbeiter/getVergleichChart") { # Mitarbeiter
    $router->add("rotationsplanController", "getVergleichChart");
} elseif ($request == "/rotationsplan/rotationsplan") { # Mitarbeiter
    $router->add("rotationsplanController", "rotationsplan");
} elseif ($request == "/rotationsplan/checkRfid") { # Mitarbeiter
    $router->add("rotationsplanController", "checkRfid");
} elseif ($request == "/rotationsplan/rfid") { # Mitarbeiter
    $router->add("rotationsplanController", "rfid");
} elseif ($request == "/rotationsplan/ajaxSetAbteilung") { # Mitarbeiter
    $router->add("rotationsplanController", "setAbteilung");
} elseif ($request == "/rotationsplan/ajaxSetSchicht") { # Mitarbeiter
    $router->add("rotationsplanController", "setSchicht");

    # Change Management
} elseif ($request == "/changeManagement") { # Change Management: Startseite
    $router->add("changeManagementController", "index");
} elseif ($request == "/changeManagement/details") { # Change Management: Detailansicht
    $router->add("changeManagementController", "details");
} elseif ($request == "/changeManagement/evaluation") { # Change Management: Evaluation
    $router->add("changeManagementController", "evaluation");
} elseif ($request == "/changeManagement/tracking") { # Change Management: Tracking
    $router->add("changeManagementController", "tracking");
} elseif ($request == "/changeManagement/ajaxStatus") { # Change Management: Overall Status Detailseite
    $router->add("changeManagementController", "getStatus");
} elseif ($request == "/changeManagement/changeValue") { # Change Management: Value (Date Implement) senden
    $router->add("changeManagementController", "changeValue");
} elseif ($request == "/changeManagement/setValue") { # Change Management: Value senden
    $router->add("changeManagementController", "setValue");
} elseif ($request == "/changeManagement/simpleChange") { # Change Management: Vereinfachten Durchlauf durchführen
    $router->add("changeManagementController", "simpleChange");
} elseif ($request == "/changeManagement/changeOrder") { # Change Management: Ordernummer senden
    $router->add("changeManagementController", "changeOrder");
} elseif ($request == "/changeManagement/changeVerantwortung") { # Change Management: Verantwortlichen ändern
    $router->add("changeManagementController", "changeVerantwortung");
} elseif ($request == "/changeManagement/changeZieldatum") { # Change Management: Zieldatum ändern
    $router->add("changeManagementController", "changeZieldatum");
} elseif ($request == "/changeManagement/setAPQP") { # Change Management: APQP setzen
    $router->add("changeManagementController", "setAPQP");
} elseif ($request == "/changeManagement/changeAPQP") { # Change Management: APQP ändern
    $router->add("changeManagementController", "changeAPQP");
} elseif ($request == "/changeManagement/getFormAPQP") { # Change Management: APQP Formular anzeigen
    $router->add("changeManagementController", "getFormAPQP");
} elseif ($request == "/changeManagement/resetAPQP") { # Change Management: APQP ändern
    $router->add("changeManagementController", "resetAPQP");
} elseif ($request == "/changeManagement/showBemerkungAntwort") { # Change Management: Formular Bemerkung APQP
    $router->add("changeManagementController", "formBemerkungAntwort");
} elseif ($request == "/changeManagement/sendBemerkungAntwort") { # Change Management: Formular Bemerkung APQP versenden
    $router->add("changeManagementController", "sendBemerkungAntwort");
} elseif ($request == "/changeManagement/checkStatus") { # Change Management: Prüfen, ob alles erledigt ist
    $router->add("changeManagementController", "checkStatus");
} elseif ($request == "/changeManagement/finishID") { # Change Management: Auftrag beenden
    $router->add("changeManagementController", "finishID");
} elseif ($request == "/changeManagement/neu") { # Change Management: Neuer Auftrag
    $router->add("changeManagementController", "neu");
} elseif ($request == "/changeManagement/delete") { # Change Management: Auftrag löschen
    $router->add("changeManagementController", "delete");
} elseif ($request == "/changeManagement/neuSend") { # Change Management: Neuer Auftrag speichern
    $router->add("changeManagementController", "neuSend");
} elseif ($request == "/changeManagement/setCitycode") { # Change Management: Citycode für neuen AUftrag setzen
    $router->add("changeManagementController", "setCitycode");
} elseif ($request == "/changeManagement/setLieferanten") { # Change Management: Lieferantenliste
    $router->add("changeManagementController", "setLieferanten");
} elseif ($request == "/changeManagement/ajaxComAPQP") { # Change Management: Kommentare (APQP) anzeigen
    $router->add("changeManagementController", "comAPQP");
} elseif ($request == "/changeManagement/setComAPQP") { # Change Management: Kommentare (APQP) speichern
    $router->add("changeManagementController", "setComAPQP");
} elseif ($request == "/changeManagement/setAntwortCom") { # Change Management: Antwort zu Kommentare (APQP) speichern
    $router->add("changeManagementController", "setAntwortCom");
} elseif ($request == "/changeManagement/aktionen") { # Change Management: Seite mit den Aktionen
    $router->add("changeManagementController", "aktionen");
} elseif ($request == "/changeManagement/angebote") { # Change Management: Seite mit den Angeboten
    $router->add("changeManagementController", "angebote");
} elseif ($request == "/changeManagement/setFreigabe") { # Change Management: Freigabe
    $router->add("changeManagementController", "setFreigabe");
} elseif ($request == "/changeManagement/abschliessen") { # Change Management: Abschliessen
    $router->add("changeManagementController", "abschliessen");
} elseif ($request == "/changeManagement/alteTeile") { # Change Management: Aktion alte Teile
    $router->add("changeManagementController", "alteTeile");
} elseif ($request == "/changeManagement/endeAlteTeile") { # Change Management: Aktion alte Teile (Beenden)
    $router->add("changeManagementController", "endeAlteTeile");
} elseif ($request == "/changeManagement/nachrichten") { # Change Management: Nachrichten
    $router->add("changeManagementController", "nachrichten");
} elseif ($request == "/changeManagement/setNachricht") { # Change Management: Nachrichte speichern
    $router->add("changeManagementController", "setNachricht");
} elseif ($request == "/changeManagement/dateien") { # Change Management: Dateien
    $router->add("changeManagementController", "dateien");
} elseif ($request == "/changeManagement/setDatei") { # Change Management: Datei speichern
    $router->add("changeManagementController", "setDatei");
} elseif ($request == "/changeManagement/partno") { # Change Management: Part Numbers
    $router->add("changeManagementController", "partno");
} elseif ($request == "/changeManagement/setPartno") { # Change Management: Part Number eintragen
    $router->add("changeManagementController", "setPartno");
} elseif ($request == "/changeManagement/partnoTable") { # Change Management: Part Number anzeigen
    $router->add("changeManagementController", "partnoTable");
} elseif ($request == "/changeManagement/changePartno") { # Change Management: Part Number ändern
    $router->add("changeManagementController", "changePartno");
} elseif ($request == "/changeManagement/deletePartno") { # Change Management: Part Number löschen
    $router->add("changeManagementController", "deletePartno");
} elseif ($request == "/changeManagement/partnoUpload") { # Change Management: Part Number löschen
    $router->add("changeManagementController", "partnoUpload");
} elseif ($request == "/changeManagement/lop") { # Change Management: Maßnahmenplan
    $router->add("changeManagementController", "lop");
} elseif ($request == "/changeManagement/setLop") { # Change Management: Maßnahmenplan eintragen
    $router->add("changeManagementController", "setLop");
} elseif ($request == "/changeManagement/changeLop") { # Change Management: Maßnahmenplan bearbeiten
    $router->add("changeManagementController", "changeLop");
} elseif ($request == "/changeManagement/deleteLop") { # Change Management: Maßnahme löschen
    $router->add("changeManagementController", "deleteLop");
} elseif ($request == "/changeManagement/meeting") { # Change Management: Meeting
    $router->add("changeManagementController", "meeting");
} elseif ($request == "/changeManagement/getMeetings") { # Change Management: Meetings anzeigen (AJAX)
    $router->add("changeManagementController", "getMeetings");
} elseif ($request == "/changeManagement/getAngebote") { # Change Management: Angebote anzeigen (AJAX)
    $router->add("changeManagementController", "getAngebote");
} elseif ($request == "/changeManagement/setMeeting") { # Change Management: Meetings speichern (AJAX)
    $router->add("changeManagementController", "setMeeting");
} elseif ($request == "/changeManagement/setMaMeeting") { # Change Management: Meetings Mitarbeiter dazu (AJAX)
    $router->add("changeManagementController", "setMaMeeting");
} elseif ($request == "/changeManagement/setMaAngebot") { # Change Management: Angebote Mitarbeiter dazu (AJAX)
    $router->add("changeManagementController", "setMaAngebot");
} elseif ($request == "/changeManagement/getMaAngebot") { # Change Management: Angebote Mitarbeiter (AJAX)
    $router->add("changeManagementController", "getMaAngebot");
} elseif ($request == "/changeManagement/checkAuftrag") { # Change Management: Auftrag Status prüfen (AJAX)
    $router->add("changeManagementController", "checkAuftrag");
} elseif ($request == "/changeManagement/getAPQPElement") { # Change Management: APQP Evaluation
    $router->add("changeManagementController", "getAPQPElement");
} elseif ($request == "/changeManagement/suche") { # Change Management: Suche
    $router->add("changeManagementController", "suche");
} elseif ($request == "/changeManagement/deleteFile") { # Change Management: Datei löschen
    $router->add("changeManagementController", "deleteFile");
} elseif ($request == "/changeManagement/setAccess") { # Change Management: Berechtigung Angebot
    $router->add("changeManagementController", "setAccess");


# Administration
} elseif ($request == "/administration") { # Administration: Startseite
    $router->add("administrationController", "index");
} elseif ($request == "/administration/mitarbeiter") { # Administration: Miarbeiter
    $router->add("administrationController", "mitarbeiter");
} elseif ($request == "/administration/mitarbeiter/neu") { # Administration: Neuen Mitarbeiter speichern
    $router->add("administrationController", "setMitarbeiter");
} elseif ($request == "/administration/mitarbeiter/setRechte") { # Administration: Neuen Mitarbeiter speichern
    $router->add("administrationController", "setRechte");
} elseif ($request == "/administration/mitarbeiter/details") { # Administration: Mitarbeiter Details
    $router->add("administrationController", "mitarbeiterDetails");
} elseif ($request == "/administration/apqp") { # Administration: APQP
    $router->add("administrationController", "apqp");
} elseif ($request == "/administration/getAPQP") { # Administration: Evaluation oder Tracking
    $router->add("administrationController", "getAPQP");
} elseif ($request == "/administration/deleteAPQP") { # Administration: Evaluation oder Tracking löschen
    $router->add("administrationController", "deleteAPQP");
} elseif ($request == "/administration/apqp/neu") { # Administration: Neuen APQP Punkt speichern
    $router->add("administrationController", "setAPQP");
} elseif ($request == "/administration/apqp/delete/citycode") { # Administration: APQP Zuordnung Citycode löschen
    $router->add("administrationController", "deleteApqpCitycode");
} elseif ($request == "/administration/netzwerk") { # Netzwerk
    $router->add("administrationController", "netzwerk");
} elseif ($request == "/administration/aruba/stack") { # Netzwerk Übersicht Stack
    $router->add("administrationController", "getArubaStack");
} elseif ($request == "/administration/aruba/switch") { # Netzwerk Übersicht Switche
    $router->add("administrationController", "getArubaSwitch");
} elseif ($request == "/administration/microsens/ring1") { # Netzwerk Übersicht Ring 1
    $router->add("administrationController", "getMicrosensRing1");
} elseif ($request == "/administration/microsens/ring2") { # Netzwerk Übersicht Ring 2
    $router->add("administrationController", "getMicrosensRing2");
}  elseif ($request == "/administration/microsens/ring3") { # Netzwerk Übersicht Ring 3
    $router->add("administrationController", "getMicrosensRing3");
}  elseif ($request == "/administration/microsens/ring5") { # Netzwerk Übersicht Ring 5
    $router->add("administrationController", "getMicrosensRing5");
}  elseif ($request == "/administration/microsens/ring6") { # Netzwerk Übersicht Ring 6
    $router->add("administrationController", "getMicrosensRing6");
}  elseif ($request == "/administration/microsens/ring7") { # Netzwerk Übersicht Ring 7
    $router->add("administrationController", "getMicrosensRing7");


# Produktion
}  elseif ($request == "/produktion") { # Produktion: Startseite
    $router->add("produktionController", "index");
}  elseif ($request == "/produktion/motorband/openCalloffs") { # Produktion: Offene Calloffs
    $router->add("produktionController", "openCalloffs");
}  elseif ($request == "/produktion/motorband/chartCalloffs") { # Produktion: Offene Calloffs (Chart)
    $router->add("produktionController", "chartCalloffs");
}  elseif ($request == "/produktion/motorband/numberCalloffs") { # Produktion: Offene Calloffs (Table)
    $router->add("produktionController", "numberCalloffs");
}  elseif ($request == "/produktion/motorband/tableCalloffs") { # Produktion: Offene Calloffs (Summe)
    $router->add("produktionController", "tableCalloffs");
}  elseif ($request == "/produktion/motorband/fabMotorband") { # Produktion: Startseite
    $router->add("produktionController", "fabMotorband");
} elseif ($request == "/produktion/motorband/taktzeit") { # Taktzeit Motorband
    $router->add("produktionController", "taktMotorband");
} elseif ($request == "/ajaxGetProduktionKW") { # Produktionszahlen (Aktuell)
    $router->add("produktionController", "ajaxGetProduktionKW");
} elseif ($request == "/ajaxGetProduktionChart") { # Produktionszahlen (Aktuell)
    $router->add("produktionController", "ajaxGetProduktionChart");
} elseif ($request == "/produktion/bandsicherung") { # Bandsicherung iSeries
    $router->add("produktionController", "bandsicherung");
} elseif ($request == "/produktion/bandsicherung/insert") { # Bandsicherung iSeries speichern
    $router->add("produktionController", "insertBandsicherung");
} elseif ($request == "/produktion/frontcorner/logfiles") { # Frontcorner: Logfiles (funktioniert noch nicht)
    $router->add("produktionController", "logfilesFrontcorner");
} elseif ($request == "/produktion/frontcorner/dashboard") { # Frontcorner: Dashboard
    $router->add("produktionController", "dashboardFrontcorner");
} elseif ($request == "/produktion/frontcorner/dashboard/show") { # Frontcorner: Dashboard (inlude)
    $router->add("produktionController", "dspDashboardFrontcorner");
} elseif ($request == "/produktion/frontcorner/dashboard/showStation") { # Frontcorner: Dashboard (eine Station)
    $router->add("produktionController", "dspStationDashboardFrontcorner");


# Produktionsanzeigen
}  elseif ($request == "/prodview") { # Produktionsanzeigen: Startseite
    $router->add("prodviewController", "index");
}  elseif ($request == "/prodview/linien/stationen") { # Produktionsanzeigen: Stationen einer Linie anzeigen
    $router->add("prodviewController", "getStationen");
}  elseif ($request == "/prodview/linien/station") { # Produktionsanzeigen: Eine Station anzeigen
    $router->add("prodviewController", "getStation");
}  elseif ($request == "/prodview/citycode") { # Produktionsanzeigen: Eingetragene Citycodes
    $router->add("prodviewController", "citycode");
}  elseif ($request == "/prodview/linien/line") { # Produktionsanzeigen: Dashboard Line
    $router->add("prodviewController", "line");

# Email
}  elseif ($request == "/email/neu/cm") { # Neuer Changemanagement Eintrag
    $router->add("emailController", "sendEmailCMNeu");


# Kalender
}  elseif ($request == "/kalender") { # Kalender Startseite
    $router->add("kalenderController", "index");

# Schulungen
}  elseif ($request == "/schulungen") { # Schulungen: Startseite
    $router->add("schulungenController", "index");
}  elseif ($request == "/schulungen/insertSchulung") { # Schulungen: neue Schulung speichern
    $router->add("schulungenController", "insertSchulung");

# Servicedesk
}  elseif ($request == "/servicedesk") { # Servicedesk: Startseite
    $router->add("servicedeskController", "index");
}  elseif ($request == "/servicedesk/insert") { # Servicedesk: Neuer Eintrag
    $router->add("servicedeskController", "insert");
}  elseif ($request == "/servicedesk/details") { # Servicedesk: Details Eintrag
    $router->add("servicedeskController", "details");
}  elseif ($request == "/servicedesk/meinAuftrag") { # Servicedesk: Auftrag übernehmen
    $router->add("servicedeskController", "meinAuftrag");
}  elseif ($request == "/servicedesk/deinAuftrag") { # Servicedesk: Auftrag delegieren
    $router->add("servicedeskController", "deinAuftrag");
}  elseif ($request == "/servicedesk/start") { # Servicedesk: Auftrag starten
    $router->add("servicedeskController", "start");
}  elseif ($request == "/servicedesk/pause") { # Servicedesk: Auftrag Pause
    $router->add("servicedeskController", "pause");
}  elseif ($request == "/servicedesk/weiter") { # Servicedesk: Auftrag weiter
    $router->add("servicedeskController", "weiter");
}  elseif ($request == "/servicedesk/beenden") { # Servicedesk: Auftrag beenden
    $router->add("servicedeskController", "beenden");
}  elseif ($request == "/servicedesk/nio") { # Servicedesk: Auftrag nio
    $router->add("servicedeskController", "nio");
}  elseif ($request == "/servicedesk/abschluss") { # Servicedesk: Auftrag abschließen
    $router->add("servicedeskController", "abschluss");
}  elseif ($request == "/servicedesk/kommentar/insert") { # Servicedesk: Kommentar speichern
    $router->add("servicedeskController", "insertKommentar");
}  elseif ($request == "/servicedesk/upload") { # Servicedesk: Fileupload
    $router->add("servicedeskController", "upload");
}  elseif ($request == "/servicedesk/deleteFile") { # Servicedesk: Datei löschen
    $router->add("servicedeskController", "deleteFile");
}  elseif ($request == "/servicedesk/archiv") { # Servicedesk: Archiv
    $router->add("servicedeskController", "archiv");




# Fehlermeldung
} else {
    $router->add("errorController", "errorPage");
}