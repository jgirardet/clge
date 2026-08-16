<?php
/**
 * CLGE Nextcloud Integration
 *
 * Gère la configuration et la connexion à Nextcloud
 * Stocke les identifiants de manière sécurisée avec chiffrement AES-256
 * Utilise SECURE_AUTH_KEY de WordPress pour le chiffrement
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

/**
 * Vérifie que SECURE_AUTH_KEY est définie
 */
if (!defined("SECURE_AUTH_KEY")) {
    trigger_error(
        "SECURE_AUTH_KEY est requise pour le chiffrement Nextcloud. Vérifiez votre wp-config.php.",
        E_USER_ERROR,
    );
}

/**
 * Chiffre une donnée avec AES-256-CBC
 *
 * @param string $data La donnée à chiffrer
 * @return string La donnée chiffrée encodée en base64 (IV + données)
 */
function clge_encrypt_nextcloud_data($data)
{
    if (empty($data)) {
        return "";
    }

    $key = SECURE_AUTH_KEY;
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, "AES-256-CBC", $key, 0, $iv);

    if ($encrypted === false) {
        return "";
    }

    return base64_encode($iv . $encrypted);
}

/**
 * Déchiffre une donnée chiffrée avec AES-256-CBC
 *
 * @param string $encrypted_data La donnée chiffrée (base64 encoded)
 * @return string|false La donnée déchiffrée ou false en cas d'erreur
 */
function clge_decrypt_nextcloud_data($encrypted_data)
{
    if (empty($encrypted_data)) {
        return false;
    }

    $key = SECURE_AUTH_KEY;

    $data = base64_decode($encrypted_data);
    if ($data === false) {
        return false;
    }

    $iv_length = openssl_cipher_iv_length("AES-256-CBC");
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);

    return openssl_decrypt($encrypted, "AES-256-CBC", $key, 0, $iv);
}

/**
 * Sauvegarde les paramètres Nextcloud
 * Chiffre le mot de passe avec AES-256 avant stockage
 *
 * @return void
 */
function clge_save_nextcloud_settings()
{
    // Vérification de sécurité
    if (!check_ajax_referer("clge_save_nextcloud_settings", "_wpnonce")) {
        wp_send_json_error(
            ["message" => "Erreur de sécurité. Veuillez réessayer."],
            403,
        );
    }

    // Vérification des capabilities
    if (!current_user_can("manage_options")) {
        wp_send_json_error(
            ["message" => 'Vous n\'avez pas les droits nécessaires.'],
            403,
        );
    }

    // Récupération et validation des données
    $url = isset($_POST["nextcloud_url"])
        ? sanitize_url($_POST["nextcloud_url"])
        : "";
    $username = isset($_POST["nextcloud_username"])
        ? sanitize_text_field($_POST["nextcloud_username"])
        : "";
    $password = isset($_POST["nextcloud_password"])
        ? $_POST["nextcloud_password"]
        : "";

    // Validation de l'URL
    if (empty($url)) {
        wp_send_json_error(
            ["message" => 'L\'URL du serveur Nextcloud est obligatoire.'],
            400,
        );
    }

    // Validation du nom d'utilisateur
    if (empty($username)) {
        wp_send_json_error(
            ["message" => 'Le nom d\'utilisateur est obligatoire.'],
            400,
        );
    }

    // Validation de l'URL (doit commencer par http:// ou https://)
    if (!preg_match("#^https?://#i", $url)) {
        wp_send_json_error(
            ["message" => 'L\'URL doit commencer par http:// ou https://'],
            400,
        );
    }

    // Sauvegarder l'URL et le nom d'utilisateur
    update_option("clge_nextcloud_url", $url);
    update_option("clge_nextcloud_username", $username);

    // Si un mot de passe est fourni, le chiffrer et sauvegarder
    if (!empty($password)) {
        $encrypted_password = clge_encrypt_nextcloud_data($password);
        if (!empty($encrypted_password)) {
            update_option(
                "clge_nextcloud_password_encrypted",
                $encrypted_password,
            );
        } else {
            echo '<div class="clge-test-connection-result error">Erreur lors du chiffrement du mot de passe.</div>';
            wp_die();
        }
    }

    // Nettoyer le mot de passe en clair de la mémoire
    unset($password, $encrypted_password);

    // Mettre à jour le statut du mot de passe
    $nextcloud_has_password = !empty(
        get_option("clge_nextcloud_password_encrypted", "")
    );

    // Retourner le message de succès pour #test-connection-result
    echo '<div class="clge-test-connection-result success">Configuration sauvegardée avec succès.</div>';

    // Retourner le statut mis à jour via OOB pour #clge-password-status-container
    echo '<div id="clge-password-status-container" class="clge-password-status ' .
        ($nextcloud_has_password ? "set" : "not-set") .
        '" hx-swap-oob="true">';
    echo $nextcloud_has_password
        ? "<span>Mot de passe configuré</span>"
        : "<span>Aucun mot de passe configuré</span>";
    echo "</div>";

    // Retourner les boutons mis à jour via OOB
    echo '<div id="clge-nextcloud-actions-container" hx-swap-oob="true">' .
        clge_generate_nextcloud_action_buttons() .
        "</div>";
    wp_die();
}
add_action(
    "wp_ajax_clge_save_nextcloud_settings",
    "clge_save_nextcloud_settings",
);

/**
 * Génère le HTML des boutons Supprimer et Tester
 *
 * @return string HTML des boutons
 */
function clge_generate_nextcloud_action_buttons()
{
    $admin_ajax_url = esc_url(admin_url("admin-ajax.php"));
    $has_password = !empty(get_option("clge_nextcloud_password_encrypted", ""));
    $has_url = !empty(get_option("clge_nextcloud_url", ""));
    $has_username = !empty(get_option("clge_nextcloud_username", ""));

    $html = '<div class="clge-form-actions" style="margin-top: 16px;">';

    // Bouton Supprimer - visible seulement si mot de passe configuré
    if ($has_password) {
        $nonce_clear = esc_attr(
            wp_create_nonce("clge_clear_nextcloud_password"),
        );
        $vals_clear = json_encode(
            [
                "action" => "clge_clear_nextcloud_password",
                "_wpnonce" => $nonce_clear,
            ],
            JSON_UNESCAPED_SLASHES,
        );

        $html .=
            '<button type="button" class="clge-submit danger"
            hx-post="' .
            $admin_ajax_url .
            '"
            hx-target="#test-connection-result"
            hx-swap="innerHTML"
            hx-confirm="⚠️ ATTENTION : Cette action supprimera définitivement le mot de passe stocké. Êtes-vous sûr ?"
            hx-vals="' .
            esc_attr($vals_clear) .
            '">
            Supprimer le mot de passe
        </button>';
    }

    // Bouton Tester - visible si URL et username configurés
    if ($has_url && $has_username) {
        $nonce_test = esc_attr(
            wp_create_nonce("clge_test_nextcloud_connection"),
        );
        $vals_test = json_encode(
            [
                "action" => "clge_test_nextcloud_connection",
                "_wpnonce" => $nonce_test,
            ],
            JSON_UNESCAPED_SLASHES,
        );

        $style = $has_password ? ' style="margin-left: 8px;"' : "";
        $html .=
            '<button type="button" class="clge-submit secondary"
            hx-post="' .
            $admin_ajax_url .
            '"
            hx-target="#test-connection-result"
            hx-swap="innerHTML"
            hx-vals="' .
            esc_attr($vals_test) .
            '"' .
            $style .
            '>
            Tester la connexion</button>';
    }

    $html .= "</div>";

    return $html;
}

/**
 * Teste la connexion Nextcloud avec Basic Auth (équivalent de curl -u user:pass URL)
 *
 * @return void
 */
function clge_test_nextcloud_connection()
{
    // Vérification de sécurité
    if (!check_ajax_referer("clge_test_nextcloud_connection", "_wpnonce")) {
        echo '<div class="clge-test-connection-result error">Erreur de sécurité. Veuillez réessayer.</div>';
        wp_die();
    }

    // Vérification des capabilities
    if (!current_user_can("manage_options")) {
        echo '<div class="clge-test-connection-result error">Vous n\'avez pas les droits nécessaires.</div>';
        wp_die();
    }

    $url = get_option("clge_nextcloud_url", "");
    $username = get_option("clge_nextcloud_username", "");
    $encrypted_password = get_option("clge_nextcloud_password_encrypted", "");

    if (empty($url) || empty($username) || empty($encrypted_password)) {
        echo '<div class="clge-test-connection-result error">Configuration incomplète. Veuillez d\'abord sauvegarder l\'URL, le nom d\'utilisateur et le mot de passe.</div>';
        wp_die();
    }

    // Déchiffrer le mot de passe
    $password = clge_decrypt_nextcloud_data($encrypted_password);
    if ($password === false) {
        echo '<div class="clge-test-connection-result error">Impossible de déchiffrer le mot de passe. La clé SECURE_AUTH_KEY a peut-être changé.</div>';
        wp_die();
    }

    // Construire les credentials Basic Auth
    $credentials = base64_encode($username . ":" . $password);
    unset($password);

    // Requête simple comme : curl -u user:password URL
    $args = [
        "timeout" => 10,
        "headers" => [
            "Authorization" => "Basic " . $credentials,
        ],
    ];

    $response = wp_remote_get($url, $args);

    // Nettoyer
    unset($credentials, $args);

    if (is_wp_error($response)) {
        // Retourner l'erreur dans un div stylisé
        $error_msg = esc_html($response->get_error_message());
        echo '<div class="clge-test-connection-result error">Erreur de connexion: ' .
            $error_msg .
            "</div>";
        wp_die();
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $headers = wp_remote_retrieve_headers($response);

    // Extraire le message d'erreur si présent dans le body XML
    $error_message = "";
    if (
        !empty($body) &&
        preg_match("/<s:message>(.*?)<\/s:message>/", $body, $matches)
    ) {
        $error_message = trim($matches[1]);
    } elseif (
        !empty($body) &&
        preg_match("/<message>(.*?)<\/message>/", $body, $matches)
    ) {
        $error_message = trim($matches[1]);
    }

    // Si succès (200-299), retourner un message de succès
    if ($status_code >= 200 && $status_code < 300) {
        echo '<div class="clge-test-connection-result success">Connexion réussie (Code: ' .
            esc_html($status_code) .
            ")</div>";
        wp_die();
    } else {
        // En cas d'échec, retourner un message d'erreur
        $display_message =
            "Code: " .
            esc_html($status_code) .
            " - " .
            esc_html($error_message ?: "Erreur de connexion");
        echo '<div class="clge-test-connection-result error">' .
            $display_message .
            "</div>";
        wp_die();
    }
}
add_action(
    "wp_ajax_clge_test_nextcloud_connection",
    "clge_test_nextcloud_connection",
);

/**
 * Supprime le mot de passe Nextcloud stocké
 *
 * @return void
 */
function clge_clear_nextcloud_password()
{
    // Vérification de sécurité
    if (!check_ajax_referer("clge_clear_nextcloud_password", "_wpnonce")) {
        echo '<div class="clge-test-connection-result error">Erreur de sécurité.</div>';
        wp_die();
    }

    // Vérification des capabilities
    if (!current_user_can("manage_options")) {
        echo '<div class="clge-test-connection-result error">Droits insuffisants.</div>';
        wp_die();
    }

    // Supprimer le mot de passe chiffré
    delete_option("clge_nextcloud_password_encrypted");

    // Retourner le message de succès + statut OOB
    echo '<div class="clge-test-connection-result success">Mot de passe supprimé avec succès.</div>';
    echo '<div id="clge-password-status-container" class="clge-password-status not-set" hx-swap-oob="true"><span>Aucun mot de passe configuré</span></div>';

    // Retourner les boutons mis à jour via OOB (seront vides car plus de mot de passe)
    echo '<div id="clge-nextcloud-actions-container" hx-swap-oob="true">' .
        clge_generate_nextcloud_action_buttons() .
        "</div>";
    wp_die();
}
add_action(
    "wp_ajax_clge_clear_nextcloud_password",
    "clge_clear_nextcloud_password",
);

/**
 * Affiche le template de configuration Nextcloud
 *
 * @return void
 */
function clge_render_nextcloud_settings()
{
    // Vérification de sécurité
    if (!current_user_can("manage_options")) {
        wp_die(
            'Vous n\'avez pas les droits nécessaires pour accéder à cette page.',
        );
    }

    include get_template_directory() . "/templates/clge-nextcloud-settings.php";
    wp_die();
}
add_action("wp_ajax_clge_nextcloud_settings", "clge_render_nextcloud_settings");

/**
 * Récupère les identifiants Nextcloud
 *
 * @param bool $include_password Si vrai, déchiffre et inclut le mot de passe
 * @return array Les identifiants
 */
function clge_get_nextcloud_credentials($include_password = false)
{
    $credentials = [
        "url" => get_option("clge_nextcloud_url", ""),
        "username" => get_option("clge_nextcloud_username", ""),
    ];

    if ($include_password) {
        $encrypted = get_option("clge_nextcloud_password_encrypted", "");
        $credentials["password"] = clge_decrypt_nextcloud_data($encrypted);
    }

    return $credentials;
}

/**
 * Vérifie si la configuration Nextcloud est complète
 *
 * @return bool
 */
function clge_is_nextcloud_configured()
{
    $url = get_option("clge_nextcloud_url", "");
    $username = get_option("clge_nextcloud_username", "");
    $encrypted_password = get_option("clge_nextcloud_password_encrypted", "");

    return !empty($url) && !empty($username) && !empty($encrypted_password);
}

/**
 * Vérifie le mot de passe Nextcloud (pour compatibilité)
 *
 * @param string $password Le mot de passe à vérifier
 * @return bool
 */
function clge_verify_nextcloud_password($password)
{
    $encrypted = get_option("clge_nextcloud_password_encrypted", "");
    if (empty($encrypted)) {
        return false;
    }

    $stored_password = clge_decrypt_nextcloud_data($encrypted);
    if ($stored_password === false) {
        return false;
    }

    // Comparaison en temps constant pour éviter les timing attacks
    return hash_equals($stored_password, $password);
}

/**
 * Nettoie les identifiants Nextcloud (utilitaire pour la désinstallation)
 *
 * @return void
 */
function clge_cleanup_nextcloud_credentials()
{
    delete_option("clge_nextcloud_url");
    delete_option("clge_nextcloud_username");
    delete_option("clge_nextcloud_password_encrypted");
}

/**
 * Récupère la liste complète des calendriers Nextcloud avec leur statut active
 *
 * @return array Tableau de calendriers [url, name, id, active]
 */

/**
 * Récupère la liste des calendriers CalDAV depuis Nextcloud
 * Utilise une requête PROPFIND sur l'API CalDAV
 *
 * @return array|WP_Error Tableau de calendriers ou erreur
 */
function clge_fetch_nextcloud_calendars()
{
    if (!clge_is_nextcloud_configured()) {
        return new WP_Error(
            "nextcloud_not_configured",
            "Configuration Nextcloud incomplète. Veuillez d'abord configurer l'URL, le nom d'utilisateur et le mot de passe.",
        );
    }

    $credentials = clge_get_nextcloud_credentials(true);
    $base_url = untrailingslashit($credentials["url"]);
    $username = $credentials["username"];
    $password = $credentials["password"];

    $caldav_url = $base_url . "/calendars/" . rawurlencode($username);

    $auth = base64_encode($username . ":" . $password);
    unset($password);

    $xml_body = '<?xml version="1.0" encoding="utf-8" ?>
<d:propfind xmlns:d="DAV:" xmlns:cs="http://calendarserver.org/ns/" xmlns:cal="urn:ietf:params:xml:ns:caldav">
  <d:prop>
    <d:displayname/>
    <d:resourcetype/>
    <cs:getctag/>
  </d:prop>
</d:propfind>';

    $args = [
        "method" => "PROPFIND",
        "timeout" => 30,
        "headers" => [
            "Authorization" => "Basic " . $auth,
            "Content-Type" => "application/xml",
            "Depth" => "1",
        ],
        "body" => $xml_body,
    ];

    unset($auth, $xml_body);

    $response = wp_remote_request($caldav_url, $args);

    if (is_wp_error($response)) {
        return new WP_Error(
            "caldav_request_failed",
            "Erreur lors de la requête CalDAV: " .
                $response->get_error_message(),
        );
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    unset($args, $response);

    if ($status_code < 200 || $status_code >= 300) {
        $error_msg =
            "Erreur HTTP " .
            $status_code .
            " pour l'URL: " .
            esc_url($caldav_url);
        if (!empty($body)) {
            if (
                preg_match("/<s:message>(.*?)<\/s:message>/", $body, $matches)
            ) {
                $error_msg .= " | Message: " . trim($matches[1]);
            } elseif (
                preg_match("/<message>(.*?)<\/message>/", $body, $matches)
            ) {
                $error_msg .= " | Message: " . trim($matches[1]);
            } else {
                $error_msg .= " | Corps: " . esc_html(substr($body, 0, 200));
            }
        }
        return new WP_Error("caldav_http_error", $error_msg);
    }

    // Vérifier que le corps n'est pas vide
    if (empty($body) || trim($body) === "") {
        return new WP_Error(
            "caldav_empty_response",
            "La réponse CalDAV est vide pour l'URL: " .
                esc_url($caldav_url) .
                ". Vérifiez que l'URL et les identifiants sont corrects.",
        );
    }

    $calendars = [];

    try {
        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($body);
        if ($xml === false) {
            $errors = libxml_get_errors();
            $error_msg = "Erreur de parse XML";
            if (!empty($errors)) {
                $error_msg .= ": " . $errors[0]->message;
            }
            return new WP_Error("caldav_parse_error", $error_msg);
        }
        $xml->registerXPathNamespace("d", "DAV:");
        $xml->registerXPathNamespace("cs", "http://calendarserver.org/ns/");
        $xml->registerXPathNamespace("caldav", "urn:ietf:params:xml:ns:caldav");
        $xml->registerXPathNamespace("ical", "http://apple.com/ns/ical/");

        $responses = $xml->xpath("//d:response");

        foreach ($responses as $response) {
            $href_elements = $response->xpath(".//d:href");
            if (empty($href_elements)) {
                continue;
            }
            $url = (string) $href_elements[0];

            $displayname_elements = $response->xpath(".//d:displayname");
            $displayname = empty($displayname_elements)
                ? "Calendrier sans nom"
                : (string) $displayname_elements[0];

            $resourcetype_elements = $response->xpath(".//d:resourcetype");

            // Garder uniquement les calendriers (ceux qui ont cal:calendar, cs:calendar ou d:calendar)
            if (!empty($resourcetype_elements)) {
                $resourcetype = $resourcetype_elements[0];
                $calendar_comp = $resourcetype->xpath(
                    ".//cal:calendar | .//cs:calendar | .//d:calendar",
                );
                if (empty($calendar_comp)) {
                    continue; // Skip si ce n'est pas un calendrier
                }
            }

            $calendar_id = basename(rtrim($url, "/"));
            $calendar_url = $url;

            $calendars[] = [
                "name" => $displayname,
                "id" => $calendar_id,
                "url" => $calendar_url,
            ];
        }
    } catch (Exception $e) {
        return new WP_Error(
            "caldav_parse_error",
            "Erreur lors du parse du XML CalDAV: " . $e->getMessage(),
        );
    }

    return $calendars;
}
function clge_get_nextcloud_calendars()
{
    $calendars = get_option("clge_nextcloud_calendars", []);
    return is_array($calendars) ? $calendars : [];
}

/**
 * Sauvegarde la liste complète des calendriers Nextcloud
 *
 * @param array $calendars Tableau de calendriers à sauvegarder
 * @return void
 */
function clge_save_nextcloud_calendars($calendars)
{
    update_option("clge_nextcloud_calendars", $calendars);
}

/**
 * Basculer le statut active d'un calendrier
 *
 * @param string $calendar_url URL du calendrier
 * @return array Liste des calendriers mise à jour
 */
function clge_toggle_calendar_active($calendar_url)
{
    $calendars = clge_get_nextcloud_calendars();
    $found = false;

    foreach ($calendars as &$calendar) {
        if ($calendar["url"] === $calendar_url) {
            $calendar["active"] = !$calendar["active"];
            $found = true;
            break;
        }
    }

    if ($found) {
        clge_save_nextcloud_calendars($calendars);
    }

    return $calendars;
}

/**
 * Fusionne les calendriers CalDAV avec ceux stockés en base
 * Garde le statut 'active' des calendriers existants
 *
 * @param array $caldav_calendars Calendriers récupérés depuis CalDAV
 * @return array Liste fusionnée
 */
function clge_merge_calendars($caldav_calendars)
{
    $stored_calendars = clge_get_nextcloud_calendars();
    $merged = [];
    $stored_by_url = [];

    // Indexer les calendriers stockés par URL
    foreach ($stored_calendars as $cal) {
        $stored_by_url[$cal["url"]] = $cal;
    }

    // Ajouter ou mettre à jour les calendriers CalDAV
    foreach ($caldav_calendars as $cal) {
        if (isset($stored_by_url[$cal["url"]])) {
            // Garder le statut active existant
            $merged[] = [
                "url" => $cal["url"],
                "name" => $cal["name"],
                "id" => $cal["id"],
                "active" => $stored_by_url[$cal["url"]]["active"],
            ];
            unset($stored_by_url[$cal["url"]]);
        } else {
            // Nouveau calendrier, désactivé par défaut
            $merged[] = [
                "url" => $cal["url"],
                "name" => $cal["name"],
                "id" => $cal["id"],
                "active" => false,
            ];
        }
    }

    return $merged;
}

/**
 * Génère le HTML pour un item de calendrier avec checkbox HTMX
 *
 * @param array $calendar Tableau avec keys: url, name, id, active
 * @return string HTML du calendrier item
 */
function clge_render_calendar_item($calendar)
{
    $admin_ajax_url = esc_url(admin_url("admin-ajax.php"));
    $calendar_id = esc_attr($calendar["id"]);
    $is_active = !empty($calendar["active"]);
    $nonce = esc_attr(wp_create_nonce("clge_toggle_calendar"));

    $hx_vals = json_encode(
        [
            "action" => "clge_toggle_calendar",
            "calendar_url" => $calendar["url"],
            "_wpnonce" => $nonce,
        ],
        JSON_UNESCAPED_SLASHES,
    );

    ob_start();
    ?>
	<div class="clge-calendar-item">
		<input type="checkbox"
				id="cal-<?php echo $calendar_id; ?>"
				<?php checked($is_active); ?>
				hx-post="<?php echo $admin_ajax_url; ?>"
				hx-vals='<?php echo esc_attr($hx_vals); ?>'
				hx-target="closest .clge-calendar-item"
				hx-swap="outerHTML"
				hx-trigger="change">
		<label for="cal-<?php echo $calendar_id; ?>"><?php echo esc_html($calendar["name"]); ?></label>
	</div>
	<?php return ob_get_clean();
}

/**
 * Affiche la liste des calendriers avec checkboxes (fragment HTML via HTMX)
 * Met à jour la liste en base avec fusion
 */
function clge_render_calendar_selection()
{
    // Vérification de sécurité pour les requêtes GET
    if (
        isset($_GET["_wpnonce"]) &&
        !wp_verify_nonce(
            sanitize_text_field($_GET["_wpnonce"]),
            "clge_load_nextcloud_calendars",
        )
    ) {
        echo '<div class="clge-test-connection-result error">Erreur de sécurité. Veuillez réessayer.</div>';
        wp_die();
    }

    if (!current_user_can("manage_options")) {
        wp_die("Vous n'avez pas les droits nécessaires.");
    }

    if (!clge_is_nextcloud_configured()) {
        echo '<div class="clge-test-connection-result error">Configuration Nextcloud incomplète. Veuillez d\'abord configurer l\'URL, le nom d\'utilisateur et le mot de passe.</div>';
        wp_die();
    }

    $calendars_result = clge_fetch_nextcloud_calendars();

    if (is_wp_error($calendars_result)) {
        echo '<div class="clge-test-connection-result error">Erreur: ' .
            esc_html($calendars_result->get_error_message()) .
            "</div>";
        wp_die();
    }

    // Fusionner avec les calendriers existants
    $merged_calendars = clge_merge_calendars($calendars_result);
    clge_save_nextcloud_calendars($merged_calendars);

    $admin_ajax_url = esc_url(admin_url("admin-ajax.php"));

    if (empty($merged_calendars)) {
        echo '<div class="clge-test-connection-result">Aucun calendrier trouvé.</div>';
        wp_die();
    }

    foreach ($merged_calendars as $calendar) {
        echo clge_render_calendar_item($calendar);
    }

    wp_die();
}
add_action(
    "wp_ajax_clge_load_nextcloud_calendars",
    "clge_render_calendar_selection",
);

/**
 * Gère le toggle de sélection d'un calendrier (appelé via HTMX)
 */
function clge_handle_toggle_calendar()
{
    if (
        !isset($_POST["_wpnonce"]) ||
        !wp_verify_nonce(
            sanitize_text_field($_POST["_wpnonce"]),
            "clge_toggle_calendar",
        )
    ) {
        header("HTTP/1.1 403 Forbidden");
        echo '<div class="clge-calendar-item" style="color: #dc2626; padding: 8px; margin: 8px 0; border: 1px solid #fecaca; border-radius: 6px;">Erreur de sécurité</div>';
        wp_die();
    }

    if (!current_user_can("manage_options")) {
        header("HTTP/1.1 403 Forbidden");
        echo '<div class="clge-calendar-item" style="color: #dc2626; padding: 8px; margin: 8px 0; border: 1px solid #fecaca; border-radius: 6px;">Droits insuffisants</div>';
        wp_die();
    }

    $calendar_url = isset($_POST["calendar_url"])
        ? esc_url_raw($_POST["calendar_url"])
        : "";

    if (empty($calendar_url)) {
        header("HTTP/1.1 400 Bad Request");
        echo '<div class="clge-calendar-item" style="color: #dc2626; padding: 8px; margin: 8px 0; border: 1px solid #fecaca; border-radius: 6px;">URL de calendrier manquante</div>';
        wp_die();
    }

    // Basculer le statut active
    clge_toggle_calendar_active($calendar_url);

    // Trouver le calendrier dans la liste pour le réafficher
    $calendars = clge_get_nextcloud_calendars();
    $found = false;
    foreach ($calendars as $calendar) {
        if ($calendar["url"] === $calendar_url) {
            echo clge_render_calendar_item($calendar);
            $found = true;
            break;
        }
    }

    if (!$found) {
        header("HTTP/1.1 404 Not Found");
        echo '<div class="clge-calendar-item" style="color: #dc2626; padding: 8px; margin: 8px 0; border: 1px solid #fecaca; border-radius: 6px;">Calendrier introuvable</div>';
    }

    wp_die();
}
add_action("wp_ajax_clge_toggle_calendar", "clge_handle_toggle_calendar");
