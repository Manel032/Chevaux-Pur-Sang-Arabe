<?php
class OwnerController {

    private Owner $model;

    public function __construct(Owner $model){
        $this->model = $model;
    }

    /* =========================
       LISTE TOUS LES OWNERS
    ========================== */
    public function index(){
        try {
            $owners = $this->model->all();
            echo json_encode([
                "success" => true,
                "data" => $owners
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       AFFICHER UN OWNER (avec ses chevaux)
    ========================== */
    public function show(int $id){
        try {
            $owner = $this->model->show($id);
            if($owner){
                $owner["chevaux"] = $this->model->horses($id);
                echo json_encode([
                    "success" => true,
                    "data" => $owner
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "error" => "Owner non trouvé"
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       AJOUTER UN OWNER
    ========================== */
    public function store(){
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $success = $this->model->create($data);
            echo json_encode(["success" => $success]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       MODIFIER UN OWNER
    ========================== */
    public function update(int $id){
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $success = $this->model->update($id, $data);
            echo json_encode(["success" => $success]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       SUPPRIMER UN OWNER
    ========================== */
    public function destroy(int $id){
        try {
            $success = $this->model->delete($id);
            echo json_encode(["success" => $success]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(){
        try {
            $filters = json_decode(file_get_contents("php://input"), true);
            $results = $this->model->search($filters);
            echo json_encode([
                "success" => true,
                "data" => $results
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(){
        try {
            $stats = $this->model->stats();
            echo json_encode([
                "success" => true,
                "data" => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       CHEVAUX D’UN OWNER
    ========================== */
    public function horses(int $owner_id){
        try {
            $chevaux = $this->model->horses($owner_id);
            echo json_encode([
                "success" => true,
                "data" => $chevaux
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    /* =========================
       DERNIERS OWNERS AJOUTÉS
    ========================== */
    public function latest(int $limit = 5){
        try {
            $owners = $this->model->latest($limit);
            echo json_encode([
                "success" => true,
                "data" => $owners
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }
}
?>