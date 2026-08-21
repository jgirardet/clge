<?php
/**
 * CLGE Nextcloud Integration - Main Class
 *
 * Point d'entrée pour l'intégration Nextcloud.
 * Charge toutes les classes et initialise les hooks WordPress.
 */

defined("ABSPATH") || exit();

// Vérifier que SECURE_AUTH_KEY est définie
if (!defined("SECURE_AUTH_KEY")) {
    add_action("admin_notices", function () {
        echo '<div class="error"><p><strong>' .
            esc_html__("Erreur de configuration Nextcloud:", "clge") .
            "</strong> " .
            esc_html__(
                "SECURE_AUTH_KEY est requise pour le chiffrement Nextcloud. Vérifiez votre wp-config.php.",
                "clge",
            ) .
            "</p></div>";
    });
    return;
}

// Vérifier que SECURE_AUTH_KEY est suffisamment longue pour AES-256 (32 bytes)
if (strlen(SECURE_AUTH_KEY) < 32) {
    add_action("admin_notices", function () {
        echo '<div class="error"><p><strong>' .
            esc_html__("Erreur de configuration Nextcloud:", "clge") .
            "</strong> " .
            esc_html__(
                "SECURE_AUTH_KEY doit faire au moins 32 caractères pour le chiffrement AES-256. Vérifiez votre wp-config.php.",
                "clge",
            ) .
            "</p></div>";
    });
    return;
}

// Charger les dépendances
require_once __DIR__ . "/parsers.php";
require_once __DIR__ . "/class-nextcloud-encryption.php";
require_once __DIR__ . "/class-nextcloud-settings.php";
require_once __DIR__ . "/class-nextcloud-api.php";
require_once __DIR__ . "/class-nextcloud-calendars.php";
require_once __DIR__ . "/class-nextcloud-events.php";
require_once __DIR__ . "/class-nextcloud-ui.php";
require_once __DIR__ . "/class-nextcloud-debug.php";

// Initialisation des hooks
Clge_Nextcloud_Settings::register_hooks();
Clge_Nextcloud_API::register_hooks();
Clge_Nextcloud_Calendars::register_hooks();
Clge_Nextcloud_Debug::register_hooks();
