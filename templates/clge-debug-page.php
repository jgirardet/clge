<?php
/**
 * CLGE Debug Page Template
 * Affiche les outils de débogage pour Nextcloud
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

// Vérification des capabilities
if (!current_user_can("manage_options")) {
    wp_die("Vous n'avez pas les droits nécessaires pour accéder à cette page.");
}

// Charger la classe des calendriers
require_once get_template_directory() .
    "/inc/clge/nextcloud/class-nextcloud-calendars.php";

$admin_ajax_url = esc_url(admin_url("admin-ajax.php"));
$debug_nonce = esc_attr(wp_create_nonce("clge_debug_nextcloud_events"));
?>
<div class="wrap clge-debug-page">
    <h1>Debug Nextcloud</h1>

    <div class="clge-card" style="margin-top: 20px;">
        <h2>Événements Nextcloud</h2>
        <p class="clge-muted">Affiche les événements bruts d'un calendrier Nextcloud.</p>

        <form id="clge-debug-form"
              hx-post="<?php echo $admin_ajax_url; ?>"
              hx-target="#clge-debug-events-result"
              hx-swap="innerHTML"
              style="margin-top: 16px;">

            <input type="hidden" name="action" value="clge_debug_nextcloud_events">
            <input type="hidden" name="_wpnonce" value="<?php echo $debug_nonce; ?>">

            <div class="clge-field">
                <label for="clge-debug-calendar-select">Sélectionner un calendrier</label>
                <select id="clge-debug-calendar-select" name="calendar_url"
                        style="width: 100%; height: 38px; padding: 8px 10px; border: 1px solid var(--clge-border-strong); border-radius: 8px; background: #fff; color: var(--clge-text);">
                    <option value="">-- Choisir un calendrier --</option>
                    <?php
                    // Charger les calendriers configurés
                    $calendars = Clge_Nextcloud_Calendars::get_calendars();

                    // Si aucun calendrier, afficher un message
                    if (empty($calendars)):
                        echo '<option value="">Aucun calendrier configuré. Chargez les calendriers depuis l\'onglet Nextcloud.</option>';
                    else:
                        foreach ($calendars as $calendar):
                            if (
                                !empty($calendar["url"]) &&
                                !empty($calendar["name"])
                            ):
                                echo '<option value="' .
                                    esc_attr($calendar["url"]) .
                                    '">' .
                                    esc_html($calendar["name"]) .
                                    "</option>";
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div style="margin-top: 12px;">
                <button type="submit" class="clge-submit secondary">
                    Charger les événements
                </button>
            </div>
        </form>

        <div id="clge-debug-events-result" style="margin-top: 16px;"></div>
    </div>
</div>

<style>
    .clge-debug-page {
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

    .clge-debug-page h1 {
        margin: 12px 0 24px;
        color: var(--clge-title);
        font-size: 30px;
        letter-spacing: 0.2px;
    }

    .clge-debug-page h2 {
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
</style>
