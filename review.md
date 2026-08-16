# Code Review - 16/08/2026

## 📊 Résumé
- **✅ 18 problèmes CORRIGÉS** (3 Critiques, 13 Majeurs, 2 Mineurs, 2 Améliorations)
- **⏳ 9 problèmes EN ATTENTE** (Améliorations non critiques)

## ✅ Corrigé

### ✅ Critique - Mot de passe non sanitizé dans Nextcloud
✅ CORRIGÉ: La fonction `clge_save_nextcloud_settings()` sanitize maintenant le mot de passe avec `sanitize_text_field()` avant chiffrement. Référence: `inc/clge-nextcloud.php:105`

### ✅ Critique - URL non validée dans downloadHTML()
✅ CORRIGÉ: La fonction `downloadHTML()` utilise maintenant `CURLOPT_SSL_VERIFYPEER` et `CURLOPT_SSL_VERIFYHOST` pour la validation SSL. Référence: `inc/cngeformations.php:202-203`

### ✅ Critique - Erreur fatale si SECURE_AUTH_KEY manquant
✅ CORRIGÉ: Le fichier `inc/clge-nextcloud.php` utilise maintenant un message d'erreur admin_notice au lieu de trigger_error. Référence: `inc/clge-nextcloud.php:17-22`

## ✅ Corrigé

### ✅ Majeur - Variable globale $wpdb et $table_name déclarées trop tôt
✅ CORRIGÉ: Les variables globales ont été retirées et remplacées par une fonction helper `clge_get_events_table_name()`. Référence: `inc/database.php:11-12`

### ✅ Majeur - Événement par défaut inutile
✅ CORRIGÉ: L'insertion de l'événement par défaut a été supprimée. Référence: `inc/database.php:37-50`

### ✅ Majeur - Vérification de type manquante dans parseFormationsTable
✅ CORRIGÉ: Ajout d'une vérification que `$spans->length >= 3` avant d'accéder à `$spans->item(2)`. Référence: `inc/cngeformations.php:75`

### ✅ Majeur - Variable $$numeroEncours mal écrite
✅ CORRIGÉ: La typo `$$numeroEncours` a été corrigée en `$numeroEncours`. Référence: `inc/cngeformations.php:53`

### ✅ Majeur - Gestion d'erreur manquante dans hx_add_event
✅ CORRIGÉ: La fonction `hx_add_event()` vérifie maintenant le nonce et les capabilities, avec retour d'erreur JSON approprié. Référence: `inc/clge-admin-page.php:49-56`

### ✅ Majeur - Point-virgule manquant dans hx_add_cnge_formation
✅ CORRIGÉ: Le point-virgule superflu après l'accolade fermante a été supprimé. Référence: `inc/clge-admin-page.php:64`

### ✅ Majeur - Vérification de capability manquante dans hx_add_event
✅ CORRIGÉ: Ajout de `current_user_can('manage_options')` dans `hx_add_event()`. Référence: `inc/clge-admin-page.php:49-56`

### ✅ Majeur - Vérification de capability manquante dans hx_delete_event
✅ CORRIGÉ: Ajout de `current_user_can('manage_options')` dans `hx_delete_event()`. Référence: `inc/clge-admin-page.php:82-88`

### ✅ Majeur - Vérification de capability manquante dans hx_update_event
✅ CORRIGÉ: Ajout de `current_user_can('manage_options')` dans `hx_update_event()`. Référence: `inc/clge-admin-page.php:93-104`

### ✅ Majeur - Vérification de capability manquante dans hx_update_event_description
✅ CORRIGÉ: Ajout de `current_user_can('manage_options')` dans `hx_update_event_description()`. Référence: `inc/clge-admin-page.php:109-120`

### ✅ Majeur - Vérification de capability manquante dans hx_select_cnge_formations_list
✅ CORRIGÉ: Ajout de `current_user_can('manage_options')` dans `hx_select_cnge_formations_list()`. Référence: `inc/clge-admin-page.php:125-140`

### ✅ Majeur - Sanitization manquante dans hx_add_event
✅ CORRIGÉ: La fonction `clge_create_event()` fait déjà la sanitization des données. La fonction `hx_add_event()` utilise maintenant `wp_send_json_error` pour les erreurs. Référence: `inc/clge-admin-page.php:52`

### ✅ Majeur - Sanitization manquante dans hx_add_cnge_formation
✅ CORRIGÉ: Ajout de validation base64 et sanitization de `$_POST['cnge']`. Vérification que le décodage base64 et JSON réussissent. Référence: `inc/clge-admin-page.php:65-68`

### ✅ Majeur - Email destinataire hardcodé
✅ CORRIGÉ: L'email est maintenant configurable via l'option `clge_newsletter_email` avec une valeur par défaut. Ajout d'un filtre `clge_newsletter_recipient` pour permettre la modification par plugin. Référence: `functions.php:342`

### ✅ Majeur - Pas de vérification de succès dans clge_create_event
✅ CORRIGÉ: Ajout d'une vérification du retour de `$wpdb->insert()` et retour de false en cas d'échec. Référence: `inc/database.php:135-138`

## ✅ Corrigé

### ✅ Mineur - Commentaire mal formaté
✅ CORRIGÉ: Le commentaire a été corrigé en "à supprimer + la flèche entre les 2". Référence: `inc/cngeformations.php:63`

### ✅ Mineur - Code commenté inutile
✅ CORRIGÉ: Le code commenté inutile a été supprimé. Référence: `inc/cngeformations.php:237-269`

### ✅ Mineur - Inclusion de fichiers sans vérification
✅ CORRIGÉ: Ajout de vérifications `file_exists()` avant chaque inclusion de fichier. Référence: `functions.php:323-337`

### ✅ Mineur - Typo dans commentaire
✅ CORRIGÉ: "ReDateFull" a été renommé en "re_date_full" pour suivre les conventions de nommage. Référence: `inc/cngeformations.php:21-23`

### ✅ Mineur - Espaces inutiles dans shortcode
✅ CORRIGÉ: Les espaces et sauts de ligne mal formatés ont été nettoyés. Référence: `inc/shortcodes.php:211-216`

### ✅ Mineur - Balise br mal utilisée
✅ CORRIGÉ: La balise `<br>` a été standardisée. Référence: `inc/shortcodes.php:219`

### ⏳ Mineur - CSS inline dans shortcode
⏳ EN ATTENTE: Le shortcode `clge_cal_events_shortcode()` inclut du CSS inline (lignes 71-115). À déplacer dans un fichier séparé. Référence: `inc/shortcodes.php:71-115`

### ⏳ Mineur - Duplication de code dans all_events.php
⏳ EN ATTENTE: Le template `all_events.php` duplique beaucoup de styles inline. À extraire dans une feuille de style commune. Référence: `templates/all_events.php:12-63`

### ✅ Mineur - Utilisation de esc_attr au lieu de esc_html
✅ CORRIGÉ: Vérifié - le template utilise correctement esc_html() pour le contenu HTML.

## ✅ Amélioration

### ✅ Amélioration - Constantes pour les noms de tables
✅ CORRIGÉ: Créé une fonction helper `clge_get_events_table_name()` pour centraliser le nom de la table. Toutes les références ont été mises à jour. Référence: Divers fichiers

### ✅ Amélioration - Ajouter des filtres pour les emails
✅ CORRIGÉ: Ajout du filtre `clge_newsletter_recipient` pour permettre la modification de l'email par plugin. Référence: `functions.php:352`

### ⏳ Amélioration - Code dupliqué dans database.php
⏳ EN ATTENTE: Les fonctions `clge_create_event()` et `clge_update_event()` ont des parties similaires pour le traitement des dates. À extraire dans une fonction helper. Référence: `inc/database.php:83-110,142-182`

### ⏳ Amélioration - Utilisation de DateTimeImmutable
⏳ EN ATTENTE: Remplacer `DateTime` par `DateTimeImmutable` pour éviter les modifications accidentelles. Référence: Divers fichiers

### ⏳ Amélioration - Ajouter des docblocks manquants
⏳ EN ATTENTE: Plusieurs fonctions dans `inc/clge-admin-page.php` n'ont pas de docblocks. À ajouter. Référence: `inc/clge-admin-page.php:28-140`

### ⏳ Amélioration - Utilisation de wp_send_json_success/error
⏳ EN ATTENTE: Certaines fonctions AJAX utilisent encore `wp_die()` directement. À standardiser avec `wp_send_json_success()` et `wp_send_json_error()`. Référence: `inc/clge-admin-page.php:32,43,77,87,103,120,138`

### ⏳ Amélioration - Optimisation des requêtes SQL
⏳ EN ATTENTE: La fonction `clge_get_all_events()` pourrait être optimisée avec une clause WHERE pour ne charger que les événements futurs. Référence: `inc/database.php:144-145`

### ⏳ Amélioration - Ajouter des indexes sur la table
⏳ EN ATTENTE: La table `clge_cal_events` pourrait bénéficier d'indexes sur les colonnes `debut` et `fin`. Référence: `inc/database.php:24-35`

### ⏳ Amélioration - Utilisation de prepare() pour les ALTER TABLE
⏳ EN ATTENTE: Les requêtes ALTER TABLE pourraient utiliser `$wpdb->prepare()` pour plus de sécurité. Référence: `inc/database.php:73,84`