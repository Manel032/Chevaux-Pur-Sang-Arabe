<?php
// api.php - API Router

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import models and controllers
require_once 'database.php';
require_once 'Cheval.php';
require_once 'ChevalControler.php';
require_once 'Owner.php';
require_once 'OwnerController.php';
require_once 'Jockey.php';
require_once 'JockeyController.php';
require_once 'Course.php';
require_once 'CourseController.php';

// Parse query action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Retrieve request body if JSON
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);
if ($inputData === null) {
    $inputData = $_POST; // Fallback to standard POST
}

// Router dispatcher
switch ($action) {
    // --- CHEVAL ACTIONS ---
    case 'get_chevaux':
        $filters = array(
            'race' => isset($_GET['race']) ? $_GET['race'] : '',
            'sexe' => isset($_GET['sexe']) ? $_GET['sexe'] : '',
            'search' => isset($_GET['search']) ? $_GET['search'] : ''
        );
        $controller = new ChevalControler();
        $controller->index($filters);
        break;

    case 'get_cheval':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new ChevalControler();
        $controller->show($id);
        break;

    case 'create_cheval':
        $controller = new ChevalControler();
        $controller->store($inputData);
        break;

    case 'update_cheval':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new ChevalControler();
        $controller->update($id, $inputData);
        break;

    case 'delete_cheval':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new ChevalControler();
        $controller->destroy($id);
        break;

    case 'get_parents_lists':
        $excludeId = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : null;
        $controller = new ChevalControler();
        $controller->getParentsLists($excludeId);
        break;

    // --- OWNER ACTIONS ---
    case 'get_owners':
        $controller = new OwnerController();
        $controller->index();
        break;

    case 'get_owner':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new OwnerController();
        $controller->show($id);
        break;

    case 'create_owner':
        $controller = new OwnerController();
        $controller->store($inputData);
        break;

    case 'update_owner':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new OwnerController();
        $controller->update($id, $inputData);
        break;

    case 'delete_owner':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new OwnerController();
        $controller->destroy($id);
        break;

    // --- JOCKEY ACTIONS ---
    case 'get_jockeys':
        $controller = new JockeyController();
        $controller->index();
        break;

    case 'get_jockey':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new JockeyController();
        $controller->show($id);
        break;

    case 'create_jockey':
        $controller = new JockeyController();
        $controller->store($inputData);
        break;

    case 'update_jockey':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new JockeyController();
        $controller->update($id, $inputData);
        break;

    case 'delete_jockey':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new JockeyController();
        $controller->destroy($id);
        break;

    // --- COURSE ACTIONS ---
    case 'get_courses':
        $controller = new CourseController();
        $controller->index();
        break;

    case 'get_course':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new CourseController();
        $controller->show($id);
        break;

    case 'create_course':
        $controller = new CourseController();
        $controller->store($inputData);
        break;

    case 'update_course':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new CourseController();
        $controller->update($id, $inputData);
        break;

    case 'delete_course':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new CourseController();
        $controller->destroy($id);
        break;

    case 'add_participation':
        $controller = new CourseController();
        $controller->addParticipation($inputData);
        break;

    case 'remove_participation':
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        $cheval_id = isset($_GET['cheval_id']) ? (int)$_GET['cheval_id'] : 0;
        $controller = new CourseController();
        $controller->removeParticipation($course_id, $cheval_id);
        break;

    default:
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Action API non reconnue'));
        break;
}
