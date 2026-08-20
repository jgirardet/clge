<?php
/**
 * CLGE Nextcloud - Settings Handler
 *
 * Gère la sauvegarde, récupération, vérification et nettoyage
 * des paramètres de configuration Nextcloud
 */

defined("ABSPATH") || exit();

class Clge_Nextcloud_Settings
{
    /**
     * Enregistre les hooks WordPress pour les actions AJAX.
     */
    public static function register_hooks(): void
    {
        add_action("wp_ajax_clge_save_nextcloud_settings", [
            self::class,
            "save_settings",
        ]);
        add_action("wp_ajax_clge_clear_nextcloud_password", [
            self::class,
            "clear_password",
        ]);
        add_action("wp_ajax_clge_nextcloud_settings", [
            self::class,
            "render_settings",
        ]);
    }

    /**
     * Sauvegarde les paramètres Nextcloud.
     * Chiffre le mot de passe avec AES-256 avant stockage.
     */
    public static function save_settings(): void
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
            ? sanitize_text_field($_POST["nextcloud_password"])
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

        // Validation du mot de passe (au moins 8 caractères si fourni)
        if (!empty($password) && strlen($password) < 8) {
            wp_send_json_error(
                [
                    "message" =>
                        "Le mot de passe doit contenir au moins 8 caractères.",
                ],
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

        // Vérifier que SECURE_AUTH_KEY est suffisante pour le chiffrement AES-256
        if (!defined("SECURE_AUTH_KEY") || strlen(SECURE_AUTH_KEY) < 32) {
            wp_send_json_error(
                [
                    "message" =>
                        "SECURE_AUTH_KEY doit faire au moins 32 caractères pour le chiffrement AES-256. Vérifiez votre wp-config.php.",
                ],
                400,
            );
        }

        // Sauvegarder l'URL et le nom d'utilisateur
        update_option("clge_nextcloud_url", $url);
        update_option("clge_nextcloud_username", $username);

        // Si un mot de passe est fourni, le chiffrer et sauvegarder
        if (!empty($password)) {
            $encrypted_password = Clge_Nextcloud_Encryption::encrypt($password);
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

        // Retourner le message de succès
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
            Clge_Nextcloud_UI::generate_action_buttons() .
            "</div>";
        wp_die();
    }

    /**
     * Supprime le mot de passe Nextcloud stocké.
     */
    public static function clear_password(): void
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

        // Retourner les boutons mis à jour via OOB
        echo '<div id="clge-nextcloud-actions-container" hx-swap-oob="true">' .
            Clge_Nextcloud_UI::generate_action_buttons() .
            "</div>";
        wp_die();
    }

    /**
     * Affiche le template de configuration Nextcloud.
     */
    public static function render_settings(): void
    {
        if (!current_user_can("manage_options")) {
            wp_die(
                'Vous n\'avez pas les droits nécessaires pour accéder à cette page.',
            );
        }
        include get_template_directory() .
            "/templates/clge-nextcloud-settings.php";
        wp_die();
    }

    /**
     * Récupère les identifiants Nextcloud.
     *
     * @param bool $include_password Si vrai, déchiffre et inclut le mot de passe
     * @return array Les identifiants (url, username, password)
     */
    public static function get_credentials(
        bool $include_password = false,
    ): array {
        $credentials = [
            "url" => get_option("clge_nextcloud_url", ""),
            "username" => get_option("clge_nextcloud_username", ""),
        ];

        if ($include_password) {
            $encrypted = get_option("clge_nextcloud_password_encrypted", "");
            $credentials["password"] = Clge_Nextcloud_Encryption::decrypt(
                $encrypted,
            );
        }

        return $credentials;
    }

    /**
     * Vérifie si Nextcloud est configuré.
     *
     * @return bool
     */
    public static function is_configured(): bool
    {
        $url = get_option("clge_nextcloud_url", "");
        $username = get_option("clge_nextcloud_username", "");
        $encrypted_password = get_option(
            "clge_nextcloud_password_encrypted",
            "",
        );
        return !empty($url) && !empty($username) && !empty($encrypted_password);
    }

    /**
     * Vérifie le mot de passe Nextcloud.
     *
     * @param string $password Le mot de passe à vérifier
     * @return bool
     */
    public static function verify_password(string $password): bool
    {
        $encrypted = get_option("clge_nextcloud_password_encrypted", "");
        if (empty($encrypted)) {
            return false;
        }
        $stored_password = Clge_Nextcloud_Encryption::decrypt($encrypted);
        if ($stored_password === false) {
            return false;
        }
        return hash_equals($stored_password, $password);
    }

    /**
     * Nettoie les identifiants Nextcloud.
     * Utilisé pour la désinstallation ou la réinitialisation.
     */
    public static function cleanup_credentials(): void
    {
        delete_option("clge_nextcloud_url");
        delete_option("clge_nextcloud_username");
        delete_option("clge_nextcloud_password_encrypted");
    }
}
