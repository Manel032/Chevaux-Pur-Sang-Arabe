<?php
// JockeyController.php - Controller for Jockey

require_once 'Jockey.php';

class JockeyController {
    
    public function index() {
        try {
            $jockeys = Jockey::getAll();
            $this->jsonResponse(array('success' => true, 'data' => $jockeys));
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function show($id) {
        try {
            $jockey = Jockey::getById($id);
            if ($jockey) {
                $participations = Jockey::getParticipations($id);
                $this->jsonResponse(array('success' => true, 'data' => $jockey, 'participations' => $participations));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Jockey non trouvé'), 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function store($data) {
        try {
            if (empty($data['nom'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Le nom du jockey est obligatoire'), 400);
                return;
            }

            $id = Jockey::create($data);
            $this->jsonResponse(array('success' => true, 'message' => 'Jockey créé avec succès', 'id' => $id), 201);
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function update($id, $data) {
        try {
            if (empty($data['nom'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Le nom du jockey est obligatoire'), 400);
                return;
            }

            $success = Jockey::update($id, $data);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Jockey mis à jour avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de mettre à jour le jockey'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function destroy($id) {
        try {
            $success = Jockey::delete($id);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Jockey supprimé avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de supprimer le jockey'), 400);
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
