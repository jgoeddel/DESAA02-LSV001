<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Prodview;

use App\App\AbstractMVC\AbstractDatabase;
use PDO;
use PDOStatement;

class ProdviewDatabase extends AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Verbindung zum SQL Server aufbauen
    public static function connectWSV575()
    {
        $srv = $_SESSION['WSV575']['server'];
        $uid = $_SESSION['WSV575']['uid'];
        $pw = $_SESSION['WSV575']['pwd'];
        try {
            $conn = new PDO("sqlsrv:server=$srv;Database=BI_Master;TrustServerCertificate=1",$uid, $pw);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            die("Fehler: ". $e->getMessage());
        }
        return $conn;
    }
    public static function run($sql, $bind = NULL): bool|PDOStatement
    {
        $stmt = self::connectWSV575()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Alle Einträge eines Standortes an einem Tag
    public function getEntries($citycode, $tag): bool|array
    {
        # Aktuelle Produktionsdaten abrufen
        $sql = "SELECT TOP(10000) MasterData.*, CityCode.CityCode, Station.Description, Station.Id AS sid ";
        $sql.= "FROM MasterData ";
        $sql.= "JOIN CityCode ON MasterData.IdCityCode = CityCode.Id ";
        $sql.= "JOIN Station ON MasterData.Station = Station.Id ";
        $sql.= "WHERE MasterData.TimeStamp LIKE '$tag%' ";
        $sql.= "AND MasterData.IdCityCode = '$citycode' ";
        $sql.= "ORDER BY MasterData.Value2";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # All Einträge einer Station an einem Tag
    public function getEntriesStation($sid, $tag): bool|array
    {
        # Aktuelle Produktionsdaten abrufen
        $sql = "SELECT TOP(10000) MasterData.*, CityCode.CityCode, Station.Description, Station.Id AS sid ";
        $sql.= "FROM MasterData ";
        $sql.= "JOIN CityCode ON MasterData.IdCityCode = CityCode.Id ";
        $sql.= "JOIN Station ON MasterData.Station = Station.Id ";
        $sql.= "WHERE MasterData.TimeStamp LIKE '$tag%' ";
        $sql.= "AND Station.Id = '$sid' ";
        $sql.= "ORDER BY MasterData.Value2";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Linien eines Standortes abrufen
    public function getLineCitycode($IdCityCode=6)
    {
        $sql = "SELECT * FROM Line WHERE IdCityCode = ?";
        return self::run($sql,[$IdCityCode])->fetchAll(PDO::FETCH_OBJ);
    }
    # Eine Linien anhand der ID abrufen
    public function getLine($id)
    {
        $sql = "SELECT * FROM Line WHERE id = ?";
        return self::run($sql,[$id])->fetch(PDO::FETCH_OBJ);
    }
    # Alle Stationen der entsprechenden Linie abrufen
    public static function getStationLine($IdLine)
    {
        $sql = "SELECT * FROM Station WHERE IdLine = ?";
        return self::run($sql,[$IdLine])->fetchAll(PDO::FETCH_OBJ);
    }
    # Letzte Stationen der jeweiligen Linien
    # FA FRONT AXLE: FA100 (ID: 10)
    # FP: FA410 (ID: 14)
    # FS FC: FC310 (ID: 19)
    # RA REAR AXLE: RA180 (ID: 39)
    # RP: RA410 (ID: 43)
    # RS RC: RC040 (ID: 47)

    # Letzte Aktivität an der letzten Station der Linie
    public static function getLastEntry($sid,$IdCityCode=6)
    {
        $sql = "SELECT TOP (1) * FROM MasterData WHERE IdCityCode = '$IdCityCode' AND Station = '$sid' ORDER BY TimeStamp DESC";
        return self::run($sql)->fetch(PDO::FETCH_OBJ);
    }

    # Backgroundcolor anhand Status ausgeben
    public static function bgColorStatus($status)
    {
        switch($status):
            case 4:
            case 7:
            case 6:
                $bgc = 'warning';
                break;
            case 3:
            case 2:
                $bgc = 'danger';
                break;
            case 5:
                $bgc = 'info';
                break;
            case 1:
                $bgc = 'success';
                break;
        endswitch;
        return $bgc;
    }
    # Icon anhand Status ausgeben
    public static function iconStatus($status)
    {
        switch($status):
            case 4:
            case 7:
            case 6:
                $bgc = 'fa-exclamation-triangle';
                break;
            case 3:
            case 2:
                $bgc = 'fa-ban';
                break;
            case 5:
                $bgc = 'fa-info';
                break;
            case 1:
                $bgc = 'fa-check';
                break;
        endswitch;
        return $bgc;
    }
}