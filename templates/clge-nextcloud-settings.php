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

    <style>
        .clge-nextcloud-page {
            --clge-bg: #ffffff;
            --clge-surface: #f8fafc;
            --clge-border: #e5e7eb;
            --clge-border-strong: #cbd5e1;
            --clge-text: #1f2937;
            --clge-muted: #6b7280;
            --clge-title: #0f172a;
            --clge-accent: #2563eb;
            --clge-accent-hover: #1d4ed8;
            --clge-success: #16a34a;
            --clge-danger: #dc2626;
            --clge-warning: #f59e0b;
            --clge-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .clge-nextcloud-page h1 {
            margin: 12px 0 24px;
            text-align: center;
            color: var(--clge-title);
            font-size: 30px;
            letter-spacing: 0.2px;
        }

        .clge-nextcloud-page h2 {
            margin: 0 0 14px;
            color: var(--clge-title);
            font-size: 20px;
        }

        .clge-card {
            background: var(--clge-bg);
            border: 1px solid var(--clge-border);
            border-radius: 12px;
            box-shadow: var(--clge-shadow);
            padding: 18px;
            margin-bottom: 18px;
        }

        .clge-muted {
            color: var(--clge-muted);
            font-size: 13px;
            margin-top: -4px;
            margin-bottom: 12px;
        }

        .clge-notice-wrap .notice {
            border-radius: 8px;
            margin: 0 0 16px;
        }

        .clge-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .clge-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .clge-field label {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.03em;
        }

        .clge-field input,
        .clge-field select {
            width: 100%;
            height: 38px;
            padding: 8px 10px;
            border: 1px solid var(--clge-border-strong);
            border-radius: 8px;
            background: #fff;
            color: var(--clge-text);
            box-sizing: border-box;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .clge-field input:focus,
        .clge-field select:focus {
            border-color: var(--clge-accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .clge-field input[type="password"] {
            font-family: monospace;
            letter-spacing: 0.5px;
        }

        .clge-submit {
            height: 38px;
            padding: 0 14px;
            border: 0;
            border-radius: 8px;
            background: var(--clge-accent);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.2s ease, transform 0.05s ease;
        }

        .clge-submit:hover,
        .clge-submit:focus {
            background: var(--clge-accent-hover);
        }

        .clge-submit:active {
            transform: translateY(1px);
        }

        .clge-submit.secondary {
            background: #64748b;
        }

        .clge-submit.secondary:hover,
        .clge-submit.secondary:focus {
            background: #475569;
        }

        .clge-submit.danger {
            background: var(--clge-danger);
        }

        .clge-submit.danger:hover,
        .clge-submit.danger:focus {
            background: #991b1b;
        }

        .clge-info-box {
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px 14px;
            color: #0369a1;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .clge-info-box strong {
            font-weight: 600;
            color: #0284c7;
        }

        .clge-password-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            margin-top: 4px;
        }

        .clge-password-status.set {
            color: var(--clge-success);
        }

        .clge-password-status.not-set {
            color: var(--clge-warning);
        }

        .clge-form-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .clge-test-connection-result {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
        }

        .clge-test-connection-result.success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            display: block;
        }

        .clge-test-connection-result.error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            display: block;
        }

        @media (max-width: 860px) {
            .clge-nextcloud-page h1 {
                font-size: 25px;
            }

            .clge-form-grid {
                grid-template-columns: 1fr;
            }

            .clge-form-actions {
                flex-direction: column;
            }

            .clge-form-actions .clge-submit {
                width: 100%;
            }
        }

        .clge-calendar-item {
            margin: 8px 0;
            padding: 8px;
            border: 1px solid var(--clge-border);
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .clge-calendar-item:hover {
            border-color: var(--clge-border-strong);
            box-shadow: var(--clge-shadow);
        }

        .clge-calendar-item input[type="checkbox"] {
            margin: 0;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .clge-calendar-item label {
            flex: 1;
            cursor: pointer;
            margin: 0;
            line-height: 1.4;
        }

        .clge-calendar-item label strong {
            color: var(--clge-text);
            display: block;
        }

        .clge-calendar-item label small {
            color: var(--clge-muted);
            font-size: 11px;
        }

        .clge-calendar-item label code {
            font-size: 11px;
            color: var(--clge-muted);
            background: var(--clge-surface);
            padding: 2px 4px;
            border-radius: 3px;
        }

        .clge-calendar-item.error {
            color: var(--clge-danger);
            background: #fee2e2;
            border-color: #fecaca;
        }
    </style>

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
                    <p class="clge-muted" style="font-size: 11px; margin-top: 2px;">Exemple: https://cloud.domain.com</p>
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
                    <p class="clge-muted" style="font-size: 11px; margin-top: 2px;">Laissez vide pour conserver le mot de passe actuel</p>
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
        <div class="clge-form-actions" style="margin-top: 16px;">
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
            <button type="button" class="clge-submit secondary"
                    hx-post="<?php echo esc_url(
                        admin_url("admin-ajax.php"),
                    ); ?>"
                    hx-target="#test-connection-result"
                    hx-swap="innerHTML"
                    hx-vals='{"action": "clge_test_nextcloud_connection", "_wpnonce": "<?php echo esc_attr(
                        wp_create_nonce("clge_test_nextcloud_connection"),
                    ); ?>"}'
                    style="<?php echo $nextcloud_has_password
                        ? "margin-left: 8px;"
                        : ""; ?>">
                Tester la connexion
            </button>
            <?php endif; ?>
        </div>
        </div>
    </section>

    <section class="clge-card">
        <h2>Calendriers à synchroniser</h2>
        <p class="clge-muted">Sélectionnez les calendriers distants Nextcloud que vous souhaitez synchroniser avec votre site.</p>

        <div id="clge-calendars-container" style="margin-top: 16px;">
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

            <div id="clge-calendars-list" style="margin-top: 12px;" <?php if (
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
        <div style="background: var(--clge-surface); padding: 12px; border-radius: 8px;">
            <p style="margin: 0 0 8px 0;"><strong>Comment obtenir les informations de connexion :</strong></p>
            <ol style="margin: 0 0 0 18px; padding: 0;">
                <li>Connectez-vous à votre instance Nextcloud</li>
                <li>Allez dans Paramètres → Sécurité</li>
                <li>Générez un "App Password" (mot de passe d'application) si vous utilisez l'authentification à 2 facteurs</li>
                <li>Copiez l'URL de votre serveur, votre nom d'utilisateur et le mot de passe</li>
            </ol>
            <p style="margin: 8px 0 0 0; font-size: 12px;">
                <strong>Note :</strong> Ce plugin utilise la clé <code>SECURE_AUTH_KEY</code> de votre <code>wp-config.php</code> pour le chiffrement.
                Si vous changez cette clé, vous devrez ressaisir le mot de passe.
            </p>
        </div>
    </section>

</div>
