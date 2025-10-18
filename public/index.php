<?php
// CRUCIAL : Activation de l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chemin vers l'autoloader de Composer (doit être au niveau du dossier public/..)
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Définir l'URL de base (pour le serveur PHP intégré, elle est simplement '/')
$baseUrl = '/'; 

// Démarrer le routeur
use App\Core\Router;

$router = new Router();
$router->run();
