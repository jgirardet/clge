<?php
// Ajouter une page dans le menu admin
function clge_add_admin_page()
{
    add_menu_page(
        "CLGE", // Titre de la page
        "CLGE", // Texte du menu
        "manage_options", // Capacité requise (admin)
        "clge", // Slug de la page
        "clge_render_admin_page", // Fonction d'affichage
        "dashicons-calendar-alt", // Icône
        20, // Position dans le menu
    );
}
add_action("admin_menu", "clge_add_admin_page");

// Afficher la page admin
function clge_render_admin_page()
{
    include get_template_directory() . "/templates/landing.php";
}

//******************* Calendrier *******************************//

// affiche la partie calendrier
// retourne le contenu de la page
function hx_clge_calendrier()
{
    include get_template_directory() . "/templates/clge-calendrier.php";
    wp_die();
}
add_action("wp_ajax_clge_calendrier", "hx_clge_calendrier");

// fragement qui affiche la liste des événements sous forme de tableau
function all_events_list()
{
    $calEvents = clge_get_all_events();
    include get_template_directory() . "/templates/all_events.php";
    wp_die();
}
add_action("wp_ajax_clge_all_events", "all_events_list");

// Ajoute un événement manuellement
// retourne le fragment liste des événements mise à jour
function hx_add_event()
{
    if (!check_admin_referer("clge_add_event")) {
        wp_send_json_error(["message" => __("Security check failed", "clge")]);
    }

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    clge_create_event($_POST);
    all_events_list();
}
add_action("wp_ajax_clge_add_event", "hx_add_event");

// Ajoute un événement CNGE formation
// retourne le fragment liste des événements mise à jour
function hx_add_cnge_formation()
{
    if (!check_admin_referer("clge_add_cnge_formation")) {
        wp_send_json_error(["message" => __("Security check failed", "clge")]);
    }

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    if (!isset($_POST["cnge"]) || !is_string($_POST["cnge"])) {
        wp_send_json_error(["message" => __("Invalid data", "clge")]);
    }

    $val = sanitize_text_field($_POST["cnge"]);

    // Vérifier que c'est bien du base64 valide
    if (!preg_match('/^[a-zA-Z0-9\/+=]+$/', $val)) {
        wp_send_json_error(["message" => __("Invalid base64 data", "clge")]);
    }

    $decoded = base64_decode($val);
    if ($decoded === false) {
        wp_send_json_error(["message" => __("Base64 decode failed", "clge")]);
    }

    $decoded = json_decode($decoded, true);
    if ($decoded === null) {
        wp_send_json_error(["message" => __("Invalid JSON data", "clge")]);
    }

    $decoded["debut"] = $decoded["debut"]["date"];
    $decoded["fin"] = $decoded["fin"]["date"];

    clge_create_event($decoded);
    all_events_list();
}
add_action("wp_ajax_clge_add_cnge_formation", "hx_add_cnge_formation");

// Supprime un événement
// retourne le fragment liste des événements mise à jour
function hx_delete_event()
{
    if (!check_admin_referer("clge_delete_event")) {
        wp_send_json_error(["message" => __("Security check failed", "clge")]);
    }

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    clge_delete_event($_POST["event_id"]);
    all_events_list();
}
add_action("wp_ajax_clge_delete_event", "hx_delete_event");

// Met à jour un événement
// retourne le fragment liste des événements mise à jour
function hx_update_event()
{
    if (!check_admin_referer("clge_update_event")) {
        wp_send_json_error(["message" => __("Security check failed", "clge")]);
    }

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    $event_id = isset($_POST["event_id"]) ? (int) $_POST["event_id"] : 0;
    $alias = isset($_POST["alias"])
        ? sanitize_text_field($_POST["alias"])
        : null;

    if ($event_id > 0) {
        clge_update_event($event_id, ["alias" => $alias]);
    }
    all_events_list();
}
add_action("wp_ajax_clge_update_event", "hx_update_event");

// Met à jour la description d'un événement
// retourne le fragment liste des événements mise à jour
function hx_update_event_description()
{
    if (!check_admin_referer("clge_update_event_description")) {
        wp_send_json_error(["message" => __("Security check failed", "clge")]);
    }

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    $event_id = isset($_POST["event_id"]) ? (int) $_POST["event_id"] : 0;
    $description = isset($_POST["description"])
        ? sanitize_textarea_field($_POST["description"])
        : null;

    if ($event_id > 0) {
        clge_update_event($event_id, ["description" => $description]);
    }
    all_events_list();
}
add_action(
    "wp_ajax_clge_update_event_description",
    "hx_update_event_description",
);

// fragment qui retoune la liste des formations CNGE sous forme d'options
function hx_select_cnge_formations_list()
{
    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => __("Unauthorized", "clge")]);
    }

    $formations = getCNGEFormationEvents();
    foreach ($formations as $event): ?>
        <option value="<?php echo esc_attr(
            base64_encode(json_encode($event)),
        ); ?>">
            <?php
            echo esc_html($event->debut->format("d/m/Y H:i")) . " → ";
            echo esc_html($event->lieu_physique) . " | ";
            echo esc_html($event->nom) . " | ";
            echo esc_html($event->fin->format("H:i"));
            ?>
        </option>
<?php endforeach;
    wp_die();
}
add_action(
    "wp_ajax_select_cnge_formation_list",
    "hx_select_cnge_formations_list",
);

?>
