<?php
class Owner {

    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /* =========================
       READ – Tous les owners
    ========================== */
    public function all(): array {
        try {
            return $this->db->query("SELECT * FROM owners ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            return [];
        }
    }

    /* =========================
       READ – Un owner
    ========================== */
    public function show(int $id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM owners WHERE id = ?");
            $stmt->execute([$id]);
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);
            return $owner ?: null;
        } catch(PDOException $e){
            return null;
        }
    }

    /* =========================
       READ – Un owner avec ses chevaux
    ========================== */
    public function showWithHorses(int $id): ?array {
        $owner = $this->show($id);
        if(!$owner) return null;
        $owner['chevaux'] = $this->horses($id);
        return $owner;
    }

    /* =========================
       CREATE – Ajouter
    ========================== */
    public function create(array $data): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO owners (nom, pays, email, tel)
                VALUES (:nom, :pays, :email, :tel)
            ");
            return $stmt->execute([
                ":nom"   => $data["nom"],
                ":pays"  => $data["pays"],
                ":email" => $data["email"],
                ":tel"   => $data["tel"]
            ]);
        } catch(PDOException $e){
            return false;
        }
    }

    /* =========================
       UPDATE
    ========================== */
    public function update(int $id, array $data): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE owners SET
                    nom = :nom,
                    pays = :pays,
                    email = :email,
                    tel = :tel
                WHERE id = :id
            ");
            return $stmt->execute([
                ":nom"   => $data["nom"],
                ":pays"  => $data["pays"],
                ":email" => $data["email"],
                ":tel"   => $data["tel"],
                ":id"    => $id
            ]);
        } catch(PDOException $e){
            return false;
        }
    }

    /* =========================
       DELETE
    ========================== */
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM owners WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e){
            return false;
        }
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(array $filters): array {
        try {
            $sql = "SELECT * FROM owners WHERE 1=1";
            $params = [];

            if(!empty($filters["nom"])){
                $sql .= " AND nom LIKE ?";
                $params[] = "%" . $filters["nom"] . "%";
            }
            if(!empty($filters["pays"])){
                $sql .= " AND pays = ?";
                $params[] = $filters["pays"];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            return [];
        }
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(): array {
        try {
            return [
                "total" => (int)$this->db->query("SELECT COUNT(*) FROM owners")->fetchColumn(),
                "pays"  => $this->db->query("
                    SELECT pays, COUNT(*) as total
                    FROM owners
                    GROUP BY pays
                ")->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch(PDOException $e){
            return ["total"=>0,"pays"=>[]];
        }
    }

    /* =========================
       CHEVAUX PAR OWNER
    ========================== */
    public function horses(int $owner_id): array {
        try {
            $sql = "
                SELECT c.*, j.nom AS jockey
                FROM chevaux c
                LEFT JOIN jockeys j ON c.jockey_id = j.id
                WHERE c.owner_id = ?
                ORDER BY c.nom ASC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$owner_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            return [];
        }
    }

    /* =========================
       DERNIERS OWNERS AJOUTÉS
    ========================== */
    public function latest(int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM owners ORDER BY id DESC LIMIT ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            return [];
        }
    }
}
?>