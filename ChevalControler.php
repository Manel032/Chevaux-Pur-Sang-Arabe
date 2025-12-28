<?php
class ChevalController {
    private $model;

    public function __construct($model){
        $this->model = $model;
    }

    /* =========================
       LISTE TOUS LES CHEVAUX
    ========================== */
    public function index(){
        echo json_encode($this->model->all());
    }

    /* =========================
       AFFICHER UN CHEVAL
    ========================== */
    public function show($id){
        $cheval = $this->model->show($id);
        if($cheval){
            echo json_encode($cheval);
        } else {
            http_response_code(404);
            echo json_encode(["error"=>"Cheval non trouvé"]);
        }
    }

    /* =========================
       AJOUTER UN CHEVAL
    ========================== */
    public function store(){
        $data = json_decode(file_get_contents("php://input"), true);
        $success = $this->model->create($data);
        echo json_encode(["success"=>$success]);
    }

    /* =========================
       MODIFIER UN CHEVAL
    ========================== */
    public function update($id){
        $data = json_decode(file_get_contents("php://input"), true);
        $success = $this->model->updateFull($id, $data);
        echo json_encode(["success"=>$success]);
    }

    /* =========================
       SUPPRIMER UN CHEVAL
    ========================== */
    public function destroy($id){
        $success = $this->model->delete($id);
        echo json_encode(["success"=>$success]);
    }

    /* =========================
       UPLOAD IMAGE
    ========================== */
    public function uploadImage($id){
        if(!isset($_FILES['image'])){
            http_response_code(400);
            echo json_encode(["error"=>"Fichier image manquant"]);
            return;
        }

        $file = $_FILES['image'];
        $allowed = ["jpg","jpeg","png","gif"];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)){
            http_response_code(400);
            echo json_encode(["error"=>"Format de fichier non autorisé"]);
            return;
        }

        $name = uniqid() . "." . $ext;
        $uploadDir = __DIR__ . "/../uploads/";

        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        move_uploaded_file($file['tmp_name'], $uploadDir . $name);

        $success = $this->model->updateImage($id, $name);
        echo json_encode(["success"=>$success, "image"=>$name]);
    }

    /* =========================
       RECHERCHE AVANCÉE
    ========================== */
    public function search(){
        $filters = json_decode(file_get_contents("php://input"), true);
        $results = $this->model->search($filters);
        echo json_encode($results);
    }

    /* =========================
       STATISTIQUES
    ========================== */
    public function stats(){
        echo json_encode($this->model->stats());
    }

    /* =========================
       DERNIERS CHEVAUX AJOUTÉS
    ========================== */
    public function latest($limit = 5){
        $results = $this->model->latest($limit);
        echo json_encode($results);
    }

    /* =========================
       CHEVAUX AVEC PLUS DE GAINS
    ========================== */
    public function topGains($limit = 5){
        $results = $this->model->topGains($limit);
        echo json_encode($results);
    }

    /* =========================
       CHEVAL AVEC TOUTES SES COURSES
    ========================== */
    public function withCourses($id){
        $results = $this->model->withCourses($id);
        echo json_encode($results);
    }

    /* =========================
       FILTRER CHEVAUX PAR OWNER
    ========================== */
    public function byOwner($owner_id){
        $results = $this->model->byOwner($owner_id);
        echo json_encode($results);
    }
}
