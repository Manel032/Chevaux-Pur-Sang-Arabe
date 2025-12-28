<?php
header("Content-Type: application/json");

require_once "Database.php";
require_once "Cheval.php";
require_once "Owner.php";
require_once "Jockey.php";
require_once "Course.php";
require_once "ChevalController.php";
require_once "OwnerController.php";
require_once "JockeyController.php";
require_once "CourseController.php";

// Connexion à la DB
$db = Database::connect();

// Instanciation des modèles et controllers
$chevalController = new ChevalController(new Cheval($db));
$ownerController  = new OwnerController(new Owner($db));
$jockeyController = new JockeyController(new Jockey($db));
$courseController = new CourseController(new Course($db));

// URL et méthode
$uri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));
$method = $_SERVER['REQUEST_METHOD'];
$resource = $uri[1] ?? null;
$id       = isset($uri[2]) && is_numeric($uri[2]) ? (int)$uri[2] : null;

// ROUTES API
switch($resource){

    /* ===== OWNERS ===== */
    case "owners":
        if(isset($uri[2]) && $uri[2] === "search") $ownerController->search();
        elseif(isset($uri[2]) && $uri[2] === "stats") $ownerController->stats();
        elseif(isset($uri[2]) && $uri[2] === "latest") $ownerController->latest();
        elseif(isset($uri[2]) && $uri[2] === "horses" && isset($uri[3])) $ownerController->horses((int)$uri[3]);
        else {
            switch($method){
                case "GET":
                    $id ? $ownerController->show($id) : $ownerController->index();
                    break;
                case "POST": $ownerController->store(); break;
                case "PUT": $id ? $ownerController->update($id) : null; break;
                case "DELETE": $id ? $ownerController->destroy($id) : null; break;
            }
        }
        break;

    /* ===== JOCKEYS ===== */
    case "jockeys":
        if(isset($uri[2]) && $uri[2] === "search") $jockeyController->search();
        elseif(isset($uri[2]) && $uri[2] === "stats") $jockeyController->stats();
        elseif(isset($uri[2]) && $uri[2] === "latest") $jockeyController->latest();
        elseif(isset($uri[2]) && $uri[2] === "horses" && isset($uri[3])) $jockeyController->horses((int)$uri[3]);
        else {
            switch($method){
                case "GET":
                    $id ? $jockeyController->show($id) : $jockeyController->index();
                    break;
                case "POST": $jockeyController->store(); break;
                case "PUT": $id ? $jockeyController->update($id) : null; break;
                case "DELETE": $id ? $jockeyController->destroy($id) : null; break;
            }
        }
        break;

    /* ===== CHEVAUX ===== */
    case "chevaux":
        if(isset($uri[2]) && $uri[2] === "search") $chevalController->search();
        elseif(isset($uri[2]) && $uri[2] === "stats") $chevalController->stats();
        elseif(isset($uri[2]) && $uri[2] === "latest") $chevalController->latest();
        else {
            switch($method){
                case "GET": $id ? $chevalController->show($id) : $chevalController->index(); break;
                case "POST": $chevalController->store(); break;
                case "PUT": $id ? $chevalController->update($id) : null; break;
                case "DELETE": $id ? $chevalController->destroy($id) : null; break;
            }
        }
        break;

    /* ===== COURSES ===== */
    case "courses":
        if(isset($uri[2]) && $uri[2] === "search") $courseController->search();
        elseif(isset($uri[2]) && $uri[2] === "stats") $courseController->stats();
        elseif(isset($uri[2]) && $uri[2] === "latest") $courseController->latest();
        elseif(isset($uri[2]) && $uri[2] === "byHorse" && isset($uri[3])) $courseController->byHorse((int)$uri[3]);
        else {
            switch($method){
                case "GET": $id ? $courseController->show($id) : $courseController->index(); break;
                case "POST": $courseController->store(); break;
                case "PUT": $id ? $courseController->update($id) : null; break;
                case "DELETE": $id ? $courseController->destroy($id) : null; break;
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["success"=>false, "error"=>"Ressource non trouvée"]);
        break;
}
?>