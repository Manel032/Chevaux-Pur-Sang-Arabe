<?php
// CourseController.php - Controller for Course (Races)

require_once 'Course.php';

class CourseController {
    
    public function index() {
        try {
            $courses = Course::getAll();
            $this->jsonResponse(array('success' => true, 'data' => $courses));
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function show($id) {
        try {
            $course = Course::getById($id);
            if ($course) {
                $participations = Course::getParticipations($id);
                $this->jsonResponse(array('success' => true, 'data' => $course, 'participations' => $participations));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Course non trouvée'), 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function store($data) {
        try {
            if (empty($data['nom']) || empty($data['date_course']) || empty($data['lieu']) || empty($data['distance'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Les champs nom, date, lieu et distance sont obligatoires'), 400);
                return;
            }

            $id = Course::create($data);
            $this->jsonResponse(array('success' => true, 'message' => 'Course créée avec succès', 'id' => $id), 201);
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function update($id, $data) {
        try {
            if (empty($data['nom']) || empty($data['date_course']) || empty($data['lieu']) || empty($data['distance'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Les champs nom, date, lieu et distance sont obligatoires'), 400);
                return;
            }

            $success = Course::update($id, $data);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Course mise à jour avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de mettre à jour la course'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function destroy($id) {
        try {
            $success = Course::delete($id);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Course supprimée avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de supprimer la course'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Add horse participation to race
    public function addParticipation($data) {
        try {
            if (empty($data['course_id']) || empty($data['cheval_id'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'L\'ID de la course et du cheval sont requis'), 400);
                return;
            }

            $success = Course::addParticipation($data);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Participation enregistrée avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Erreur lors de l\'enregistrement de la participation'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Remove horse from race
    public function removeParticipation($course_id, $cheval_id) {
        try {
            $success = Course::removeParticipation($course_id, $cheval_id);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Participation supprimée avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Erreur lors du retrait du cheval'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    private function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
