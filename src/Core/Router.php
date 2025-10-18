<?php

namespace App\Core;

class Router {
    public function run() {
        // Tente de récupérer l'URL à partir de la variable GET 'url' (utilisée par le .htaccess)
        $url = $_GET['url'] ?? '';

        // *** CORRECTION DE REPLI POUR LE SERVEUR PHP INTEGRE ***
        // Si $_GET['url'] est vide (ce qui arrive quand le serveur PHP ne passe que la racine), 
        // on tente d'utiliser REQUEST_URI et on retire le préfixe '/public' si nécessaire.
        if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
            $url = $_SERVER['REQUEST_URI'];
            // On retire le dossier public si l'URI le contient et la barre oblique
            $url = preg_replace('/^\/public\/?/', '', $url);
            // Si l'URL commence par le chemin racine '/', on le retire pour avoir 'items/create'
            if (strpos($url, '/') === 0) {
                $url = substr($url, 1);
            }
        }
        // *******************************************************

        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $urlParts = explode('/', $url);

        // Définir le contrôleur
        $controllerSegment = $urlParts[0] ?? '';
        $methodSegment = $urlParts[1] ?? '';
        $id = $urlParts[2] ?? null;

        if (empty($controllerSegment) || strtolower($controllerSegment) === 'items' || strtolower($controllerSegment) === 'item') {
            
            // Si c'est la racine ou une route d'item, le contrôleur est toujours ItemController
            $controllerName = 'ItemController';
            
            // La méthode est le segment 1. Cela inclut 'createPartial' ou 'editPartial'. Sinon 'index'.
            $methodName = !empty($methodSegment) ? $methodSegment : 'index'; 
            
        } else {
            // Logique pour d'autres contrôleurs (reste inchangée)
            $controllerName = ucfirst($controllerSegment) . 'Controller';
            $methodName = !empty($methodSegment) ? $methodSegment : 'index';
        }

        $controllerClassName = "App\\Controllers\\" . $controllerName;

        if (class_exists($controllerClassName)) {
            $controller = new $controllerClassName();
            if (method_exists($controller, $methodName)) {
                // Appeler la méthode en passant l'ID. L'ID sera null pour les routes sans ID.
                $controller->$methodName($id);
            } else {
                http_response_code(404);
                echo "Méthode non trouvée : " . htmlspecialchars($methodName); // Afficher la méthode pour le débogage
            }
        } else {
            http_response_code(404);
            echo "Contrôleur non trouvé : " . htmlspecialchars($controllerName); // Afficher le contrôleur pour le débogage
        }
    }
}
