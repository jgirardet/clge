<?php
/**
 * Shortcodes pour le thème Clge
 */

// Empêcher l'accès direct au fichier
if (!defined("ABSPATH")) {
    exit();
}

/**
 * Shortcode [clge_cal_events]
 * Affiche les 7 prochains événements du calendrier clge_cal_events
 *
 * Utilisation: [clge_cal_events]
 *
 * @param array $atts Attributs du shortcode
 * @return string HTML des événements
 */
if (!function_exists("clge_cal_events_shortcode")):
    function clge_cal_events_shortcode($atts = [])
    {
        // Récupérer tous les événements
        $events = clge_get_all_events();

        // Filtrer: garder seulement les événements futurs et limiter à 7
        $now = new DateTime();
        $upcoming_events = [];

        foreach ($events as $event) {
            $debut =
                $event->debut instanceof DateTime
                    ? $event->debut
                    : new DateTime($event->debut);
            if ($debut >= $now) {
                $upcoming_events[] = $event;
            }
        }

        // Trier par date de début (le plus proche en premier)
        usort($upcoming_events, function ($a, $b) {
            $a_debut =
                $a->debut instanceof DateTime
                    ? $a->debut
                    : new DateTime($a->debut);
            $b_debut =
                $b->debut instanceof DateTime
                    ? $b->debut
                    : new DateTime($b->debut);
            return $a_debut <=> $b_debut;
        });

        // Limiter à 7 événements
        if (is_front_page()) {
            $upcoming_events = array_slice($upcoming_events, 0, 7);
        }

        // Si aucun événement
        if (empty($upcoming_events)) {
            return '<p class="clge-no-events">' .
                esc_html__("Aucun événement à afficher.", "clge") .
                "</p>";
        }

        // Démarrer le tampon de sortie
        ob_start();

        // Inclure le CSS inline uniquement si pas déjà fait
        static $css_included = false;
        if (!$css_included) { ?>
            <style id="clge-cal-events-styles">
                        .clge-event-box {
                            display: flex;
                            justify-content: space-between;
                            flex-direction: column;
                            /*align-items: center;*/
                            margin-bottom: 10px;
                            border: 0 black solid;
                            border-radius: 10px;
                            background-color: #c5c9cc33;
                            padding: 5px;
                            box-shadow: rgba(0, 0, 0, 0.05) 0 2px 32px, rgba(0, 0, 0, 0.05) 0 2px 1px;
                        }
                        .clge-event-left {
                            display: flex;
                            align-items: center;
                            justify-content: left;
                        }
                        .clge-event-date-primary {
                            color: #1b6db5;
                            font-weight: 600;
                        }
                        .clge-event-lieu {
                            color: #1b6db5;
                            text-transform: uppercase;
                            font-weight: 600;
                            flex:1;
                            text-align: center  ;
                        }
                        .clge-event-nom {
                            color: #f29816;
                            font-weight: 700;
                            display: flex;
                            justify-content: right;
                            text-align: right;

                        }
                        .clge-event-description {
                            color: #666;
                            font-size: 0.9em;
                            margin-top: 5px;
                            text-align: right;
                        }
                        </style>
            <?php $css_included = true;}

        // Fonction helper pour formater les dates
        if (!function_exists("clge_format_date_range")):
            function clge_format_date_range($debut, $fin)
            {
                $mois_fr = [
                    1 => "janvier",
                    2 => "février",
                    3 => "mars",
                    4 => "avril",
                    5 => "mai",
                    6 => "juin",
                    7 => "juillet",
                    8 => "août",
                    9 => "septembre",
                    10 => "octobre",
                    11 => "novembre",
                    12 => "décembre",
                ];

                $debut_jour = (int) $debut->format("j");
                $debut_mois = (int) $debut->format("n");
                $debut_annee = (int) $debut->format("Y");

                $fin_jour = (int) $fin->format("j");
                $fin_mois = (int) $fin->format("n");
                $fin_annee = (int) $fin->format("Y");

                $date_principale = $debut_jour;
                $date_secondaire = "";

                // Même jour
                if (
                    $debut_jour === $fin_jour &&
                    $debut_mois === $fin_mois &&
                    $debut_annee === $fin_annee
                ) {
                    $date_secondaire = "&nbsp;" . $mois_fr[$debut_mois];
                }
                // Même mois et année
                elseif (
                    $debut_mois === $fin_mois &&
                    $debut_annee === $fin_annee
                ) {
                    $date_principale = $debut_jour;
                    $date_secondaire =
                        "-" . $fin_jour . "&nbsp;" . $mois_fr[$fin_mois];
                }
                // Mois différents
                else {
                    $date_principale =
                        $debut_jour . " " . $mois_fr[$debut_mois] . " -";
                    $date_secondaire =
                        "&nbsp;" . $fin_jour . " " . $mois_fr[$fin_mois];
                }

                return [
                    "primary" => $date_principale,
                    "secondary" => $date_secondaire,
                ];
            }
        endif;
        ?>

        <div class="clge-events-layout">
            <?php foreach ($upcoming_events as $event):

                $debut =
                    $event->debut instanceof DateTime
                        ? $event->debut
                        : new DateTime($event->debut);
                $fin =
                    $event->fin instanceof DateTime
                        ? $event->fin
                        : new DateTime($event->fin);

                $date_parts = clge_format_date_range($debut, $fin);
                ?>
                <article class="clge-event-box">
                    <div class="clge-event-left">
                        <span class="clge-event-date-primary"><?php echo esc_html(
                            $date_parts["primary"],
                        ); ?></span>
                        <?php if (!empty($date_parts["secondary"])): ?>
                            <span class="clge-event-date-primary"><?php echo esc_html(
                                $date_parts["secondary"],
                            ); ?>

                            </span>
                        <?php endif; ?>
                                                <span class="clge-event-lieu"><?php echo esc_html(
                                                    $event->lieu_physique,
                                                ); ?></span>
                    </div>
                    <div class="clge-event-right">
                        <div class="clge-event-nom ">
                            <a href="<?php echo isset($event->url) &&
                            !empty($event->url)
                                ? esc_url($event->url)
                                : the_permalink(); ?>" <?php echo !$event->evt_clge
    ? ' target="_blank" rel="noopener noreferrer"'
    : " "; ?>>
                                <?php if (!$event->evt_clge): ?>
                                                                Formation:</br>
                                <?php endif; ?>
                                <?php echo esc_html(
                                    !empty($event->alias)
                                        ? $event->alias
                                        : $event->nom,
                                ); ?></a>
                        </div>
                    </div>
                    <?php if (!is_front_page()): ?>
                    <div class="clge-event-description">
                                                <?php echo isset(
                                                    $event->description,
                                                )
                                                    ? esc_html(
                                                        $event->description,
                                                    )
                                                    : ""; ?>
                    </div>
                    <?php endif; ?>
                </article>
            <?php
            endforeach; ?>
        </div>

        <?php return ob_get_clean();
    }
    add_shortcode("clge_cal_events", "clge_cal_events_shortcode");
endif;
