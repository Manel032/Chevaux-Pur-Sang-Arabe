<?php
// Course.php - Model for Course (Races)

require_once 'database.php';

class Course {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM course ORDER BY date_course DESC, nom ASC");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM course WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO course (nom, date_course, lieu, distance, prix_millimes) 
                VALUES (:nom, :date_course, :lieu, :distance, :prix_millimes)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            ':nom' => $data['nom'],
            ':date_course' => $data['date_course'],
            ':lieu' => $data['lieu'],
            ':distance' => (int)$data['distance'],
            ':prix_millimes' => isset($data['prix_millimes']) ? (int)$data['prix_millimes'] : 0
        ));
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $sql = "UPDATE course SET 
                    nom = :nom, 
                    date_course = :date_course, 
                    lieu = :lieu, 
                    distance = :distance, 
                    prix_millimes = :prix_millimes 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(array(
            ':id' => (int)$id,
            ':nom' => $data['nom'],
            ':date_course' => $data['date_course'],
            ':lieu' => $data['lieu'],
            ':distance' => (int)$data['distance'],
            ':prix_millimes' => isset($data['prix_millimes']) ? (int)$data['prix_millimes'] : 0
        ));
    }

    public static function delete($id) {
        $db = Database::getConnection();
        
        // Delete participations associated with this race
        $stmt_assoc = $db->prepare("DELETE FROM participation WHERE course_id = :id");
        $stmt_assoc->execute(array(':id' => (int)$id));

        $stmt = $db->prepare("DELETE FROM course WHERE id = :id");
        return $stmt->execute(array(':id' => (int)$id));
    }

    // Get all horse participations inside this race, ordered by ranking
    public static function getParticipations($course_id) {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.nom AS cheval_nom, c.race AS cheval_race, j.nom AS jockey_nom
                FROM participation p
                JOIN cheval c ON p.cheval_id = c.id
                LEFT JOIN jockey j ON p.jockey_id = j.id
                WHERE p.course_id = :course_id
                ORDER BY CASE WHEN p.classement IS NULL THEN 999 ELSE p.classement END ASC, c.nom ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':course_id' => $course_id));
        return $stmt->fetchAll();
    }

    // Add a horse participation to a race
    public static function addParticipation($data) {
        $db = Database::getConnection();
        
        // Remove existing participation for this horse in this race if any
        $stmt_del = $db->prepare("DELETE FROM participation WHERE course_id = :course_id AND cheval_id = :cheval_id");
        $stmt_del->execute(array(
            ':course_id' => (int)$data['course_id'],
            ':cheval_id' => (int)$data['cheval_id']
        ));

        $sql = "INSERT INTO participation (cheval_id, course_id, jockey_id, classement)
                VALUES (:cheval_id, :course_id, :jockey_id, :classement)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(array(
            ':cheval_id' => (int)$data['cheval_id'],
            ':course_id' => (int)$data['course_id'],
            ':jockey_id' => !empty($data['jockey_id']) ? (int)$data['jockey_id'] : null,
            ':classement' => !empty($data['classement']) ? (int)$data['classement'] : null
        ));
    }

    // Remove a horse participation from a race
    public static function removeParticipation($course_id, $cheval_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM participation WHERE course_id = :course_id AND cheval_id = :cheval_id");
        return $stmt->execute(array(
            ':course_id' => (int)$course_id,
            ':cheval_id' => (int)$cheval_id
        ));
    }
}
