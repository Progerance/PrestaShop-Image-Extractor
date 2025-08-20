# PrestaShop Image Extractor / Organizer

Un script PHP pour extraire et organiser automatiquement toutes les images originales uploadées par l'administrateur dans PrestaShop, en les structurant par produit pour faciliter l'exportation ou le traitement externe en masse.

## 🎯 Objectif

Ce script résout un problème courant : **extraire et organiser les images originales de produits PrestaShop** dispersées dans l'arborescence complexe du CMS pour un accès simplifié.

Particulièrement utile pour :
- Exporter toutes les images produits vers des marketplaces externes
- Faire du traitement d'images en masse (détourage, redimensionnement)
- Créer des sauvegardes organisées des visuels produits
- Préparer des catalogues pour impression ou autres supports
- Migrer vers d'autres plateformes e-commerce

## 📁 Structure générée

```
/img/products-to-process/
├── product-123/
│   ├── cover_image_456.jpg
│   ├── image_457.jpg
│   └── image_458.png
├── product-124/
│   ├── cover_image_789.jpg
│   └── image_790.jpg
└── product-125/
    └── cover_image_123.png
```

## ✨ Fonctionnalités

- ✅ **Extraction des images originales** depuis l'arborescence PrestaShop complexe
- ✅ **Organisation automatique** par ID produit dans des dossiers séparés
- ✅ **Conservation des images originales** (copie, pas déplacement)
- ✅ **Identification des images principales** (préfixe `cover_`)
- ✅ **Support tous formats** (JPG, PNG, GIF, WebP)
- ✅ **Gestion des erreurs** avec rapport détaillé
- ✅ **Compatible PrestaShop 1.6 et 1.7**

## 🚀 Installation

### Prérequis

**Extensions PHP requises :**
```bash
# Ubuntu/Debian
sudo apt install php-cli php-mysql php-pdo

# CentOS/RHEL
sudo yum install php-cli php-mysql php-pdo
```

**Vérification :**
```bash
php -r "echo 'PDO: ' . (extension_loaded('pdo') ? 'OK' : 'NON') . \"\n\";"
php -r "echo 'MySQL: ' . (extension_loaded('pdo_mysql') ? 'OK' : 'NON') . \"\n\";"
```

### Configuration

1. **Téléchargez le script** `organize_images.php`

2. **Modifiez la configuration** (lignes 9-17) :
```php
// Chemin vers votre installation PrestaShop
define('PS_ROOT_DIR', '/var/www/vhosts/monsite.com/httpdocs');

// Configuration base de données
$db_host = 'localhost';
$db_name = 'prestashop_db';
$db_user = 'username';
$db_pass = 'password';
$db_prefix = 'ps_'; // Préfixe des tables PrestaShop
```

3. **Trouvez le bon chemin PrestaShop** :
```bash
# Si vous placez le script à la racine de PrestaShop
define('PS_ROOT_DIR', __DIR__);

# Ou utilisez ce mini-script pour trouver le chemin
php -r "echo 'Chemin actuel : ' . realpath('.') . \"\n\";"
```

## 🎮 Utilisation

### Méthode 1 : Ligne de commande (recommandée)
```bash
# Placez-vous dans le dossier du script
cd /var/www/vhosts/monsite.com/httpdocs

# Lancez le script
php organize_images.php
```

### Méthode 2 : Via navigateur
```
http://monsite.com/organize_images.php
```
⚠️ **Attention** : Supprimez le script après utilisation pour des raisons de sécurité !

## 📊 Exemple de sortie

```
Début du traitement...
Nombre total d'images à traiter : 1247

Traitement produit ID: 123
  ✓ Image 456 copiée
  ✓ Image 457 copiée
  ✓ Image 458 copiée

Traitement produit ID: 124
  ✓ Image 789 copiée
  ✗ Image 790 introuvable

==================================================
RÉSUMÉ DU TRAITEMENT
==================================================
Produits traités : 156
Images copiées : 1205
Erreurs : 42

Images organisées dans : /var/www/vhosts/monsite.com/httpdocs/img/products-to-process
```

## 🔧 Cas d'usage courants

### 1. Export vers marketplaces
Les images sont extraites dans un format facilement exploitable pour :
- Amazon, eBay, Cdiscount
- Comparateurs de prix
- Réseaux sociaux (catalogues Facebook, Instagram)

### 2. Workflow d'exportation recommandé
1. **Extraire** → `php organize_images.php`
2. **Traiter** → Appliquez vos modifications sur chaque dossier produit
3. **Exporter** → Utilisez les images organisées pour vos besoins externes

### 3. Intégration CI/CD
```bash
#!/bin/bash
# Script d'automatisation
php organize_images.php
python bulk_background_removal.py
# ... autres traitements
```

## 🐛 Résolution des problèmes

### Erreur "could not find driver"
```bash
# Installez l'extension MySQL pour PHP
sudo apt install php-mysql php-pdo-mysql
sudo systemctl restart php7.4-fpm
```

### Erreur de permissions
```bash
# Donnez les bonnes permissions au dossier de destination
chmod 755 /var/www/vhosts/monsite.com/httpdocs/img/products-to-process
```

### Images introuvables
- Vérifiez le chemin `PS_ROOT_DIR`
- Vérifiez que les images existent dans `/img/p/`
- Consultez le log des erreurs généré

### Problèmes de mémoire (beaucoup d'images)
```php
// Ajoutez en début de script
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);
```

## 🔒 Sécurité

- ⚠️ **Ne laissez jamais ce script accessible via web** en production
- 🔐 Utilisez des **identifiants de base de données en lecture seule** si possible
- 🗑️ **Supprimez le script** après utilisation
- 📝 **Testez d'abord** sur un environnement de développement

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- 🐛 Signaler des bugs
- 💡 Proposer des améliorations
- 📖 Améliorer la documentation
- ⭐ Partager vos cas d'usage

## 📝 Licence

MIT License - Vous êtes libre d'utiliser, modifier et redistribuer ce script.

## 🙏 Remerciements

Script créé pour la communauté PrestaShop, testé sur des installations avec plus de 10 000 produits.

---

**💡 Astuce** : Combinez ce script avec des solutions IA locales comme REMBG pour un workflow de détourage 100% gratuit et automatisé !
