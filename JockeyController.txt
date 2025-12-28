<?php
class JockeyController {

    private Jockey $model;

    public function __construct(Jockey $model){
        $this->model = $model;
    }

    /* =========================
       LISTE TOUS LES JOCKEYS
    ========================== */
    public function index(){
        $jockeys = $this->model->all();
        echo json_encode([
            "success" => true,
            "data" => $jockeys
        ]);
    }

    /* =========================
       AFFICHER UN JOCKEY (avec ses chevaux)
    ========================== */
    public function show(int $id){
        $jockey = $this->model->show($id);
        if($jockey){
            $jockey["chevaux"] = $this->model->horses($id);
            echo json_encode([
                "success" => true,
                "data" => $jockey
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Jockey non trouvé"
            ]);
        }
    }

    /* =========================
       AJOUTER UN JOCKEY
    ========================== */
    public function store(){
        $data = json_decode(file_get_contents("php://input"), true);
        $success = $this->model->create($data);
        echo json_encode([
            "success" => $success
        ]);
    }

    /* =========================
       MODIFIER UN JOCKEY
    ========================== */
    public function update(int $id){
        $data = json_decode(file_get_contents("php://input"), true);
        $success = $this->model->update($id, $data);
        echo json_encode([
            "success" => $success
        ]);
    }

    /* =========================
       SUPPRIMER UN JOCKEY
    ========================== */
    public function destroy(int $id){
        $success = $this->model->delete($id);
        echo json_encode([
            "success" => $success
        ]);
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(){
        $filters = json_decode(file_get_contents("php://input"), true);
        $results = $this->model->search($filters);
        echo json_encode([
            "success" => true,
            "data" => $results
        ]);
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(){
        $stats = $this->model->stats();
        echo json_encode([
            "success" => true,
            "data" => $stats
        ]);
    }

    /* =========================
       CHEVAUX D’UN JOCKEY
    ========================== */
    public function horses(int $jockey_id){
        $chevaux = $this->model->horses($jockey_id);
        echo json_encode([
            "success" => true,
            "data" => $chevaux
        ]);
    }

    /* =========================
       DERNIERS JOCKEYS AJOUTÉS
    ========================== */
    public function latest(int $limit = 5){
        $jockeys = $this->model->latest($limit);
        echo json_encode([
            "success" => true,
            "data" => $jockeys
        ]);
    }
}
?>