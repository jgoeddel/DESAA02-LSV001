<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\SecurityLogin;

class SecurityLoginDatabase extends \App\App\AbstractMVC\AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Anmelden
    function newStayin($user_id, $identifier, $securitytoken)
    {
        $table = $this->getTable("c_securitytokens");
        if (!empty($this->pdo)) {
            $sql = "INSERT INTO $table SET user_id = :user_id, identifier = :identifier, securitytoken = :securitytoken";
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                'user_id' => $user_id,
                'identifier' => $identifier,
                'securitytoken' => $securitytoken,
            ]);
        }
    }

    # Angemeldet ?
    function getStayinData($identifier)
    {
        $table = $this->getTable("c_securitytokens");
        if (!empty($this->pdo)) {
            $user = $this->pdo->prepare("SELECT * FROM `$table` WHERE `identifier` = :identifier");
            $user->execute([
                'identifier' => $identifier,
            ]);
            $userdata = $user->fetch(\PDO::FETCH_OBJ);
        }
        return $userdata;
    }

    # Anmeldung verlängern
    function updateSecurityToken($securitytoken, $user_id)
    {
        $table = $this->getTable("c_securitytokens");
        if (!empty($this->pdo)) {
            $statement = $this->pdo->prepare("UPDATE `$table` SET `securitytoken` = :securitytoken WHERE `user_id` = :userid");
            $statement->execute([
                'securitytoken' => $securitytoken,
                'userid' => $user_id
            ]);
        }
    }

    # Anmeldung löschen
    function deleteStayindata($user_id)
    {
        $table = $this->getTable("c_securitytokens");
        if (!empty($this->pdo)) {
            $statement = $this->pdo->prepare("DELETE FROM `$table` WHERE `user_id` = :userid");
            $statement->execute([
                'userid' => $user_id
            ]);
        }
    }
}