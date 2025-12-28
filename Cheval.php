<?php
class Cheval {

    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /* =========================
       READ – Tous les chevaux
    ========================== */
    public function all(){
        $sql = "
        SELECT c.*,
               o.nom AS owner,
               j.nom AS jockey
        FROM chevaux c
        LEFT JOIN owners o ON c.owner_id = o.id
        LEFT JOIN jockeys j ON c.jockey_id = j.id
        ORDER BY c.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       READ – Un cheval
    ========================== */
    public function show(int $id){
        $stmt = $this->db->prepare("SELECT * FROM chevaux WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       CREATE – Ajouter
    ========================== */
    public function create(array $data){
        $sql = "
        INSERT INTO chevaux
        (nom, age, ddn, pays_origine, pays_vie, owner_id, jockey_id)
        VALUES
        (:nom, :age, :ddn, :po, :pv, :owner, :jockey)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nom"    => $data["nom"],
            ":age"    => $data["age"],
            ":ddn"    => $data["ddn"],
            ":po"     => $data["pays_origine"],
            ":pv"     => $data["pays_vie"],
            ":owner"  => $data["owner_id"],
            ":jockey" => $data["jockey_id"]
        ]);
    }

    /* =========================
       UPDATE – Simple
    ========================== */
    public function update(int $id, array $data){
        $sql = "
        UPDATE chevaux SET
            nom = :nom,
            age = :age,
            pays_origine = :po,
            pays_vie = :pv
        WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nom" => $data["nom"],
            ":age" => $data["age"],
            ":po"  => $data["pays_origine"],
            ":pv"  => $data["pays_vie"],
            ":id"  => $id
        ]);
    }

    /* =========================
       UPDATE – COMPLET
    ========================== */
    public function updateFull(int $id, array $data){
        $sql = "
        UPDATE chevaux SET
            nom = :nom,
            age = :age,
            ddn = :ddn,
            pays_origine = :po,
            pays_vie = :pv,
            owner_id = :owner,
            jockey_id = :jockey
        WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nom"    => $data["nom"],
            ":age"    => $data["age"],
            ":ddn"    => $data["ddn"],
            ":po"     => $data["pays_origine"],
            ":pv"     => $data["pays_vie"],
            ":owner"  => $data["owner_id"],
            ":jockey" => $data["jockey_id"],
            ":id"     => $id
        ]);
    }

    /* =========================
       DELETE
    ========================== */
    public function delete(int $id){
        $stmt = $this->db->prepare("DELETE FROM chevaux WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* =========================
       UPLOAD IMAGE
    ========================== */
    public function updateImage(int $id, string $image){
        $stmt = $this->db->prepare(
            "UPDATE chevaux SET image = ? WHERE id = ?"
        );
        return $stmt->execute([$image, $id]);
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(array $filters){
        $sql = "SELECT * FROM chevaux WHERE 1=1";
        $params = [];

        if (!empty($filters["nom"])) {
            $sql .= " AND nom LIKE ?";
            $params[] = "%" . $filters["nom"] . "%";
        }

        if (!empty($filters["pays_origine"])) {
            $sql .= " AND pays_origine = ?";
            $params[] = $filters["pays_origine"];
        }

        if (!empty($filters["owner_id"])) {
            $sql .= " AND owner_id = ?";
            $params[] = $filters["owner_id"];
        }

        if (!empty($filters["jockey_id"])) {
            $sql .= " AND jockey_id = ?";
            $params[] = $filters["jockey_id"];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(){
        return [
            "total" => $this->db->query("SELECT COUNT(*) FROM chevaux")->fetchColumn(),
            "pays"  => $this->db->query("
                SELECT pays_origine, COUNT(*) total
                FROM chevaux
                GROUP BY pays_origine
            ")->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    /* =========================
       DERNIERS CHEVAUX AJOUTÉS
    ========================== */
    public function latest(int $limit = 5){
        $sql = "
        SELECT c.*,
               o.nom AS owner,
               j.nom AS jockey
        FROM chevaux c
        LEFT JOIN owners o ON c.owner_id = o.id
        LEFT JOIN jockeys j ON c.jockey_id = j.id
        ORDER BY c.id DESC
        LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       CHEVAUX AVEC PLUS DE GAINS
    ========================== */
    public function topGains(int $limit = 5){
        $sql = "
        SELECT c.*, SUM(co.gains) as total_gains
        FROM chevaux c
        LEFT JOIN courses co ON co.cheval_id = c.id
        GROUP BY c.id
        ORDER BY total_gains DESC
        LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       CHEVAL AVEC TOUTES SES COURSES
    ========================== */
    public function withCourses(int $id){
        $sql = "
        SELECT c.*, co.nom AS course_nom, co.date, co.trophée, co.gains
        FROM chevaux c
        LEFT JOIN courses co ON co.cheval_id = c.id
        WHERE c.id = ?
        ORDER BY co.date DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       CHEVAUX PAR OWNER
    ========================== */
    public function byOwner(int $owner_id){
        $sql = "
        SELECT c.*,
               o.nom AS owner,
               j.nom AS jockey
        FROM chevaux c
        LEFT JOIN owners o ON c.owner_id = o.id
        LEFT JOIN jockeys j ON c.jockey_id = j.id
        WHERE c.owner_id = ?
        ORDER BY c.nom ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 
