<?php
namespace App\Models;

// Assurez-vous que la classe Database est incluse ou accessible via autoloading
// use App\Core\Database; // Exemple, ajustez si nécessaire

class Item {
    private $db;

    public function __construct() {
        // Supposons que Database::getInstance() initialise correctement PDO
        $this->db = \App\Models\Database::getInstance(); 
    }

    // Fonction utilitaire pour décoder les ingrédients après récupération
    private function processItem($item) {

        // 1. Initialiser le tableau vide si la clé n'existe pas ou est null
        if (!isset($item['ingredients'])) {
            $item['ingredients'] = '[]'; // Force une chaîne JSON vide si la colonne est NULL
        }

        // 2. Décoder si c'est une chaîne
        if ($item && is_string($item['ingredients'])) {
            $item['ingredients'] = json_decode($item['ingredients'], true);

            // 3. VÉRIFICATION CRITIQUE : Si le décodage échoue (null ou non-array), le forcer à []
            if (!is_array($item['ingredients'])) {
                $item['ingredients'] = [];
            }
} 
        // 4. Au cas où ce serait un objet non-array après décodage (très rare, mais sécuritaire)
        if (!is_array($item['ingredients'])) {
            $item['ingredients'] = [];
        }

        return $item;
    }

    // READ All
    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM items ORDER BY id DESC");
        $items = $stmt->fetchAll();
        
        // Décoder les ingrédients pour tous les items
        return array_map([$this, 'processItem'], $items);
    }

    // READ One
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        // Décoder les ingrédients
        return $this->processItem($item);
    }

    // CREATE
    public function create($data) {
        // Préparer les ingrédients pour le stockage : encoder en JSON
        $ingredients = isset($data['ingredients']) ? json_encode($data['ingredients']) : '[]';

        $stmt = $this->db->prepare("INSERT INTO items (name, description, ingredients) VALUES (?, ?, ?)");
        // Note: Assurez-vous que la colonne 'ingredients' existe dans votre table 'items' !
        return $stmt->execute([$data['name'], $data['description'], $ingredients]);
    }

    // UPDATE
    public function update($id, $data) {
        // Préparer les ingrédients pour le stockage : encoder en JSON
        $ingredients = isset($data['ingredients']) ? json_encode($data['ingredients']) : '[]';

        $stmt = $this->db->prepare("UPDATE items SET name = ?, description = ?, ingredients = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['description'], $ingredients, $id]);
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM items WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
