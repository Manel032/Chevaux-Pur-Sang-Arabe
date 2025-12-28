<?php
header("Content-Type: application/json; charset=UTF-8");

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoload
spl_autoload_register(function($class){
    $paths = [
        __DIR__ . "/config/{$class}.php",
        __DIR__ . "/models/{$class}.php",
        __DIR__ . "/controllers/{$class}.php"
    ];
    foreach ($paths as $file) {
        if(file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// DB
$db = Database::getInstance()->connect();

// Models
$chevalModel = new Cheval($db);
$ownerModel  = new Owner($db);
$jockeyModel = new Jockey($db);
$courseModel = new Course($db);

// Controllers
$chevalController = new ChevalController($chevalModel);
$ownerController  = new OwnerController($ownerModel);
$jockeyController = new JockeyController($jockeyModel);
$courseController = new CourseController($courseModel);

// URI et méthode
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode("/", trim($uri, "/"));
$method = $_SERVER['REQUEST_METHOD'];

$resource = $uri[1] ?? null; 
$action   = $uri[2] ?? null; 
$id       = $uri[3] ?? null; 

// ROUTES complètes
switch($resource){
    case "chevaux":
        switch($method){
            case "GET":
                if($action === "latest") $chevalController->latest($id ?? 5);
                elseif($action === "stats") $chevalController->stats();
                elseif($id) $chevalController->show((int)$id);
                else $chevalController->index();
                break;
            case "POST":
                if($action === "search") $chevalController->search();
                else $chevalController->store();
                break;
            case "PUT":
                if($id) $chevalController->update((int)$id);
                break;
            case "DELETE":
                if($id) $chevalController->destroy((int)$id);
                break;
            default: http_response_code(405);
        }
        break;

    case "owners":
        switch($method){
            case "GET":
                if($action === "latest") $ownerController->latest($id ?? 5);
                elseif($action === "stats") $ownerController->stats();
                elseif($action === "horses" && $id) $ownerController->horses((int)$id);
                elseif($id) $ownerController->show((int)$id);
                else $ownerController->index();
                break;
            case "POST":
                if($action === "search") $ownerController->search();
                else $ownerController->store();
                break;
            case "PUT":
                if($id) $ownerController->update((int)$id);
                break;
            case "DELETE":
                if($id) $ownerController->destroy((int)$id);
                break;
            default: http_response_code(405);
        }
        break;

    case "jockeys":
        switch($method){
            case "GET":
                if($action === "latest") $jockeyController->latest($id ?? 5);
                elseif($action === "stats") $jockeyController->stats();
                elseif($action === "horses" && $id) $jockeyController->horses((int)$id);
                elseif($id) $jockeyController->show((int)$id);
                else $jockeyController->index();
                break;
            case "POST":
                if($action === "search") $jockeyController->search();
                else $jockeyController->store();
                break;
            case "PUT":
                if($id) $jockeyController->update((int)$id);
                break;
            case "DELETE":
                if($id) $jockeyController->destroy((int)$id);
                break;
            default: http_response_code(405);
        }
        break;

    case "courses":
        switch($method){
            case "GET":
                if($action === "latest") $courseController->latest($id ?? 5);
                elseif($action === "stats") $courseController->stats();
                elseif($action === "byHorse" && $id) $courseController->byHorse((int)$id);
                elseif($id) $courseController->show((int)$id);
                else $courseController->index();
                break;
            case "POST":
                if($action === "search") $courseController->search();
                else $courseController->store();
                break;
            case "PUT":
                if($id) $courseController->update((int)$id);
                break;
            case "DELETE":
                if($id) $courseController->destroy((int)$id);
                break;
            default: http_response_code(405);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["success"=>false,"error"=>"Ressource non trouvée"]);
}
