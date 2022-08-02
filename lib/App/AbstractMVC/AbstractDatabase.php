<?php
/** (c) Joachim Göddel . RLMS */
namespace App\App\AbstractMVC;

use App\Functions\Functions;
use PDO;

abstract class AbstractDatabase
{
    # ELEMENTS
    protected PDO $pdo;

    # CONSTRUCT
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    # ABSTRACT
    abstract function getTable($table);

    # Eintrag in die Datenbank schreiben
    public function save($tableName, $post)
    {
        # Post Parameter umwandeln
        $params = array_map(fn($attr) => ":$attr", array_keys($post));
        # Query zusammenstellen
        $sql = "INSERT INTO $tableName (" . implode(',', array_keys($post)) . ") VALUES (" . implode(',', $params) . ")";
        $stmt = $this->pdo->prepare($sql);
        foreach ($post as $key => $value) {
            $stmt->bindValue(":$key", Functions::trimValue($value));
        }
        $stmt->execute();
        # Neue ID als Rückgabewert
        $sql = "SELECT LAST_INSERT_ID() AS id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $newID = $stmt->fetch(\PDO::FETCH_OBJ)->id;
        return $newID;
    }

    # Einen Eintrag unformatiert aus der Datenbank abrufen
    public function findOne($tableName, $where) // [email => mail@mail.de, vorname = Joe ...
    {
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $stmt = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
        foreach($where AS $key => $item){
            $stmt->bindValue(":$key", $item);
        }
        $stmt->execute();
        return $stmt->fetchObject(\PDO::FETCH_OBJ);
    }

    # Alle Einträge unformatiert aus der Datenbank abrufen
    public function findAll($tableName, $where) // [email => mail@mail.de, vorname = Joe ...
    {
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $stmt = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
        foreach($where AS $key => $item){
            $stmt->bindValue(":$key", $item);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

}