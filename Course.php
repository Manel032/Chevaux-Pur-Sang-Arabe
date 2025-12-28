<?php
class Course {

    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /* =========================
       READ – Toutes les courses
    ========================== */
    public function all(): array {
        $sql = "
            SELECT co.*, c.nom AS cheval, o.nom AS owner, j.nom AS jockey
            FROM courses co
            LEFT JOIN chevaux c ON co.cheval_id = c.id
            LEFT JOIN owners o ON c.owner_id = o.id
            LEFT JOIN jockeys j ON c.jockey_id = j.id
            ORDER BY co.date DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       READ – Une course
    ========================== */
    public function show(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT co.*, c.nom AS cheval, o.nom AS owner, j.nom AS jockey
            FROM courses co
            LEFT JOIN chevaux c ON co.cheval_id = c.id
            LEFT JOIN owners o ON c.owner_id = o.id
            LEFT JOIN jockeys j ON c.jockey_id = j.id
            WHERE co.id = ?
        ");
        $stmt->execute([$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        return $course ?: null;
    }

    /* =========================
       CREATE – Ajouter une course
    ========================== */
    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO courses (nom, date, trophée, gains, cheval_id)
            VALUES (:nom, :date, :trophee, :gains, :cheval_id)
        ");
        return $stmt->execute([
            ":nom"       => $data["nom"],
            ":date"      => $data["date"],
            ":trophee"   => $data["trophée"],
            ":gains"     => $data["gains"],
            ":cheval_id" => $data["cheval_id"]
        ]);
    }

    /* =========================
       UPDATE – Modifier une course
    ========================== */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE courses SET
                nom = :nom,
                date = :date,
                trophée = :trophee,
                gains = :gains,
                cheval_id = :cheval_id
            WHERE id = :id
        ");
        return $stmt->execute([
            ":nom"       => $data["nom"],
            ":date"      => $data["date"],
            ":trophee"   => $data["trophée"],
            ":gains"     => $data["gains"],
            ":cheval_id" => $data["cheval_id"],
            ":id"        => $id
        ]);
    }

    /* =========================
       DELETE – Supprimer une course
    ========================== */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(array $filters): array {
        $sql = "SELECT co.*, c.nom AS cheval, o.nom AS owner, j.nom AS jockey
                FROM courses co
                LEFT JOIN chevaux c ON co.cheval_id = c.id
                LEFT JOIN owners o ON c.owner_id = o.id
                LEFT JOIN jockeys j ON c.jockey_id = j.id
                WHERE 1=1";
        $params = [];

        if(!empty($filters["nom"])){
            $sql .= " AND co.nom LIKE ?";
            $params[] = "%".$filters["nom"]."%";
        }

        if(!empty($filters["cheval_id"])){
            $sql .= " AND co.cheval_id = ?";
            $params[] = $filters["cheval_id"];
        }

        if(!empty($filters["date"])){
            $sql .= " AND co.date = ?";
            $params[] = $filters["date"];
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
            "total" => (int)$this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
            "gains_total" => (float)$this->db->query("SELECT SUM(gains) FROM courses")->fetchColumn()
        ];
    }

    /* =========================
       COURSES D’UN CHEVAL
    ========================== */
    public function byHorse(int $cheval_id): array {
        $stmt = $this->db->prepare("
            SELECT co.*, c.nom AS cheval, o.nom AS owner, j.nom AS jockey
            FROM courses co
            LEFT JOIN chevaux c ON co.cheval_id = c.id
            LEFT JOIN owners o ON c.owner_id = o.id
            LEFT JOIN jockeys j ON c.jockey_id = j.id
            WHERE co.cheval_id = ?
            ORDER BY co.date DESC
        ");
        $stmt->execute([$cheval_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DERNIÈRES COURSES AJOUTÉES
    ========================== */
    public function latest(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT co.*, c.nom AS cheval, o.nom AS owner, j.nom AS jockey
            FROM courses co
            LEFT JOIN chevaux c ON co.cheval_id = c.id
            LEFT JOIN owners o ON c.owner_id = o.id
            LEFT JOIN jockeys j ON c.jockey_id = j.id
            ORDER BY co.date DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}?>
