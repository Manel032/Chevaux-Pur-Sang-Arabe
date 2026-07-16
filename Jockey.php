<?php
// Jockey.php - Model for Jockey

require_once 'database.php';

class Jockey {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM jockey ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM jockey WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO jockey (nom, nationalite, experience_annees) 
                VALUES (:nom, :nationalite, :experience_annees)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            ':nom' => $data['nom'],
            ':nationalite' => !empty($data['nationalite']) ? $data['nationalite'] : 'Tunisienne',
            ':experience_annees' => isset($data['experience_annees']) ? (int)$data['experience_annees'] : 0
        ));
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $sql = "UPDATE jockey SET 
                    nom = :nom, 
                    nationalite = :nationalite, 
                    experience_annees = :experience_annees 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(array(
            ':id' => (int)$id,
            ':nom' => $data['nom'],
            ':nationalite' => !empty($data['nationalite']) ? $data['nationalite'] : 'Tunisienne',
            ':experience_annees' => isset($data['experience_annees']) ? (int)$data['experience_annees'] : 0
        ));
    }

    public static function delete($id) {
        $db = Database::getConnection();
        
        // Remove jockey references in participations
        $stmt_assoc = $db->prepare("UPDATE participation SET jockey_id = NULL WHERE jockey_id = :id");
        $stmt_assoc->execute(array(':id' => (int)$id));

        $stmt = $db->prepare("DELETE FROM jockey WHERE id = :id");
        return $stmt->execute(array(':id' => (int)$id));
    }

    // Get list of races this jockey has run
    public static function getParticipations($jockey_id) {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.nom AS course_nom, c.date_course, c.lieu, ch.nom AS cheval_nom
                FROM participation p
                JOIN course c ON p.course_id = c.id
                JOIN cheval ch ON p.cheval_id = ch.id
                WHERE p.jockey_id = :jockey_id
                ORDER BY c.date_course DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':jockey_id' => $jockey_id));
        return $stmt->fetchAll();
    }
}
