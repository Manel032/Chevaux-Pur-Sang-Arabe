<?php
// Owner.php - Model for Owner

require_once 'database.php';

class Owner {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM owner ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM owner WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO owner (nom, telephone, email, adresse) 
                VALUES (:nom, :telephone, :email, :adresse)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            ':nom' => $data['nom'],
            ':telephone' => !empty($data['telephone']) ? $data['telephone'] : null,
            ':email' => !empty($data['email']) ? $data['email'] : null,
            ':adresse' => !empty($data['adresse']) ? $data['adresse'] : null
        ));
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $sql = "UPDATE owner SET 
                    nom = :nom, 
                    telephone = :telephone, 
                    email = :email, 
                    adresse = :adresse 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(array(
            ':id' => (int)$id,
            ':nom' => $data['nom'],
            ':telephone' => !empty($data['telephone']) ? $data['telephone'] : null,
            ':email' => !empty($data['email']) ? $data['email'] : null,
            ':adresse' => !empty($data['adresse']) ? $data['adresse'] : null
        ));
    }

    public static function delete($id) {
        $db = Database::getConnection();
        
        // Remove owner association from horses owned by this owner
        $stmt_assoc = $db->prepare("UPDATE cheval SET owner_id = NULL WHERE owner_id = :id");
        $stmt_assoc->execute(array(':id' => (int)$id));

        $stmt = $db->prepare("DELETE FROM owner WHERE id = :id");
        return $stmt->execute(array(':id' => (int)$id));
    }

    // Get list of horses owned by this owner
    public static function getHorses($owner_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nom, race, sexe FROM cheval WHERE owner_id = :owner_id ORDER BY nom ASC");
        $stmt->execute(array(':owner_id' => $owner_id));
        return $stmt->fetchAll();
    }
}
