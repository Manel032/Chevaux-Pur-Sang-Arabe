<?php
// OwnerController.php - Controller for Owner

require_once 'Owner.php';

class OwnerController {
    
    public function index() {
        try {
            $owners = Owner::getAll();
            $this->jsonResponse(array('success' => true, 'data' => $owners));
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function show($id) {
        try {
            $owner = Owner::getById($id);
            if ($owner) {
                $horses = Owner::getHorses($id);
                $this->jsonResponse(array('success' => true, 'data' => $owner, 'horses' => $horses));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Propriétaire non trouvé'), 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function store($data) {
        try {
            if (empty($data['nom'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Le nom est obligatoire'), 400);
                return;
            }

            $id = Owner::create($data);
            $this->jsonResponse(array('success' => true, 'message' => 'Propriétaire créé avec succès', 'id' => $id), 201);
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function update($id, $data) {
        try {
            if (empty($data['nom'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Le nom est obligatoire'), 400);
                return;
            }

            $success = Owner::update($id, $data);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Propriétaire mis à jour avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de mettre à jour le propriétaire'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    public function destroy($id) {
        try {
            $success = Owner::delete($id);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Propriétaire supprimé avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de supprimer le propriétaire'), 400);
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
