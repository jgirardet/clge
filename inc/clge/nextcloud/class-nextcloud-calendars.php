<?php
/**
 * CLGE Nextcloud - Calendars Handler
 *
 * Gère la logique métier des calendriers :
 * - Récupération depuis l'API
 * - Stockage dans les options WordPress
 * - Fusion avec les calendriers existants
 * - Activation/Désactivation
 * - Rendering HTML
 */

defined("ABSPATH") || exit();

class Clge_Nextcloud_Calendars
{
    /**
     * Enregistre les hooks WordPress pour les actions AJAX.
     */
    public static function register_hooks(): void
    {
        add_action("wp_ajax_clge_load_nextcloud_calendars", [
            self::class,
            "render_calendar_selection",
        ]);
        add_action("wp_ajax_clge_get_known_calendars", [
            self::class,
            "render_known_calendars",
        ]);
        add_action("wp_ajax_clge_toggle_calendar", [
            self::class,
            "handle_toggle_calendar",
        ]);
    }

    /**
     * Récupère la liste des calendriers stockés en option WordPress.
     *
     * @return array<array{url: string, name: string, id: string, active: bool}> Tableau de calendriers
     */
    public static function get_calendars(): array
    {
        $calendars = get_option("clge_nextcloud_calendars", []);
        return is_array($calendars) ? $calendars : [];
    }

    /**
     * Sauvegarde la liste des calendriers dans une option WordPress.
     *
     * @param array $calendars Tableau de calendriers à sauvegarder
     */
    public static function save_calendars(array $calendars): void
    {
        update_option("clge_nextcloud_calendars", $calendars);
    }

    /**
     * Trouve un calendrier par son URL.
     *
     * @param string $calendar_url URL du calendrier à trouver
     * @param array<array{url: string, name: string, id: string, active: bool}>|null $calendars Liste des calendriers (optionnel)
     * @return array{url: string, name: string, id: string, active: bool}|null Le calendrier trouvé ou null
     */
    public static function find_calendar_by_url(
        string $calendar_url,
        ?array $calendars = null,
    ): ?array {
        if ($calendars === null) {
            $calendars = self::get_calendars();
        }
        foreach ($calendars as $calendar) {
            if ($calendar["url"] === $calendar_url) {
                return $calendar;
            }
        }
        return null;
    }

    /**
     * Bascule le statut 'active' d'un calendrier.
     *
     * @param string $calendar_url URL du calendrier à basculer
     * @return array Liste complète des calendriers mise à jour
     */
    public static function toggle_active(string $calendar_url): array
    {
        $calendars = self::get_calendars();
        $found = false;

        foreach ($calendars as &$calendar) {
            if ($calendar["url"] === $calendar_url) {
                $calendar["active"] = !$calendar["active"];
                $found = true;
                break; // Un calendrier est identifié par son URL unique : pas de doublons attendus
            }
        }

        if ($found) {
            self::save_calendars($calendars);
        }

        return $calendars;
    }

    /**
     * Fusionne les calendriers CalDAV avec ceux stockés en base.
     * - Garde le statut 'active' des calendriers existants
     * - Ajoute les nouveaux calendriers (désactivés par défaut)
     * - Conserve les calendriers stockés qui ne sont plus sur Nextcloud (marqués comme inactifs)
     *
     * @param array<array{url: string, name: string, id: string}> $caldav_calendars Calendriers récupérés depuis CalDAV
     * @param bool $delete_missing Si vrai, supprime les calendriers non présents sur Nextcloud
     * @return array<array{url: string, name: string, id: string, active: bool}> Liste fusionnée
     */
    public static function merge_calendars(
        array $caldav_calendars,
        bool $delete_missing = false,
    ): array {
        $stored_calendars = self::get_calendars();
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

        // Ajouter les calendriers stockés qui ne sont plus sur Nextcloud (marqués comme inactifs)
        // Sauf si $delete_missing est vrai, auquel cas on les ignore
        if (!$delete_missing) {
            foreach ($stored_by_url as $cal) {
                $merged[] = [
                    "url" => $cal["url"],
                    "name" => $cal["name"],
                    "id" => $cal["id"],
                    "active" => false, // Désactivé car plus disponible sur Nextcloud
                ];
            }
        }

        return $merged;
    }

    /**
     * Génère le HTML pour un item de calendrier avec checkbox HTMX.
     *
     * @param array{url: string, name: string, id?: string, active?: bool} $calendar Calendrier à rendre
     * @return string HTML du calendrier item
     */
    public static function render_calendar_item(array $calendar): string
    {
        $admin_ajax_url = esc_url(admin_url("admin-ajax.php"));
        $calendar_id = isset($calendar["id"])
            ? esc_attr($calendar["id"])
            : md5($calendar["url"]);
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
     * Affiche la liste des calendriers connus (depuis option WP).
     * Utilisé pour l'affichage initial de la page.
     */
    public static function render_known_calendars(): void
    {
        if (!current_user_can("manage_options")) {
            wp_die('Vous n\'avez pas les droits nécessaires.');
        }

        $calendars = self::get_calendars();

        if (empty($calendars)) {
            echo '<div class="clge-test-connection-result">Aucun calendrier configuré. Cliquez sur "Charger les calendriers" pour les récupérer depuis Nextcloud.</div>';
            wp_die();
        }

        foreach ($calendars as $calendar) {
            echo self::render_calendar_item($calendar);
        }

        wp_die();
    }

    /**
     * Affiche la liste des calendriers (récupérés depuis Nextcloud + fusion).
     * Charge depuis l'API, fusionne avec les calendriers existants et sauvegarde.
     */
    public static function render_calendar_selection(): void
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
            wp_die('Vous n\'avez pas les droits nécessaires.');
        }

        if (!Clge_Nextcloud_Settings::is_configured()) {
            echo '<div class="clge-test-connection-result error">Configuration Nextcloud incomplète. Veuillez d\'abord configurer l\'URL, le nom d\'utilisateur et le mot de passe.</div>';
            wp_die();
        }

        $calendars_result = Clge_Nextcloud_API::fetch_calendars();

        if (is_wp_error($calendars_result)) {
            echo '<div class="clge-test-connection-result error">Erreur: ' .
                esc_html($calendars_result->get_error_message()) .
                "</div>";
            wp_die();
        }

        // Fusionner avec les calendriers existants
        $merged_calendars = self::merge_calendars($calendars_result);
        self::save_calendars($merged_calendars);

        if (empty($merged_calendars)) {
            echo '<div class="clge-test-connection-result">Aucun calendrier trouvé.</div>';
            wp_die();
        }

        foreach ($merged_calendars as $calendar) {
            echo self::render_calendar_item($calendar);
        }

        wp_die();
    }

    /**
     * Gère le toggle de sélection d'un calendrier (appelé via HTMX).
     */
    public static function handle_toggle_calendar(): void
    {
        if (
            !isset($_POST["_wpnonce"]) ||
            !wp_verify_nonce(
                sanitize_text_field($_POST["_wpnonce"]),
                "clge_toggle_calendar",
            )
        ) {
            header("HTTP/1.1 403 Forbidden");
            echo '<div class="clge-test-connection-result error">Erreur de sécurité</div>';
            wp_die();
        }

        if (!current_user_can("manage_options")) {
            header("HTTP/1.1 403 Forbidden");
            echo '<div class="clge-test-connection-result error">Droits insuffisants</div>';
            wp_die();
        }

        $calendar_url = isset($_POST["calendar_url"])
            ? esc_url_raw($_POST["calendar_url"])
            : "";
        if (empty($calendar_url)) {
            header("HTTP/1.1 400 Bad Request");
            echo '<div class="clge-test-connection-result error">URL de calendrier manquante</div>';
            wp_die();
        }

        self::toggle_active($calendar_url);

        // Utiliser la méthode helper pour trouver et rendre le calendrier
        $calendar = self::find_calendar_by_url($calendar_url);
        if ($calendar !== null) {
            echo self::render_calendar_item($calendar);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo '<div class="clge-test-connection-result error">Calendrier introuvable</div>';
        }

        wp_die();
    }
}
