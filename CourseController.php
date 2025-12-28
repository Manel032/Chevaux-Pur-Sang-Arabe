<?php
class CourseController {

    private Course $model;

    public function __construct(Course $model){
        $this->model = $model;
    }

    /* =========================
       LISTE TOUTES LES COURSES
    ========================== */
    public function index(){
        $courses = $this->model->all();
        echo json_encode([
            "success" => true,
            "data" => $courses
        ]);
    }

    /* =========================
       AFFICHER UNE COURSE
    ========================== */
    public function show(int $id){
        $course = $this->model->show($id);
        if($course){
            echo json_encode([
                "success" => true,
                "data" => $course
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Course non trouvée"
            ]);
        }
    }

    /* =========================
       AJOUTER UNE COURSE
    ========================== */
    public function store(){
        $data = json_decode(file_get_contents("php://input"), true);
        if(!$data){
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Données invalides"
            ]);
            return;
        }

        $success = $this->model->create($data);
        echo json_encode([
            "success" => $success
        ]);
    }

    /* =========================
       MODIFIER UNE COURSE
    ========================== */
    public function update(int $id){
        $data = json_decode(file_get_contents("php://input"), true);
        if(!$data){
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Données invalides"
            ]);
            return;
        }

        $success = $this->model->update($id, $data);
        echo json_encode([
            "success" => $success
        ]);
    }

    /* =========================
       SUPPRIMER UNE COURSE
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
        $filters = json_decode(file_get_contents("php://input"), true) ?: [];
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
       COURSES D’UN CHEVAL
    ========================== */
    public function byHorse(int $cheval_id){
        $courses = $this->model->byHorse($cheval_id);
        echo json_encode([
            "success" => true,
            "data" => $courses
        ]);
    }

    /* =========================
       DERNIÈRES COURSES AJOUTÉES
    ========================== */
    public function latest(int $limit = 5){
        $courses = $this->model->latest($limit);
        echo json_encode([
            "success" => true,
            "data" => $courses
        ]);
    }
}
?>