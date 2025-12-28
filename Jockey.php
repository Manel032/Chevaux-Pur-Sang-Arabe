<?php
class Jockey {

    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /* =========================
       READ – Tous les jockeys
    ========================== */
    public function all(): array {
        return $this->db->query("SELECT * FROM jockeys ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       READ – Un jockey
    ========================== */
    public function show(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM jockeys WHERE id = ?");
        $stmt->execute([$id]);
        $jockey = $stmt->fetch(PDO::FETCH_ASSOC);
        return $jockey ?: null;
    }

    /* =========================
       CREATE – Ajouter
    ========================== */
    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO jockeys (nom, pays, email, tel)
            VALUES (:nom, :pays, :email, :tel)
        ");
        return $stmt->execute([
            ":nom"   => $data["nom"],
            ":pays"  => $data["pays"],
            ":email" => $data["email"],
            ":tel"   => $data["tel"]
        ]);
    }

    /* =========================
       UPDATE
    ========================== */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE jockeys SET
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
    }

    /* =========================
       DELETE
    ========================== */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM jockeys WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(array $filters): array {
        $sql = "SELECT * FROM jockeys WHERE 1=1";
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
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(): array {
        return [
            "total" => (int)$this->db->query("SELECT COUNT(*) FROM jockeys")->fetchColumn(),
            "pays"  => $this->db->query("
                SELECT pays, COUNT(*) as total
                FROM jockeys
                GROUP BY pays
            ")->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    /* =========================
       CHEVAUX D’UN JOCKEY
    ========================== */
    public function horses(int $jockey_id): array {
        $sql = "
            SELECT c.*, o.nom AS owner
            FROM chevaux c
            LEFT JOIN owners o ON c.owner_id = o.id
            WHERE c.jockey_id = ?
            ORDER BY c.nom ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jockey_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DERNIERS JOCKEYS AJOUTÉS
    ========================== */
    public function latest(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM jockeys ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>