<?php
/**
 * Nextcloud Settings Template
 * Gère la configuration de connexion Nextcloud
 * Utilise chiffrement AES-256 avec SECURE_AUTH_KEY
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

// Charger les classes Nextcloud pour utiliser les méthodes statiques
require_once get_template_directory() .
    "/inc/clge/nextcloud/class-nextcloud-settings.php";
require_once get_template_directory() .
    "/inc/clge/nextcloud/class-nextcloud-ui.php";

// Récupérer les valeurs existantes depuis les options WordPress
$nextcloud_url = get_option("clge_nextcloud_url", "");
$nextcloud_username = get_option("clge_nextcloud_username", "");
$nextcloud_has_password = !empty(
    get_option("clge_nextcloud_password_encrypted", "")
);

// Vérifier si Nextcloud est configuré
$is_configured = Clge_Nextcloud_Settings::is_configured();
?>
<div class="wrap clge-nextcloud-page">


    <h1>Configuration Nextcloud</h1>

    <div class="clge-notice-wrap">
        <?php if (isset($_GET["error"])): ?>
            <div class="notice notice-error">
                <p><?php echo esc_html(
                    sanitize_text_field($_GET["error"]),
                ); ?></p>
            </div>
        <?php elseif (isset($_GET["message"])): ?>
            <div class="notice notice-success">
                <p><?php echo esc_html(
                    sanitize_text_field($_GET["message"]),
                ); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <section class="clge-card">
        <h2>Paramètres de connexion</h2>
        <p class="clge-muted">Configurez la connexion à votre serveur Nextcloud. Les informations seront chiffrées avec SECURE_AUTH_KEY.</p>

        <div class="clge-info-box">
            <strong>Sécurité :</strong> Le mot de passe est chiffré avec AES-256-CBC utilisant la clé SECURE_AUTH_KEY de WordPress. Il est stocké de manière réversible pour permettre l'authentification Basic Auth.
        </div>

        <form
            id="clge-nextcloud-form"
            hx-post="<?php echo esc_url(admin_url("admin-ajax.php")); ?>"
            hx-target="#test-connection-result"
            hx-swap="innerHTML"
        >
            <input type="hidden" name="action" value="clge_save_nextcloud_settings">
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(
                wp_create_nonce("clge_save_nextcloud_settings"),
            ); ?>">

            <div class="clge-form-grid">
                <div class="clge-field">
                    <label for="nextcloud_url">URL du serveur Nextcloud *</label>
                    <input
                        type="url"
                        id="nextcloud_url"
                        name="nextcloud_url"
                        value="<?php echo esc_url($nextcloud_url); ?>"
                        placeholder="https://votre-serveur.cloud"
                        required
                    >
                    <p class="clge-help-text">Exemple: https://cloud.domain.com</p>
                </div>

                <div class="clge-field">
                    <label for="nextcloud_username">Nom d'utilisateur *</label>
                    <input
                        type="text"
                        id="nextcloud_username"
                        name="nextcloud_username"
                        value="<?php echo esc_attr($nextcloud_username); ?>"
                        placeholder="votre-utilisateur"
                        required
                    >
                </div>

                <div class="clge-field">
                    <label for="nextcloud_password">Mot de passe</label>
                    <input
                        type="password"
                        id="nextcloud_password"
                        name="nextcloud_password"
                        placeholder="Entrez le mot de passe"
                        autocomplete="new-password"
                    >
                    <div id="clge-password-status-container" class="clge-password-status <?php echo $nextcloud_has_password
                        ? "set"
                        : "not-set"; ?>">
                        <?php if ($nextcloud_has_password): ?>
                            <span>✓ Mot de passe configuré</span>
                        <?php else: ?>
                            <span>⚠ Aucun mot de passe configuré</span>
                        <?php endif; ?>
                    </div>
                    <p class="clge-help-text">Laissez vide pour conserver le mot de passe actuel</p>
                </div>
            </div>

            <div class="clge-form-actions">
                <button type="submit" class="clge-submit">
                    <?php echo $nextcloud_has_password
                        ? "Mettre à jour"
                        : "Enregistrer la configuration"; ?>
                </button>
            </div>

            <div id="test-connection-result" class="clge-test-connection-result"></div>
        </form>

        <div id="clge-nextcloud-actions-container">
        <div class="clge-form-actions clge-mt-4">
            <?php if ($nextcloud_has_password): ?>
            <button type="button" class="clge-submit danger"
                    hx-post="<?php echo esc_url(
                        admin_url("admin-ajax.php"),
                    ); ?>"
                    hx-target="#test-connection-result"
                    hx-swap="innerHTML"
                    hx-confirm="⚠️ ATTENTION : Cette action supprimera définitivement le mot de passe stocké. Êtes-vous sûr ?"
                    hx-vals='{"action": "clge_clear_nextcloud_password", "_wpnonce": "<?php echo esc_attr(
                        wp_create_nonce("clge_clear_nextcloud_password"),
                    ); ?>"}'>
                Supprimer le mot de passe
            </button>
            <?php endif; ?>
            <?php if (!empty($nextcloud_url) && !empty($nextcloud_username)): ?>
            <button type="button" class="clge-submit secondary<?php echo $nextcloud_has_password
                ? " clge-ml-2"
                : ""; ?>"
                    hx-post="<?php echo esc_url(
                        admin_url("admin-ajax.php"),
                    ); ?>"
                    hx-target="#test-connection-result"
                    hx-swap="innerHTML"
                    hx-vals='{"action": "clge_test_nextcloud_connection", "_wpnonce": "<?php echo esc_attr(
                        wp_create_nonce("clge_test_nextcloud_connection"),
                    ); ?>"}'>
                Tester la connexion
            </button>
            <?php endif; ?>
        </div>
        </div>
    </section>

    <section class="clge-card">
        <h2>Calendriers à synchroniser</h2>
        <p class="clge-muted">Sélectionnez les calendriers distants Nextcloud que vous souhaitez synchroniser avec votre site.</p>

        <div id="clge-calendars-container" class="clge-mt-4">
            <button type="button" class="clge-submit secondary"
                    hx-get="<?php echo esc_url(admin_url("admin-ajax.php")); ?>"
                    hx-target="#clge-calendars-list"
                    hx-swap="innerHTML"
                    hx-vals='{"action": "clge_load_nextcloud_calendars", "_wpnonce": "<?php echo esc_attr(
                        wp_create_nonce("clge_load_nextcloud_calendars"),
                    ); ?>"}'
                    hx-confirm="Êtes-vous sûr de vouloir recharger les calendriers ? Cela téléchargera la liste depuis Nextcloud et la fusionnera avec vos calendriers connus.">
                Charger les calendriers
            </button>

            <div id="clge-calendars-list" class="clge-mt-3" <?php if (
                $is_configured
            ): ?>hx-get="<?php echo esc_url(
    admin_url("admin-ajax.php"),
); ?>?action=clge_get_known_calendars&_wpnonce=<?php echo esc_attr(
    wp_create_nonce("clge_get_known_calendars"),
); ?>" hx-trigger="load" hx-swap="innerHTML"<?php endif; ?>></div>
        </div>
    </section>

    <section class="clge-card">
        <h2>Documentation</h2>
        <div class="clge-doc-box">
            <p><strong>Comment obtenir les informations de connexion :</strong></p>
            <ol>
                <li>Connectez-vous à votre instance Nextcloud</li>
                <li>Allez dans Paramètres → Sécurité</li>
                <li>Générez un "App Password" (mot de passe d'application) si vous utilisez l'authentification à 2 facteurs</li>
                <li>Copiez l'URL de votre serveur, votre nom d'utilisateur et le mot de passe</li>
            </ol>
            <p>
                <strong>Note :</strong> Ce plugin utilise la clé <code>SECURE_AUTH_KEY</code> de votre <code>wp-config.php</code> pour le chiffrement.
                Si vous changez cette clé, vous devrez ressaisir le mot de passe.
            </p>
        </div>
    </section>

</div>
