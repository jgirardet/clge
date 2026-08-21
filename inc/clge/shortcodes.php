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
 * Affiche les 7 prochains événements des calendriers Nextcloud actifs
 *
 * Utilisation: [clge_cal_events]
 *
 * @param array $atts Attributs du shortcode
 * @return string HTML des événements
 */

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
        elseif ($debut_mois === $fin_mois && $debut_annee === $fin_annee) {
            $date_principale = $debut_jour;
            $date_secondaire = "-" . $fin_jour . "&nbsp;" . $mois_fr[$fin_mois];
        }
        // Mois différents
        else {
            $date_principale = $debut_jour . " " . $mois_fr[$debut_mois] . " -";
            $date_secondaire = "&nbsp;" . $fin_jour . " " . $mois_fr[$fin_mois];
        }

        return [
            "primary" => $date_principale,
            "secondary" => $date_secondaire,
        ];
    }
endif;

if (!function_exists("clge_cal_events_shortcode")) {
    function clge_cal_events_shortcode($atts = [])
    {
        // Récupérer tous les événements depuis Nextcloud (calendriers actifs)
        error_log(
            "CLGE Shortcode: class_exists Clge_Nextcloud_Events = " .
                (class_exists("Clge_Nextcloud_Events") ? "YES" : "NO"),
        );

        if (class_exists("Clge_Nextcloud_Events")) {
            $events = Clge_Nextcloud_Events::get_all_active_events();
            error_log(
                "CLGE Shortcode: " .
                    count($events) .
                    " événements récupérés depuis Nextcloud",
            );
        } else {
            // Si la classe n'est pas disponible, retourner un message
            error_log(
                "CLGE Shortcode: ERREUR - Classe Clge_Nextcloud_Events non disponible",
            );
            return '<p class="clge-no-events">' .
                esc_html__("Nextcloud Events: classe non disponible.", "clge") .
                "</p>";
        }

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

        // Add modal HTML once
        static $modal_added = false;
        if (!$modal_added) {
            echo '<div class="clge-modal-overlay" id="clge-modal-overlay"><div class="clge-modal"><button class="clge-modal-close" id="clge-modal-close">&times;</button><div class="clge-modal-content" id="clge-modal-content"></div></div></div>';
            $modal_added = true;
        }

        // Add JavaScript once
        static $js_added = false;
        if (!$js_added) {
            echo '<script>document.addEventListener("DOMContentLoaded", function() {';
            echo 'var overlay = document.getElementById("clge-modal-overlay");';
            echo 'var modal = document.querySelector(".clge-modal");';
            echo 'var content = document.getElementById("clge-modal-content");';
            echo 'var closeBtn = document.getElementById("clge-modal-close");';
            echo 'var triggers = document.querySelectorAll(".clge-event-description-modal-trigger");';
            echo "triggers.forEach(function(trigger) {";
            echo 'trigger.addEventListener("click", function(e) {';
            echo "e.preventDefault();";
            echo 'content.textContent = this.getAttribute("data-description");';
            echo 'overlay.classList.add("active");';
            echo "});";
            echo "});";
            echo 'overlay.addEventListener("click", function(e) {';
            echo "if (e.target === overlay) {";
            echo 'overlay.classList.remove("active");';
            echo "}";
            echo "});";
            echo 'closeBtn.addEventListener("click", function() {';
            echo 'overlay.classList.remove("active");';
            echo "});";
            echo "});</script>";
            $js_added = true;
        }
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
                        <span class="clge-event-date"><?php echo esc_html(
                            $date_parts["primary"],
                        ); ?></span>
                        <?php if (!empty($date_parts["secondary"])): ?>
                            <span class="clge-event-date"><?php echo esc_html(
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
                            <?php
                            $description = isset($event->description)
                                ? trim($event->description)
                                : "";

                            if (!empty($description)) {
                                if (
                                    filter_var(
                                        $description,
                                        FILTER_VALIDATE_URL,
                                    )
                                ) {
                                    // Cas 1: description est une URL -> lien vers cette URL
                                    $event_link = esc_url($description);
                                    $is_clge_url =
                                        strpos($description, "clge.fr") !==
                                        false;
                                    $event_target = $is_clge_url
                                        ? ""
                                        : ' target="_blank" rel="noopener noreferrer"';
                                    echo '<a href="' .
                                        $event_link .
                                        '"' .
                                        $event_target .
                                        ">";
                                } else {
                                    // Cas 2: description est du texte -> clic ouvre la modale
                                    echo '<a href="#" class="clge-event-description-modal-trigger" data-description="' .
                                        esc_attr($description) .
                                        '">';
                                }
                            } else {
                                // Cas 3: pas de description -> pas de lien du tout
                                echo "<span>";
                            }

                            if (!$event->evt_clge) {
                                echo "Formation:<br/>";
                            }
                            echo esc_html(
                                !empty($event->alias)
                                    ? $event->alias
                                    : $event->nom,
                            );

                            if (!empty($description)) {
                                echo "</a>";
                            } else {
                                echo "</span>";
                            }
                            ?>
                        </div>
                    </div>
                </article>
            <?php
            endforeach; ?>
        </div>

        <?php return ob_get_clean();
    }
}

add_shortcode("clge_cal_events", "clge_cal_events_shortcode");
