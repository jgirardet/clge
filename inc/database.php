<?php
/**
 * Gestion de la table clge_cal_events
 */

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'clge_cal_events';

// Création de la table si elle n'existe pas
function clge_create_cal_events_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            debut datetime NOT NULL,
            fin datetime NOT NULL,
            nom varchar(255) NOT NULL,
            abrev varchar(50) DEFAULT NULL,  -- Champ facultatif
            alias varchar(50) DEFAULT NULL,  -- Champ facultatif
            description text DEFAULT NULL,   -- Champ facultatif
            lieu_physique varchar(255) NOT NULL,
            url varchar(255) NOT NULL,
            evt_clge bool NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Ajout d'un événement par défaut
        $wpdb->insert(
            $table_name,
            array(
                'debut' => current_time('mysql'),  // Date/heure actuelle
                'fin'   => date('Y-m-d H:i:s', strtotime('+1 hour')),  // 1 heure plus tard
                'nom'   => 'Événement par défaut',
                'abrev' => 'Def',
                'lieu_physique'  => 'En ligne',
                'url'   => '#',
                'evt_clge' => 0,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );
    }
}

// Appel à la création de la table lors de l'activation du thème
add_action('after_switch_theme', 'clge_create_cal_events_table');

// Migration : Ajouter les colonnes alias et description si elles n'existent pas
function clge_migrate_add_alias_description() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';

    // Vérifier si la colonne alias existe
    $alias_exists = $wpdb->get_results(
        $wpdb->prepare("SHOW COLUMNS FROM $table_name LIKE %s", 'alias')
    );

    if (empty($alias_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN alias varchar(50) DEFAULT NULL AFTER abrev");
    }

    // Vérifier si la colonne description existe
    $description_exists = $wpdb->get_results(
        $wpdb->prepare("SHOW COLUMNS FROM $table_name LIKE %s", 'description')
    );

    if (empty($description_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN description text DEFAULT NULL AFTER alias");
    }
}
add_action('after_switch_theme', 'clge_migrate_add_alias_description');

// Create: Ajouter un événement à partir d'un tableau associatif
function clge_create_event($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';

    $insert_data = array(
        'debut' => $data['debut'] instanceof DateTime ? $data['debut']->format('Y-m-d H:i:s') : $data['debut'] . (array_key_exists('debut_h', $data) ? ' ' . $data["debut_h"] : ''),
        'fin'   => $data['fin'] instanceof DateTime ? $data['fin']->format('Y-m-d H:i:s') : $data['fin']. (array_key_exists('fin_h', $data) ? ' ' . ($data['fin_h'] != "00:00"  ?  $data['fin_h'] : "23:59") : ''),
        'nom'   => sanitize_text_field($data['nom']),
        'lieu_physique'  => sanitize_text_field($data['lieu_physique']),
        'url'   => esc_url_raw($data['url']),
        'evt_clge' => isset($data['evt_clge']) ? (int) $data['evt_clge'] : 0,
    );

    if (isset($data['abrev'])) {
        $insert_data['abrev'] = sanitize_text_field($data['abrev']);
    }

    if (isset($data['alias'])) {
        $insert_data['alias'] = sanitize_text_field($data['alias']);
    }

    if (isset($data['description'])) {
        $insert_data['description'] = sanitize_textarea_field($data['description']);
    }

    $wpdb->insert($table_name, $insert_data);
    return $wpdb->insert_id;
}


// Retrieve: Tous les événements (retourne des objets avec ->)
function clge_get_all_events() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';
    $events = $wpdb->get_results("SELECT * FROM $table_name ORDER BY debut ASC");

    foreach ($events as &$event) {
        $event->debut = new DateTime($event->debut);
        $event->fin   = new DateTime($event->fin);
    }

    return $events; 
}

// Retrieve: Un événement par ID (retourne un objet)
function clge_get_event($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';
    $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if ($event) {
        $event->debut = new DateTime($event->debut);
        $event->fin   = new DateTime($event->fin);
    }

    return $event;
}

// Update: Mettre à jour un événement à partir d'un tableau associatif + ID
function clge_update_event($id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';

    $update_data = array(
        'debut' => $data['debut'] instanceof DateTime ? $data['debut']->format('Y-m-d H:i:s') : $data['debut'],
        'fin'   => $data['fin'] instanceof DateTime ? $data['fin']->format('Y-m-d H:i:s') : $data['fin'],
        'nom'   => sanitize_text_field($data['nom']),
        'lieu_physique'  => sanitize_text_field($data['lieu_physique']),
        'url'   => esc_url_raw($data['url']),
        'evt_clge' => isset($data['evt_clge']) ? (int) $data['evt_clge'] : 0,
    );

    if (isset($data['abrev'])) {
        $update_data['abrev'] = sanitize_text_field($data['abrev']);
    }

    if (isset($data['alias'])) {
        $update_data['alias'] = sanitize_text_field($data['alias']);
    }

    if (isset($data['description'])) {
        $update_data['description'] = sanitize_textarea_field($data['description']);
    }

    $wpdb->update($table_name, $update_data, array('id' => $id), null, array('%d'));
    return $wpdb->rows_affected;
}

// Delete: Supprimer un événement par ID
function clge_delete_event($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_cal_events';

    $wpdb->delete($table_name, array('id' => $id), array('%d'));

    return $wpdb->rows_affected;
}
