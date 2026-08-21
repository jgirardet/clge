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

    <div class="clge-card clge-mt-5">
        <h2>Événements Nextcloud</h2>
        <p class="clge-muted">Affiche les événements bruts d'un calendrier Nextcloud.</p>

        <form id="clge-debug-form" class="clge-mt-4"
              hx-post="<?php echo $admin_ajax_url; ?>"
              hx-target="#clge-debug-events-result"
              hx-swap="innerHTML">

            <input type="hidden" name="action" value="clge_debug_nextcloud_events">
            <input type="hidden" name="_wpnonce" value="<?php echo $debug_nonce; ?>">

            <div class="clge-field">
                <label for="clge-debug-calendar-select">Sélectionner un calendrier</label>
                <select id="clge-debug-calendar-select" name="calendar_url">
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

            <div class="clge-mt-3">
                <button type="submit" class="clge-submit secondary">
                    Charger les événements
                </button>
            </div>
        </form>

        <div id="clge-debug-events-result" class="clge-mt-4"></div>
    </div>
</div>
