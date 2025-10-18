<?php
namespace App\Controllers;

use App\Models\Item;

class ItemController {

    // Affiche la liste des items
    public function index() {
        $itemModel = new Item();
        $items = $itemModel->findAll();
        $this->render('items/index', ['items' => $items]);
    }

    // Affiche le formulaire de création (gardé, mais non utilisé par la modale)
    public function create() {
        $this->render('items/form', ['action' => '/items/store', 'item' => null]);
    }

    /**
     * [NOUVEAU] Action AJAX pour charger le formulaire de création sans layout.
     * Cette méthode est appelée par le JavaScript de index.phtml.
     */
    public function createPartial() {
        // Le JavaScript utilise cette route, elle doit renvoyer UNIQUEMENT le formulaire.
        $this->renderPartial('items/form', ['action' => '/items/store', 'item' => null]);
    }

    // Enregistre un nouvel item
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemModel = new Item();
            $itemModel->create($_POST);
            
            // [IMPORTANT] Pour l'AJAX : Au lieu de rediriger, nous renvoyons une réponse 200/201.
            // Le JavaScript dans index.phtml détecte la redirection ou le succès et recharge la page.
            http_response_code(201); // 201 Created est un bon code pour le succès.
            // header('Location: /'); // Commenté car la redirection est gérée côté client (JS)
            // exit; // Retire l'exit si on veut laisser PHP terminer proprement
        }
    }

    // Affiche le formulaire d'édition (gardé, mais non utilisé par la modale)
    public function edit($id) {
        $itemModel = new Item();
        $item = $itemModel->find($id);
        $this->render('items/form', ['action' => "/items/update/{$id}", 'item' => $item]);
    }
    
    /**
     * [NOUVEAU] Action AJAX pour charger le formulaire d'édition sans layout.
     * Cette méthode est appelée par le JavaScript de index.phtml.
     */
    public function editPartial($id) {
        $itemModel = new Item();
        $item = $itemModel->find($id);
        if (!$item) { 
            http_response_code(404); 
            echo "Item non trouvé";
            return; 
        }
        // Le JavaScript utilise cette route, elle doit renvoyer UNIQUEMENT le formulaire.
        $this->renderPartial('items/form', ['action' => "/items/update/{$id}", 'item' => $item]);
    }

    // Met à jour un item
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemModel = new Item();
            $itemModel->update($id, $_POST);
            
            // [IMPORTANT] Pour l'AJAX : Renvoyer un succès au lieu d'une redirection PHP.
            http_response_code(200);
            // header('Location: /'); // Commenté
            // exit;
        }
    }

    // Supprime un item
    public function destroy($id) {
        $itemModel = new Item();
        $itemModel->delete($id);
        header('Location: /'); // La suppression peut rester une redirection classique
        exit;
    }

    // Fonction utilitaire pour charger les vues AVEC le layout (header/footer)
    protected function render($view, $data = []) {
        extract($data);

        require __DIR__ . "/../../views/layouts/header.phtml";
        
        require __DIR__ . "/../../views/{$view}.phtml";
        
        require __DIR__ . "/../../views/layouts/footer.phtml";
    }

    /**
     * [NOUVEAU] Fonction utilitaire pour charger les vues SANS le layout (pour les requêtes AJAX/modales).
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../views/{$view}.phtml";
    }
}
