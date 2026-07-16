<?php
// Cheval.php - Model for Cheval

require_once 'database.php';

class Cheval {
    // Fetch all horses with optional filtering (breed, gender, search query)
    public static function getAll($filters = []) {
        $db = Database::getConnection();
        
        $sql = "SELECT c.*, o.nom AS owner_nom, 
                       p.nom AS pere_nom, m.nom AS mere_nom
                FROM cheval c
                LEFT JOIN owner o ON c.owner_id = o.id
                LEFT JOIN cheval p ON c.pere_id = p.id
                LEFT JOIN cheval m ON c.mere_id = m.id
                WHERE 1=1";
        
        $params = array();

        if (!empty($filters['race'])) {
            $sql .= " AND c.race = :race";
            $params[':race'] = $filters['race'];
        }

        if (!empty($filters['sexe'])) {
            $sql .= " AND c.sexe = :sexe";
            $params[':sexe'] = $filters['sexe'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (c.nom LIKE :search OR c.robe LIKE :search OR o.nom LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY c.nom ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Fetch a single horse by ID, including details of owner and parents
    public static function getById($id) {
        $db = Database::getConnection();
        
        $sql = "SELECT c.*, o.nom AS owner_nom, o.telephone AS owner_tel, o.email AS owner_email,
                       p.nom AS pere_nom, m.nom AS mere_nom
                FROM cheval c
                LEFT JOIN owner o ON c.owner_id = o.id
                LEFT JOIN cheval p ON c.pere_id = p.id
                LEFT JOIN cheval m ON c.mere_id = m.id
                WHERE c.id = :id";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':id' => $id));
        return $stmt->fetch();
    }

    // Insert a new horse record
    public static function create($data) {
        $db = Database::getConnection();
        
        $sql = "INSERT INTO cheval (nom, race, sexe, date_naissance, robe, pere_id, mere_id, owner_id, image_url)
                VALUES (:nom, :race, :sexe, :date_naissance, :robe, :pere_id, :mere_id, :owner_id, :image_url)";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            ':nom' => $data['nom'],
            ':race' => $data['race'],
            ':sexe' => $data['sexe'],
            ':date_naissance' => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
            ':robe' => !empty($data['robe']) ? $data['robe'] : null,
            ':pere_id' => !empty($data['pere_id']) ? (int)$data['pere_id'] : null,
            ':mere_id' => !empty($data['mere_id']) ? (int)$data['mere_id'] : null,
            ':owner_id' => !empty($data['owner_id']) ? (int)$data['owner_id'] : null,
            ':image_url' => !empty($data['image_url']) ? $data['image_url'] : null
        ));
        
        return $db->lastInsertId();
    }

    // Update an existing horse record
    public static function update($id, $data) {
        $db = Database::getConnection();
        
        $sql = "UPDATE cheval SET 
                    nom = :nom, 
                    race = :race, 
                    sexe = :sexe, 
                    date_naissance = :date_naissance, 
                    robe = :robe, 
                    pere_id = :pere_id, 
                    mere_id = :mere_id, 
                    owner_id = :owner_id, 
                    image_url = :image_url
                WHERE id = :id";
                
        $stmt = $db->prepare($sql);
        return $stmt->execute(array(
            ':id' => (int)$id,
            ':nom' => $data['nom'],
            ':race' => $data['race'],
            ':sexe' => $data['sexe'],
            ':date_naissance' => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
            ':robe' => !empty($data['robe']) ? $data['robe'] : null,
            ':pere_id' => !empty($data['pere_id']) ? (int)$data['pere_id'] : null,
            ':mere_id' => !empty($data['mere_id']) ? (int)$data['mere_id'] : null,
            ':owner_id' => !empty($data['owner_id']) ? (int)$data['owner_id'] : null,
            ':image_url' => !empty($data['image_url']) ? $data['image_url'] : null
        ));
    }

    // Delete a horse record
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM cheval WHERE id = :id");
        return $stmt->execute(array(':id' => (int)$id));
    }

    // Get list of potential fathers (males)
    public static function getEligibleFathers($excludeId = null) {
        $db = Database::getConnection();
        $sql = "SELECT id, nom, race FROM cheval WHERE sexe = 'Mâle'";
        $params = array();
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        $sql .= " ORDER BY nom ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get list of potential mothers (females)
    public static function getEligibleMothers($excludeId = null) {
        $db = Database::getConnection();
        $sql = "SELECT id, nom, race FROM cheval WHERE sexe = 'Femelle'";
        $params = array();
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        $sql .= " ORDER BY nom ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Fetch the 3-generation pedigree tree for a horse
    public static function getPedigreeTree($id) {
        $horse = self::getBasicInfo($id);
        if (!$horse) return null;

        // Level 1: Horse itself
        $tree = array(
            'id' => $horse['id'],
            'nom' => $horse['nom'],
            'race' => $horse['race'],
            'sexe' => $horse['sexe'],
            'father' => null,
            'mother' => null
        );

        // Level 2: Parents
        if ($horse['pere_id']) {
            $father = self::getBasicInfo($horse['pere_id']);
            if ($father) {
                $tree['father'] = array(
                    'id' => $father['id'],
                    'nom' => $father['nom'],
                    'race' => $father['race'],
                    'sexe' => $father['sexe'],
                    'father' => null,
                    'mother' => null
                );
                // Level 3: Paternal Grandparents
                if ($father['pere_id']) {
                    $tree['father']['father'] = self::getBasicInfo($father['pere_id']);
                }
                if ($father['mere_id']) {
                    $tree['father']['mother'] = self::getBasicInfo($father['mere_id']);
                }
            }
        }

        if ($horse['mere_id']) {
            $mother = self::getBasicInfo($horse['mere_id']);
            if ($mother) {
                $tree['mother'] = array(
                    'id' => $mother['id'],
                    'nom' => $mother['nom'],
                    'race' => $mother['race'],
                    'sexe' => $mother['sexe'],
                    'father' => null,
                    'mother' => null
                );
                // Level 3: Maternal Grandparents
                if ($mother['pere_id']) {
                    $tree['mother']['father'] = self::getBasicInfo($mother['pere_id']);
                }
                if ($mother['mere_id']) {
                    $tree['mother']['mother'] = self::getBasicInfo($mother['mere_id']);
                }
            }
        }

        return $tree;
    }

    // Private helper for pedigree lookup
    private static function getBasicInfo($id) {
        if (!$id) return null;
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nom, race, sexe, pere_id, mere_id FROM cheval WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        return $stmt->fetch();
    }
}
