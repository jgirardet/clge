# Code Review - 16/08/2026

## 🔴 Critique

### 🔴 Critique - Mot de passe non sanitizé dans Nextcloud
La fonction `clge_save_nextcloud_settings()` ne sanitize pas le mot de passe avant chiffrement. Utiliser `sanitize_text_field()` ou similaire. Référence: `inc/clge-nextcloud.php:105`

### 🔴 Critique - URL non validée dans downloadHTML()
La fonction `downloadHTML()` utilise cURL avec une URL hardcodée sans validation SSL. Ajouter `curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true)`. Référence: `inc/cngeformations.php:150-166`

### 🔴 Critique - Erreur fatale si SECURE_AUTH_KEY manquant
Le fichier `inc/clge-nextcloud.php` déclenche une erreur fatale si SECURE_AUTH_KEY n'est pas définie. Utiliser un message d'erreur plus gracieux. Référence: `inc/clge-nextcloud.php:17-22`

## 🟠 Majeur

### 🟠 Majeur - Variable globale $wpdb et $table_name déclarées trop tôt
Les variables globales `$wpdb` et `$table_name` sont déclarées au niveau global dans `inc/database.php:11-12` avant toute vérification. Déplacer ces déclarations dans les fonctions. Référence: `inc/database.php:11-12`

### 🟠 Majeur - Événement par défaut inutile
La fonction `clge_create_cal_events_table()` insère un événement par défaut inutile. Supprimer les lignes 37-50. Référence: `inc/database.php:37-50`

### 🟠 Majeur - Vérification de type manquante dans parseFormationsTable
La fonction `parseFormationsTable()` ne vérifie pas que `$spans->item(2)` existe avant de l'utiliser. Ajouter une vérification. Référence: `inc/cngeformations.php:75`

### 🟠 Majeur - Variable $$numeroEncours mal écrite
La ligne 42 dans `inc/cngeformations.php` utilise `$$numeroEncours` au lieu de `$numeroEncours`. Corriger cette typo. Référence: `inc/cngeformations.php:42`

### 🟠 Majeur - Gestion d'erreur manquante dans hx_add_event
La fonction `hx_add_event()` dans `inc/clge-admin-page.php:49-56` ne gère pas le cas où `check_admin_referer()` échoue. Ajouter un retour d'erreur. Référence: `inc/clge-admin-page.php:51`

### 🟠 Majeur - Point-virgule manquant dans hx_add_cnge_formation
La ligne 64 dans `inc/clge-admin-page.php` a un point-virgule après l'accolade fermante. Supprimer ce point-virgule superflu. Référence: `inc/clge-admin-page.php:64`

### 🟠 Majeur - Vérification de capability manquante dans hx_add_event
La fonction `hx_add_event()` ne vérifie pas les capabilities de l'utilisateur. Ajouter `current_user_can('manage_options')`. Référence: `inc/clge-admin-page.php:49-56`

### 🟠 Majeur - Vérification de capability manquante dans hx_delete_event
La fonction `hx_delete_event()` ne vérifie pas les capabilities de l'utilisateur. Ajouter `current_user_can('manage_options')`. Référence: `inc/clge-admin-page.php:82-88`

### 🟠 Majeur - Vérification de capability manquante dans hx_update_event
La fonction `hx_update_event()` ne vérifie pas les capabilities de l'utilisateur. Ajouter `current_user_can('manage_options')`. Référence: `inc/clge-admin-page.php:93-104`

### 🟠 Majeur - Vérification de capability manquante dans hx_update_event_description
La fonction `hx_update_event_description()` ne vérifie pas les capabilities de l'utilisateur. Ajouter `current_user_can('manage_options')`. Référence: `inc/clge-admin-page.php:109-120`

### 🟠 Majeur - Vérification de capability manquante dans hx_select_cnge_formations_list
La fonction `hx_select_cnge_formations_list()` ne vérifie pas les capabilities de l'utilisateur. Ajouter `current_user_can('manage_options')`. Référence: `inc/clge-admin-page.php:125-140`

### 🟠 Majeur - Sanitization manquante dans hx_add_event
La fonction `hx_add_event()` utilise directement `$_POST` sans sanitization. Utiliser `sanitize_text_field()` et autres fonctions de sanitization. Référence: `inc/clge-admin-page.php:52`

### 🟠 Majeur - Sanitization manquante dans hx_add_cnge_formation
La fonction `hx_add_cnge_formation()` utilise directement `$_POST['cnge']` sans validation. Valider que c'est bien du base64 avant de décoder. Référence: `inc/clge-admin-page.php:65-68`

### 🟠 Majeur - Email destinataire hardcodé
L'email destinataire `contact@clge.fr` est hardcodé dans `functions.php:342`. Rendre cela configurable via les options du thème. Référence: `functions.php:342`

### 🟠 Majeur - Pas de vérification de succès dans clge_create_event
La fonction `clge_create_event()` ne vérifie pas si l'insertion a réussi. Ajouter une vérification du retour de `$wpdb->insert()`. Référence: `inc/database.php:108-109`

## 🟡 Mineur

### 🟡 Mineur - Commentaire mal formaté
Le commentaire en ligne 47 dans `inc/cngeformations.php` a une typo : "à supprimer + la fleche entre les 2". Corriger en "à supprimer + la flèche entre les 2". Référence: `inc/cngeformations.php:50`

### 🟡 Mineur - Code commenté inutile
Les lignes 164, 189-223 dans `inc/cngeformations.php` contiennent du code commenté. Supprimer ce code mort. Référence: `inc/cngeformations.php:164,189-223`

### 🟡 Mineur - Inclusion de fichiers sans vérification
Les lignes 318-333 dans `functions.php` incluent des fichiers sans vérifier leur existence. Utiliser `file_exists()` avant `require`. Référence: `functions.php:318-333`

### 🟡 Mineur - Typo dans commentaire
La ligne 20 dans `inc/cngeformations.php` a une typo : "ReDateFull" devrait être en minuscules pour suivre les conventions. Référence: `inc/cngeformations.php:20`

### 🟡 Mineur - Espaces inutiles dans shortcode
La ligne 215-216 dans `inc/shortcodes.php` a des espaces et sauts de ligne mal formatés. Nettoyer le code HTML. Référence: `inc/shortcodes.php:215-216`

### 🟡 Mineur - Balise br mal utilisée
La ligne 219 dans `inc/shortcodes.php` utilise `<br>` au lieu de `<br />` ou `<br>`. Standardiser sur `<br>`. Référence: `inc/shortcodes.php:219`

### 🟡 Mineur - CSS inline dans shortcode
Le shortcode `clge_cal_events_shortcode()` inclut du CSS inline (lignes 71-115). Déplacer ce CSS dans un fichier séparé. Référence: `inc/shortcodes.php:71-115`

### 🟡 Mineur - Duplication de code dans all_events.php
Le template `all_events.php` duplique beaucoup de styles inline. Extraire ces styles dans une feuille de style commune. Référence: `templates/all_events.php:12-63`

### 🟡 Mineur - Utilisation de esc_attr au lieu de esc_html
La ligne 222 dans `templates/all_events.php` utilise `esc_attr()` pour du contenu HTML. Utiliser `esc_html()` à la place. Référence: `templates/all_events.php:222`

## 🔵 Amélioration

### 🔵 Amélioration - Code dupliqué dans database.php
Les fonctions `clge_create_event()` et `clge_update_event()` ont des parties similaires pour le traitement des dates. Extraire cette logique dans une fonction helper. Référence: `inc/database.php:83-110,142-182`

### 🔵 Amélioration - Utilisation de DateTimeImmutable
Remplacer `DateTime` par `DateTimeImmutable` pour éviter les modifications accidentelles des objets DateTime. Référence: Divers fichiers

### 🔵 Amélioration - Ajouter des docblocks manquants
Plusieurs fonctions dans `inc/clge-admin-page.php` n'ont pas de docblocks. Ajouter des documentations PHPDoc. Référence: `inc/clge-admin-page.php:28-140`

### 🔵 Amélioration - Constantes pour les noms de tables
Le nom de la table `$wpdb->prefix . 'clge_cal_events'` est répété plusieurs fois. Créer une constante globale. Référence: Divers fichiers

### 🔵 Amélioration - Utilisation de wp_send_json_success/error
Les fonctions AJAX dans `inc/clge-admin-page.php` utilisent `wp_die()` directement. Utiliser `wp_send_json_success()` et `wp_send_json_error()` pour plus de cohérence. Référence: `inc/clge-admin-page.php:32,43,54,77,87,103,120,138`

### 🔵 Amélioration - Ajouter des filtres pour les emails
L'email de newsletter est hardcodé. Ajouter un filtre WordPress pour permettre de le modifier via un plugin. Référence: `functions.php:342`

### 🔵 Amélioration - Optimisation des requêtes SQL
La fonction `clge_get_all_events()` charge tous les événements puis les filtre. Ajouter une clause WHERE pour ne charger que les événements futurs si nécessaire. Référence: `inc/database.php:114-125`

### 🔵 Amélioration - Ajouter des indexes sur la table
La table `clge_cal_events` n'a pas d'index sur les colonnes fréquemment interrogées comme `debut` et `fin`. Ajouter ces indexes. Référence: `inc/database.php:19-32`

### 🔵 Amélioration - Utilisation de prepare() pour les ALTER TABLE
Les requêtes ALTER TABLE dans `clge_migrate_add_alias_description()` devraient utiliser `$wpdb->prepare()` même si c'est moins critique. Référence: `inc/database.php:68,77`