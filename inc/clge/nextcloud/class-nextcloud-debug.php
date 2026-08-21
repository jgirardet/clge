<?php
/**
 * CLGE Nextcloud - Debug Handler
 *
 * Outils de debug pour l'intégration Nextcloud :
 * - Affichage de la page de debug
 * - Récupération et affichage des événements pour vérification
 */

defined("ABSPATH") || exit();

class Clge_Nextcloud_Debug
{
    /**
     * Enregistre les hooks WordPress pour les actions AJAX de debug.
     */
    public static function register_hooks(): void
    {
        add_action("wp_ajax_clge_debug_page", [
            self::class,
            "render_debug_page",
        ]);
        add_action("wp_ajax_clge_debug_nextcloud_events", [
            self::class,
            "handle_debug_events",
        ]);
    }

    /**
     * Affiche la page de debug Nextcloud.
     */
    public static function render_debug_page(): void
    {
        if (!current_user_can("manage_options")) {
            wp_die(
                'Vous n\'avez pas les droits nécessaires pour accéder à cette page.',
            );
        }
        include get_template_directory() . "/templates/clge-debug-page.php";
        wp_die();
    }

    /**
     * Gère la requête AJAX pour charger les événements Nextcloud en debug.
     * Affiche les événements au format brut pour vérification.
     */
    public static function handle_debug_events(): void
    {
        // Vérifier le nonce
        check_ajax_referer("clge_debug_nextcloud_events", "_wpnonce");

        // Récupérer l'URL du serveur Nextcloud
        $nextcloud_url = get_option("clge_nextcloud_url", "");
        $nextcloud_url = untrailingslashit($nextcloud_url);

        // Récupérer l'URL du calendrier depuis la requête
        $calendar_url = isset($_POST["calendar_url"])
            ? esc_url_raw($_POST["calendar_url"])
            : "";

        // Si $calendar_url commence par /, ajouter l'URL du serveur Nextcloud au début
        if (!empty($calendar_url) && substr($calendar_url, 0, 1) === "/") {
            $calendar_url = $nextcloud_url . $calendar_url;
        }

        // Récupérer les événements via l'API
        $events = Clge_Nextcloud_API::fetch_calendar_events($calendar_url);

        if (is_wp_error($events)) {
            wp_send_json_error(["message" => $events->get_error_message()]);
        }

        // Afficher les événements pour le debug
        echo '<div class="clge-debug-result clge-mt-4">';
        echo '<h3 class="clge-mt-0">Événements trouvés: ' .
            count($events) .
            "</h3>";
        echo '<pre class="clge-pre-debug">';
        echo htmlspecialchars(print_r($events, true), ENT_QUOTES, "UTF-8");
        echo "</pre>";
        echo "</div>";
        wp_die();
    }
}
