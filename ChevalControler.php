<?php
// ChevalControler.php - Controller for Cheval (single 'l' in Controler to match requested structure)

require_once 'Cheval.php';

class ChevalControler {
    
    // Handle list request
    public function index($filters = []) {
        try {
            $chevaux = Cheval::getAll($filters);
            $this->jsonResponse(array('success' => true, 'data' => $chevaux));
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Handle view detail request
    public function show($id) {
        try {
            $cheval = Cheval::getById($id);
            if ($cheval) {
                $pedigree = Cheval::getPedigreeTree($id);
                $this->jsonResponse(array('success' => true, 'data' => $cheval, 'pedigree' => $pedigree));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Cheval non trouvé'), 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Handle create request
    public function store($data) {
        try {
            if (empty($data['nom']) || empty($data['race']) || empty($data['sexe'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Les champs nom, race et sexe sont obligatoires'), 400);
                return;
            }

            $id = Cheval::create($data);
            $this->jsonResponse(array('success' => true, 'message' => 'Cheval créé avec succès', 'id' => $id), 210);
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Handle update request
    public function update($id, $data) {
        try {
            if (empty($data['nom']) || empty($data['race']) || empty($data['sexe'])) {
                $this->jsonResponse(array('success' => false, 'message' => 'Les champs nom, race et sexe sont obligatoires'), 400);
                return;
            }

            $success = Cheval::update($id, $data);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Cheval mis à jour avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de mettre à jour le cheval'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Handle delete request
    public function destroy($id) {
        try {
            $success = Cheval::delete($id);
            if ($success) {
                $this->jsonResponse(array('success' => true, 'message' => 'Cheval supprimé avec succès'));
            } else {
                $this->jsonResponse(array('success' => false, 'message' => 'Impossible de supprimer le cheval'), 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Get eligible fathers/mothers lists for dropdowns
    public function getParentsLists($excludeId = null) {
        try {
            $peres = Cheval::getEligibleFathers($excludeId);
            $meres = Cheval::getEligibleMothers($excludeId);
            $this->jsonResponse(array(
                'success' => true, 
                'peres' => $peres,
                'meres' => $meres
            ));
        } catch (Exception $e) {
            $this->jsonResponse(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    // Helper method to output json responses
    private function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
