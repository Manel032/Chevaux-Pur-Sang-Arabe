<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion des requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclure les classes
require_once "models/Cheval.php";
require_once "models/Owner.php";
require_once "models/Jockey.php";

require_once "controllers/ChevalController.php";
require_once "controllers/OwnerController.php";
require_once "controllers/JockeyController.php";

// Connexion à la base de données
$dsn = "mysql:host=localhost;dbname=chevaux_arabes;charset=utf8";
$user = "root";
$pass = "";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Connexion DB échouée: " . $e->getMessage()]);
    exit();
}

// Instancier les modèles
$chevalModel = new Cheval($pdo);
$ownerModel  = new Owner($pdo);
$jockeyModel = new Jockey($pdo);

// Instancier les controllers
$chevalCtrl = new ChevalController($chevalModel);
$ownerCtrl  = new OwnerController($ownerModel);
$jockeyCtrl = new JockeyController($jockeyModel);

// Récupérer l'URL et la méthode
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Supprimer les paramètres GET
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$segments = explode('/', trim($requestPath, '/'));

// Exemple d'URL: /api/chevaux/1
$resource = $segments[1] ?? null;
$id = isset($segments[2]) ? (int)$segments[2] : null;

// ROUTES
switch ($resource) {

    /* ================= CHEVAUX ================= */
    case "chevaux":
        switch ($method) {
            case "GET":
                if ($id) $chevalCtrl->show($id);
                else $chevalCtrl->index();
                break;
            case "POST":
                $chevalCtrl->store();
                break;
            case "PUT":
                if ($id) $chevalCtrl->update($id);
                break;
            case "DELETE":
                if ($id) $chevalCtrl->destroy($id);
                break;
        }
        break;

    /* ================= OWNERS ================= */
    case "owners":
        switch ($method) {
            case "GET":
                if ($id) $ownerCtrl->show($id);
                else $ownerCtrl->index();
                break;
            case "POST":
                $ownerCtrl->store();
                break;
            case "PUT":
                if ($id) $ownerCtrl->update($id);
                break;
            case "DELETE":
                if ($id) $ownerCtrl->destroy($id);
                break;
        }
        break;

    /* ================= JOCKEYS ================= */
    case "jockeys":
        switch ($method) {
            case "GET":
                if ($id) $jockeyCtrl->show($id);
                else $jockeyCtrl->index();
                break;
            case "POST":
                $jockeyCtrl->store();
                break;
            case "PUT":
                if ($id) $jockeyCtrl->update($id);
                break;
            case "DELETE":
                if ($id) $jockeyCtrl->destroy($id);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Ressource non trouvée"]);
        break;
}
?>