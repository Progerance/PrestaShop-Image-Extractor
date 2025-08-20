<?php
/**
 * Script pour organiser les images PrestaShop par ID produit
 * Copie les images originales dans un dossier structuré pour traitement
 */

// Configuration
define('PS_ROOT_DIR', '/path/to/your/prestashop'); // Chemin vers votre installation PrestaShop
define('OUTPUT_DIR', PS_ROOT_DIR . '/img/products-to-process'); // Dossier de destination

// Configuration base de données
$db_host = 'localhost';
$db_name = 'prestashop_db';
$db_user = 'username';
$db_pass = 'password';
$db_prefix = 'ps_'; // Préfixe des tables PrestaShop

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer le dossier de destination s'il n'existe pas
    if (!is_dir(OUTPUT_DIR)) {
        mkdir(OUTPUT_DIR, 0755, true);
    }
    
    // Requête pour récupérer tous les produits avec leurs images
    $query = "
        SELECT DISTINCT 
            p.id_product,
            p.reference,
            pi.id_image,
            pi.cover
        FROM {$db_prefix}product p
        INNER JOIN {$db_prefix}image pi ON p.id_product = pi.id_product
        WHERE p.active = 1
        ORDER BY p.id_product, pi.cover DESC, pi.position ASC
    ";
    
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $processed_products = 0;
    $processed_images = 0;
    $errors = [];
    
    echo "Début du traitement...\n";
    echo "Nombre total d'images à traiter : " . count($products) . "\n\n";
    
    $current_product = null;
    
    foreach ($products as $row) {
        $product_id = $row['id_product'];
        $image_id = $row['id_image'];
        $is_cover = $row['cover'];
        
        // Nouveau produit
        if ($current_product !== $product_id) {
            $current_product = $product_id;
            $processed_products++;
            echo "Traitement produit ID: $product_id\n";
            
            // Créer le dossier pour ce produit
            $product_dir = OUTPUT_DIR . "/product-$product_id";
            if (!is_dir($product_dir)) {
                mkdir($product_dir, 0755, true);
            }
        }
        
        // Chercher les images dans l'arborescence PrestaShop
        $image_path = findImagePath($image_id);
        
        if ($image_path && file_exists($image_path)) {
            // Définir le nom de fichier de destination
            $extension = pathinfo($image_path, PATHINFO_EXTENSION);
            $prefix = $is_cover ? 'cover_' : '';
            $destination = "$product_dir/{$prefix}image_{$image_id}.$extension";
            
            // Copier l'image
            if (copy($image_path, $destination)) {
                $processed_images++;
                echo "  ✓ Image $image_id copiée\n";
            } else {
                $errors[] = "Erreur copie image $image_id pour produit $product_id";
                echo "  ✗ Erreur copie image $image_id\n";
            }
        } else {
            $errors[] = "Image $image_id introuvable pour produit $product_id";
            echo "  ✗ Image $image_id introuvable\n";
        }
    }
    
    // Résumé
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "RÉSUMÉ DU TRAITEMENT\n";
    echo str_repeat("=", 50) . "\n";
    echo "Produits traités : $processed_products\n";
    echo "Images copiées : $processed_images\n";
    echo "Erreurs : " . count($errors) . "\n";
    
    if (!empty($errors)) {
        echo "\nDétail des erreurs :\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    echo "\nImages organisées dans : " . OUTPUT_DIR . "\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}

/**
 * Trouve le chemin physique d'une image PrestaShop
 */
function findImagePath($image_id) {
    $base_path = PS_ROOT_DIR . '/img/p/';
    
    // PrestaShop organise les images par dossiers selon l'ID
    // Ex: ID 123 -> /1/2/3/123.jpg
    $id_str = (string)$image_id;
    $path_parts = str_split($id_str);
    
    // Construire le chemin
    $path = $base_path . implode('/', $path_parts) . '/';
    
    // Chercher les extensions possibles
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    foreach ($extensions as $ext) {
        $full_path = $path . $image_id . '.' . $ext;
        if (file_exists($full_path)) {
            return $full_path;
        }
    }
    
    return false;
}

/**
 * Fonction utilitaire pour créer un fichier de log des opérations
 */
function logOperation($message) {
    $log_file = OUTPUT_DIR . '/processing.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

?>
