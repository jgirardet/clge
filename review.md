# Code Review - 16/08/2026

## 🟡 Mineur

### 🟡 Mineur - CSS inline dans shortcode
Le shortcode `clge_cal_events_shortcode()` inclut du CSS inline (lignes 71-115). Déplacer ce CSS dans un fichier séparé. Référence: `inc/shortcodes.php:71-115`

### 🟡 Mineur - Duplication de code dans all_events.php
Le template `all_events.php` duplique beaucoup de styles inline. Extraire ces styles dans une feuille de style commune. Référence: `templates/all_events.php:12-63`

## 🔵 Amélioration

### 🔵 Amélioration - Code dupliqué dans database.php
Les fonctions `clge_create_event()` et `clge_update_event()` ont des parties similaires pour le traitement des dates. Extraire cette logique dans une fonction helper. Référence: `inc/database.php:83-110,142-182`

### 🔵 Amélioration - Utilisation de DateTimeImmutable
Remplacer `DateTime` par `DateTimeImmutable` pour éviter les modifications accidentelles des objets DateTime. Référence: Divers fichiers

### 🔵 Amélioration - Ajouter des docblocks manquants
Plusieurs fonctions dans `inc/clge-admin-page.php` n'ont pas de docblocks. Ajouter des documentations PHPDoc. Référence: `inc/clge-admin-page.php:28-140`

### 🔵 Amélioration - Utilisation de wp_send_json_success/error
Les fonctions AJAX dans `inc/clge-admin-page.php` utilisent `wp_die()` directement. Utiliser `wp_send_json_success()` et `wp_send_json_error()` pour plus de cohérence. Référence: `inc/clge-admin-page.php:32,43,77,87,103,120,138`

### 🔵 Amélioration - Optimisation des requêtes SQL
La fonction `clge_get_all_events()` charge tous les événements puis les filtre. Ajouter une clause WHERE pour ne charger que les événements futurs si nécessaire. Référence: `inc/database.php:144-145`

### 🔵 Amélioration - Ajouter des indexes sur la table
La table `clge_cal_events` n'a pas d'index sur les colonnes fréquemment interrogées comme `debut` et `fin`. Ajouter ces indexes. Référence: `inc/database.php:24-35`

### 🔵 Amélioration - Utilisation de prepare() pour les ALTER TABLE
Les requêtes ALTER TABLE dans `clge_migrate_add_alias_description()` devraient utiliser `$wpdb->prepare()` même si c'est moins critique. Référence: `inc/database.php:73,84`