<?php
/**
 * CLGE Nextcloud - UI Handler
 *
 * Gère la génération du HTML pour l'interface Nextcloud :
 * - Boutons d'action (Tester, Supprimer)
 * - Formulaires de configuration
 */

defined("ABSPATH") || exit();

class Clge_Nextcloud_UI
{
    /**
     * Génère le HTML des boutons Supprimer et Tester.
     * Utilisé dans les formulaires de configuration Nextcloud.
     *
     * @return string HTML des boutons
     */
    public static function generate_action_buttons(): string
    {
        $admin_ajax_url = esc_url(admin_url("admin-ajax.php"));
        $has_password = !empty(
            get_option("clge_nextcloud_password_encrypted", "")
        );
        $has_url = !empty(get_option("clge_nextcloud_url", ""));
        $has_username = !empty(get_option("clge_nextcloud_username", ""));

        $html = '<div class="clge-form-actions clge-mt-4">';

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

            $ml_class = $has_password ? " clge-ml-2" : "";
            $html .=
                '<button type="button" class="clge-submit secondary' .
                $ml_class .
                '"
                hx-post="' .
                $admin_ajax_url .
                '"
                hx-target="#test-connection-result"
                hx-swap="innerHTML"
                hx-vals="' .
                esc_attr($vals_test) .
                '">
                Tester la connexion
            </button>';
        }

        $html .= "</div>";
        return $html;
    }
}
