<?php
/**
 * CLGE Nextcloud - Parsers
 *
 * Fonctions de parsing pour iCalendar, dates et adresses mailto
 * Utilisées par l'intégration Nextcloud pour traiter les données CalDAV
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

/**
 * Parse une date iCalendar en objet DateTime
 *
 * Gère les formats :
 * - DATE (YYYYMMDD) avec VALUE=DATE
 * - Date avec timezone (TZID)
 * - Date UTC (se termine par Z)
 * - Date locale (différents formats)
 *
 * @param string $date_string Date au format iCalendar
 * @param array $params Paramètres iCalendar (ex: ['TZID' => 'Europe/Paris'])
 * @return DateTime|null Objet DateTime ou null en cas d'erreur
 */
function clge_parse_icalendar_date($date_string, $params = [])
{
    try {
        // Vérifier si c'est une DATE (jour entier) avec VALUE=DATE
        if (
            isset($params["VALUE"]) &&
            strtoupper($params["VALUE"]) === "DATE"
        ) {
            // Essayer de parser comme date (YYYYMMDD)
            $date = DateTime::createFromFormat("Ymd", $date_string);
            if ($date !== false) {
                // Pour les dates (jour entier), mettre à minuit UTC
                $date->setTime(0, 0, 0);
                $date->setTimezone(new DateTimeZone("UTC"));
                return $date;
            }
        }

        // Vérifier si c'est une date avec timezone
        if (isset($params["TZID"])) {
            $timezone = new DateTimeZone($params["TZID"]);
            $date = new DateTime($date_string, $timezone);
            return $date;
        }

        // Vérifier si la date est en UTC (se termine par Z)
        if (substr($date_string, -1) === "Z") {
            $date = new DateTime(substr($date_string, 0, -1) . "+00:00");
            $date->setTimezone(new DateTimeZone("UTC"));
            return $date;
        }

        // Date sans timezone (format local)
        // Essayer différents formats, en commençant par les plus courants
        $formats = [
            "Ymd\\THis", // 20240101T120000 (iCalendar standard)
            "Ymd", // 20240101 (DATE)
            "Y-m-d\\TH:i:s", // 2024-01-01T12:00:00
            "Y-m-d", // 2024-01-01
            "Ymd\\THis\\Z", // 20240101T120000Z (au cas où)
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $date_string);
            if ($date !== false) {
                // Si pas de timezone spécifiée et que ce n'est pas une date UTC,
                // on suppose UTC pour les formats iCalendar
                if (
                    !isset($params["TZID"]) &&
                    substr($date_string, -1) !== "Z"
                ) {
                    $date->setTimezone(new DateTimeZone("UTC"));
                }
                return $date;
            }
        }

        // Dernier recours : essayer de parser directement
        return new DateTime($date_string);
    } catch (Exception $e) {
        error_log("Erreur de parse de date iCalendar: " . $e->getMessage());
        return null;
    }
}

/**
 * Parse le contenu iCalendar (ICS) et extrait les événements
 *
 * Traite les structures :
 * - BEGIN:VEVENT / END:VEVENT
 * - Propriétés avec paramètres (DTSTART;VALUE=DATE:20240101)
 * - Propriétés simples (SUMMARY:Réunion)
 * - Lignes repliées (continuation lines)
 *
 * @param string $ical_content Contenu iCalendar brut
 * @return array Tableau d'événements parsés
 */
function clge_parse_icalendar_content($ical_content)
{
    $events = [];

    // D'abord, déplier toutes les lignes (unfold)
    // Les lignes qui commencent par un espace ou tab sont des continuations
    $lines = explode("\n", $ical_content);
    $unfolded_lines = [];
    $current_line = "";

    foreach ($lines as $line) {
        $trimmed = ltrim($line);

        // Lignes vides
        if ($trimmed === "") {
            if ($current_line !== "") {
                $unfolded_lines[] = $current_line;
                $current_line = "";
            }
            continue;
        }

        // Lignes repliées (commencent par espace ou tab)
        if (substr($line, 0, 1) === " " || substr($line, 0, 1) === "\t") {
            $current_line .= $trimmed;
        } else {
            // Si on a une ligne en cours, l'ajouter
            if ($current_line !== "") {
                $unfolded_lines[] = $current_line;
            }
            $current_line = $trimmed;
        }
    }

    // Ne pas oublier la dernière ligne
    if ($current_line !== "") {
        $unfolded_lines[] = $current_line;
    }

    // Maintenant parser les événements
    $current_event = null;
    $in_event = false;

    foreach ($unfolded_lines as $line) {
        $trimmed = trim($line);

        // Lignes vides
        if ($trimmed === "") {
            continue;
        }

        // BEGIN:VEVENT
        if ($trimmed === "BEGIN:VEVENT") {
            $current_event = [
                "is_fullday" => false, // Par défaut, ce n'est pas un événement sur toute la journée
                "_date_fields" => [], // Pour suivre quels champs sont de type DATE
            ];
            $in_event = true;
            continue;
        }

        // END:VEVENT
        if ($trimmed === "END:VEVENT") {
            if ($current_event !== null && !empty($current_event)) {
                // Nettoyer les champs temporaires
                unset($current_event["_date_fields"]);
                $events[] = $current_event;
            }
            $current_event = null;
            $in_event = false;
            continue;
        }

        // Si on est dans un événement
        if ($in_event && $current_event !== null) {
            // Trouver le premier :
            $colon_pos = strpos($line, ":");
            if ($colon_pos !== false) {
                $full_property = trim(substr($line, 0, $colon_pos));
                $value = trim(substr($line, $colon_pos + 1));

                // Séparer la propriété des paramètres
                $semicolon_pos = strpos($full_property, ";");
                if ($semicolon_pos !== false) {
                    $property = trim(substr($full_property, 0, $semicolon_pos));
                    $params_str = trim(
                        substr($full_property, $semicolon_pos + 1),
                    );

                    // Parser les paramètres
                    $params = [];
                    $param_parts = explode(";", $params_str);
                    foreach ($param_parts as $param) {
                        if (strpos($param, "=") !== false) {
                            $pair = explode("=", $param, 2);
                            $params[strtoupper(trim($pair[0]))] =
                                $pair[1] ?? "";
                        }
                    }

                    // Traiter les propriétés courantes
                    switch (strtoupper($property)) {
                        case "DTSTART":
                        case "DTEND":
                            // Vérifier si c'est une DATE (jour entier)
                            $is_date =
                                isset($params["VALUE"]) &&
                                $params["VALUE"] === "DATE";

                            // Stocker la date
                            $current_event[
                                strtolower($property)
                            ] = clge_parse_icalendar_date($value, $params);

                            // Mettre à jour is_fullday
                            $current_event["is_fullday"] = $is_date;
                            break;
                        case "CREATED":
                        case "LAST-MODIFIED":
                            $current_event[
                                strtolower($property)
                            ] = clge_parse_icalendar_date($value, $params);
                            break;
                        case "SUMMARY":
                            $current_event["summary"] = $value;
                            break;
                        case "DESCRIPTION":
                            $current_event["description"] = $value;
                            break;
                        case "LOCATION":
                            $current_event["location"] = $value;
                            break;
                        case "UID":
                            $current_event["uid"] = $value;
                            break;
                        case "URL":
                            $current_event["url"] = $value;
                            break;
                        case "ORGANIZER":
                            // Extraire le CN des paramètres s il existe
                            $cn = isset($params["CN"]) ? $params["CN"] : null;
                            // Parser la valeur (qui peut être mailto:email@domaine.fr)
                            $mail_data = clge_parse_mailto($value);
                            // Si on a un CN dans les paramètres, il prime sur celui de mailto
                            $final_cn = !empty($cn) ? $cn : $mail_data["name"];
                            $current_event["organizer"] = [
                                "cn" => $final_cn,
                                "mail" => $mail_data["email"],
                            ];
                            break;
                        case "STATUS":
                            $current_event["status"] = $value;
                            break;
                        case "TRANSP":
                            $current_event["transparency"] = $value;
                            break;
                        case "SEQUENCE":
                            $current_event["sequence"] = (int) $value;
                            break;
                        case "CATEGORIES":
                            $current_event["categories"] = $value;
                            break;
                        case "ATTENDEE":
                            if (!isset($current_event["attendees"])) {
                                $current_event["attendees"] = [];
                            }
                            // Parser l'email depuis value (mailto:email@domaine.fr)
                            $mail_data = clge_parse_mailto($value);

                            // Convertir tous les paramètres en minuscules
                            $lower_params = [];
                            foreach ($params as $key => $param_value) {
                                $lower_params[strtolower($key)] = $param_value;
                            }

                            // Fusionner email et params
                            $attendee = [
                                "email" => $mail_data["email"],
                                "cn" =>
                                    $mail_data["name"] ??
                                    ($lower_params["cn"] ?? null),
                            ];

                            // Ajouter tous les autres paramètres
                            foreach ($lower_params as $key => $param_value) {
                                if ($key !== "cn") {
                                    $attendee[$key] = $param_value;
                                }
                            }

                            $current_event["attendees"][] = $attendee;
                            break;
                        default:
                            // Stocker les autres propriétés
                            if (!isset($current_event["_extra"])) {
                                $current_event["_extra"] = [];
                            }
                            $current_event["_extra"][strtoupper($property)] = [
                                "value" => $value,
                                "params" => $params,
                            ];
                            break;
                    }
                } else {
                    // Pas de paramètres, propriété simple
                    $property = strtoupper($full_property);

                    // Traiter les propriétés courantes
                    switch ($property) {
                        case "DTSTART":
                        case "DTEND":
                        case "CREATED":
                        case "LAST-MODIFIED":
                            $current_event[
                                strtolower($property)
                            ] = clge_parse_icalendar_date($value, []);
                            break;
                        case "SUMMARY":
                            $current_event["summary"] = $value;
                            break;
                        case "DESCRIPTION":
                            $current_event["description"] = $value;
                            break;
                        case "LOCATION":
                            $current_event["location"] = $value;
                            break;
                        case "UID":
                            $current_event["uid"] = $value;
                            break;
                        case "URL":
                            $current_event["url"] = $value;
                            break;
                        case "ORGANIZER":
                            // Parser la valeur (qui peut être mailto:email@domaine.fr)
                            $mail_data = clge_parse_mailto($value);
                            $current_event["organizer"] = [
                                "cn" => $mail_data["name"],
                                "mail" => $mail_data["email"],
                            ];
                            break;
                        case "STATUS":
                            $current_event["status"] = $value;
                            break;
                        case "TRANSP":
                            $current_event["transparency"] = $value;
                            break;
                        case "SEQUENCE":
                            $current_event["sequence"] = (int) $value;
                            break;
                        case "CATEGORIES":
                            $current_event["categories"] = $value;
                            break;
                        default:
                            // Stocker les autres propriétés
                            if (!isset($current_event["_extra"])) {
                                $current_event["_extra"] = [];
                            }
                            $current_event["_extra"][
                                strtoupper($property)
                            ] = $value;
                            break;
                    }
                }
            }
        }
    }

    return $events;
}

/**
 * Parse une adresse mailto et extrait le CN (Common Name) et l'email
 *
 * Gère les formats:
 * - "mailto:email@domaine.fr" -> ['email' => 'email@domaine.fr', 'name' => null]
 * - "mailto:Nom <email@domaine.fr>" -> ['email' => 'email@domaine.fr', 'name' => 'Nom']
 * - "Nom <mailto:email@domaine.fr>" -> ['email' => 'email@domaine.fr', 'name' => 'Nom']
 * - "Nom <email@domaine.fr>" -> ['email' => 'email@domaine.fr', 'name' => 'Nom']
 * - "email@domaine.fr" -> ['email' => 'email@domaine.fr', 'name' => null]
 *
 * @param string $input Chaîne à parser (adresse mailto ou email standard)
 * @return array Tableau associatif avec les clés 'email' et 'name'
 */
function clge_parse_mailto($input)
{
    $result = [
        "email" => "",
        "name" => null,
    ];

    if (empty($input)) {
        return $result;
    }

    // Supprimer le préfixe mailto: s'il est présent
    $clean_input = str_replace("mailto:", "", $input);
    $clean_input = trim($clean_input);

    // Cas 1: Format avec nom et email entre chevrons: "Nom <email@domaine.fr>"
    if (preg_match('/^(.+?)\s*<(.+?)>$/', $clean_input, $matches)) {
        $result["name"] = trim($matches[1]);
        $result["email"] = trim($matches[2]);

        // Nettoyer l'email des éventuels mailto: restants
        $result["email"] = str_replace("mailto:", "", $result["email"]);
        $result["email"] = trim($result["email"]);

        return $result;
    }

    // Cas 2: Simple adresse email (avec ou sans mailto:)
    if (filter_var($clean_input, FILTER_VALIDATE_EMAIL)) {
        $result["email"] = $clean_input;
        return $result;
    }

    // Cas 3: Adresse email entre chevrons sans nom: "<email@domaine.fr>"
    if (preg_match('/^<(.+?)>$/', $clean_input, $matches)) {
        $result["email"] = trim($matches[1]);
        $result["email"] = str_replace("mailto:", "", $result["email"]);
        $result["email"] = trim($result["email"]);
        return $result;
    }

    // Cas 4: Dernier recours - essayer de trouver une adresse email dans la chaîne
    if (
        preg_match(
            "/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/",
            $clean_input,
            $matches,
        )
    ) {
        $result["email"] = $matches[1];

        // Extraire le nom (tout ce qui précède l'email)
        $name_part = str_replace($matches[1], "", $clean_input);
        $name_part = trim(str_replace(["<", ">", "mailto:"], "", $name_part));

        if (!empty($name_part)) {
            $result["name"] = $name_part;
        }
    }

    return $result;
}
