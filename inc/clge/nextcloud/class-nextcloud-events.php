<?php
/**
 * CLGE Nextcloud - Events Handler
 *
 * Récupère et formate les événements des calendriers Nextcloud actifs
 * pour une intégration transparente avec le shortcode [clge_cal_events]
 */

defined("ABSPATH") || exit();

class Clge_Nextcloud_Events
{
    /**
     * Récupère tous les événements des calendriers Nextcloud actifs
     *
     * @return array Tableau d'événements au format CLGE
     */
    public static function get_all_active_events(): array
    {
        // Vérifier que Nextcloud est configuré
        if (!Clge_Nextcloud_Settings::is_configured()) {
            // Debug:Nextcloud non configuré
            error_log(
                "CLGE Nextcloud Events: Nextcloud non configuré (URL, utilisateur ou mot de passe manquant)",
            );
            return [];
        }

        // Récupérer tous les calendriers
        $all_calendars = Clge_Nextcloud_Calendars::get_calendars();

        // Debug: nombre de calendriers total
        error_log(
            "CLGE Nextcloud Events: " .
                count($all_calendars) .
                " calendriers configurés au total",
        );

        // Filtrer pour garder uniquement les calendriers actifs
        $active_calendars = array_filter($all_calendars, function ($calendar) {
            return !empty($calendar["active"]);
        });

        // Debug: nombre de calendriers actifs
        error_log(
            "CLGE Nextcloud Events: " .
                count($active_calendars) .
                " calendriers actifs",
        );

        // Si aucun calendrier actif, retourner tableau vide
        if (empty($active_calendars)) {
            error_log(
                "CLGE Nextcloud Events: Aucun calendrier actif. Activez au moins un calendrier dans l'admin.",
            );
            return [];
        }

        // Récupérer les événements de chaque calendrier actif
        $all_events = [];
        
        // Récupérer l'URL de base du serveur Nextcloud pour construire les URLs absolues
        $nextcloud_base_url = rtrim(get_option("clge_nextcloud_url", ""), "/");
        
        foreach ($active_calendars as $calendar) {
            $calendar_url = $calendar["url"];
            $calendar_name = $calendar["name"] ?? "unknown";
            
            // Si l'URL du calendrier est relative (commence par /), la rendre absolue
            if (strpos($calendar_url, "/") === 0) {
                $calendar_url = $nextcloud_base_url . $calendar_url;
            }
            
            error_log(
                "CLGE Nextcloud Events: Fetching events from calendar: " .
                    $calendar_name .
                    " (" .
                    $calendar_url .
                    ")",
            );

            $events = Clge_Nextcloud_API::fetch_calendar_events($calendar_url);

            // Si erreur, logger le détail
            if (is_wp_error($events)) {
                error_log(
                    "CLGE Nextcloud Events: ERROR for calendar '" .
                        $calendar_name .
                        "': " .
                        $events->get_error_code() .
                        " - " .
                        $events->get_error_message(),
                );
            } else {
                // Si pas d'erreur, fusionner avec les autres événements
                $event_count = count($events);
                error_log(
                    "CLGE Nextcloud Events: Got " .
                        $event_count .
                        " events from calendar '" .
                        $calendar_name .
                        "'",
                );
                $all_events = array_merge($all_events, $events);
            }
        }

        // Appliquer le mapping vers le format CLGE
        return array_map([self::class, "map_to_clge_format"], $all_events);
    }

    /**
     * Mappe un événement Nextcloud vers le format CLGE attendu par le shortcode
     *
     * IMPORTANT: Pour les événements "full-day", dtend est EXCLUSIF dans iCalendar.
     * On soustrait donc 1 jour à dtend pour obtenir la date de fin inclusive.
     *
     * Exemple: Un événement du 20 au 21 août (full-day) a:
     * - dtstart: 2024-08-20 00:00:00
     * - dtend: 2024-08-21 00:00:00 (exclusif)
     * Après correction: fin = 2024-08-20 23:59:59 (ou 2024-08-20 pour affichage)
     *
     * @param array $nc_event Événement Nextcloud (avec dtstart, dtend, summary, etc.)
     * @return object Événement au format CLGE
     */
    public static function map_to_clge_format(array $nc_event): object
    {
        $event = new stdClass();

        // === DATES ===
        // dtstart est toujours présent et valide
        $event->debut = $nc_event["dtstart"];

        // dtend peut ne pas être présent (rare), on utilise dtstart dans ce cas
        if (
            isset($nc_event["dtend"]) &&
            $nc_event["dtend"] instanceof DateTime
        ) {
            $event->fin = clone $nc_event["dtend"];

            // CORRECTION FULL-DAY: dtend est EXCLUSIF dans iCalendar
            // Pour les événements sur toute la journée, on soustrait 1 jour
            if (!empty($nc_event["is_fullday"])) {
                $event->fin->modify("-1 day");
            }
        } else {
            // Si pas de dtend, utiliser dtstart (événement ponctuel)
            $event->fin = clone $nc_event["dtstart"];
        }

        // === TITRE ET ALIAS ===
        $summary = $nc_event["summary"] ?? "";
        $event->nom = $summary;
        $event->alias = $summary;

        // === ABREVIATION ===
        $event->abrev = self::generate_abbreviation($summary);

        // === LIEU ===
        $event->lieu_physique = $nc_event["location"] ?? "";

        // === URL ===
        $event->url = $nc_event["url"] ?? "";

        // === DESCRIPTION ===
        $event->description = $nc_event["description"] ?? "";

        // === TYPE D'EVENEMENT ===
        // Tous les événements Nextcloud sont considérés comme des événements CLGE
        // (pas de distinction CLGE/Formation comme dans l'ancien système)
        $event->evt_clge = true;

        return $event;
    }

    /**
     * Génère une abréviation depuis un titre
     *
     * Prend la première lettre de chaque mot (max 10 caractères)
     *
     * @param string $title Titre de l'événement
     * @return string Abréviation en majuscules
     */
    private static function generate_abbreviation(string $title): string
    {
        if (empty(trim($title))) {
            return "";
        }

        $words = explode(" ", $title);
        $abbrev = "";

        foreach ($words as $word) {
            if (strlen($abbrev) >= 10) {
                break;
            }
            $first_char = substr($word, 0, 1);
            if ($first_char !== "") {
                $abbrev .= strtoupper($first_char);
            }
        }

        return $abbrev;
    }
}
