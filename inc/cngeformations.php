<?php



define("MoisVersNumero", [
    'janvier'   => 1,
    'février'   => 2,
    'mars'      => 3,
    'avril'     => 4,
    'mai'       => 5,
    'juin'      => 6,
    'juillet'   => 7,
    'août'      => 8,
    'septembre' => 9,
    'octobre'   => 10,
    'novembre'  => 11,
    'décembre'  => 12,
]);

define("ReDateFull", "/(?'ddebut'\d\d)\/(?'mdebut'\d\d)\/(?'ydebut'\d\d) (?'hdebut'\d?\d:\d\d)\|(?'dfin'\d\d)\/(?'mfin'\d\d)\/(?'yfin'\d\d) (?'hfin'\d?\d:\d\d)/");
define("ReHeure", "/(?'hdebut'\d?\d:\d\d)\|(?'hfin'\d?\d:\d\d)/");

// Fonction pour parser une table
function parseFormationsTable(DOMElement $table, string $currentMois, string $annee): array
{
    $events = [];
    $rows = $table->getElementsByTagName('tr');

    $jourEnCours = "";
    $moisenCours = MoisVersNumero[strtolower($currentMois)];
    $numeroEncours = 0;

    foreach ($rows as $row) {
        if (!$row instanceof DOMElement) continue; // Vérifie que $row est un DOMElement

        $cells = $row->getElementsByTagName('td');
        if ($cells->length === 3) {
            $event = new stdClass();

            // on récupère le numéro de jour en cours dans la première colonne
            $jourEnCours = trim($cells->item(0)->firstChild->nodeValue) ?? $jourEnCours;
            $numeroEncours = intval(explode(' ', $jourEnCours)[1] ?? "0") ?? $$numeroEncours;

            // on récupère la case horaire dans le deuxième colone
            $secondTd = $cells->item(1);
            if (!$secondTd instanceof DOMElement) continue; // Vérifie que $secondTd est un DOMElement
            $spans = $secondTd->getElementsByTagName('span');
            $col2 = ($spans->length > 0) ? trim($spans->item(0)->nodeValue) : '';

            // Formattage de cet case : il y a des espaces bizard à supprimer + la fleche entre les 2
            $encoded =  urlencode($col2);
            $encoded = str_replace("+", "", $encoded);
            $encoded = str_replace("%0A%0A", " ", $encoded);
            $encoded = str_replace("%E2%86%92", "|", $encoded);
            $encoded = str_replace(" | ", "|", $encoded);
            $encoded = str_replace("%0A", "", $encoded);
            $caseCol2 = urldecode($encoded);

            // On ne reconnait que 2 types de formats, sinon on ignore
            if (preg_match(ReDateFull, $caseCol2, $matches)) {
                // en premier le format complet qui donne toutes les infos
                $event->debut = DateTime::createFromFormat('d m y H:i', $matches['ddebut'] . ' ' . $matches['mdebut'] . ' ' . $matches['ydebut'] . ' ' . $matches["hdebut"]);
                $event->fin = DateTime::createFromFormat('d m y H:i', $matches['dfin'] . ' ' . $matches['mfin'] . ' ' . $matches['yfin'] . ' ' . $matches["hfin"]);
            } elseif (preg_match(ReHeure, $caseCol2, $matches)) {
                // en deuxième on reconstruit la date  partir du reste (en général formation d'1 seul jour)
                $strDebut = "{$numeroEncours} {$moisenCours} {$annee} {$matches["hdebut"]}";
                $strFin = "{$numeroEncours} {$moisenCours} {$annee} {$matches["hfin"]}";
                $event->debut = DateTime::createFromFormat("d m Y H:i", $strDebut);
                $event->fin = DateTime::createFromFormat("d m Y H:i", $strFin);
            } else {
                continue;
            }

            // on récupère l'abbréviation
            $event->abrev = $spans->item(2)->nodeValue;

            // on récupère le nom et l'url
            $links = $secondTd->getElementsByTagName('a');
            if ($links->length > 0) {
                $link = $links->item(0);
                $event->nom = trim($link->nodeValue);
                $event->url = $link->getAttribute('href');
            } else {
                $event->nom = '';
                $event->url = '';
            }

            // La troisième colonne: on récupère le lieu physique
            $thirdTd = $cells->item(2);
            $event->lieu_physique = '';

            if (!$thirdTd instanceof DOMElement) continue; // Vérifie que $thirdTd est un DOMElement

            $lieuPhysiqueSpans = $thirdTd->getElementsByTagName('span');
            foreach ($lieuPhysiqueSpans as $span) {
                if ($span instanceof DOMElement && strpos($span->getAttribute('class'), 'lieu_physique') !== false) {
                    $event->lieu_physique = trim($span->nodeValue);
                    break;
                }
            }

            $events[] = $event;
        }
    }

    return $events;
}

// Fonction pour parser la liste des mois
function parseListeMois(DOMXPath $xpath,  string $annee): array
{
    $allEvents = [];
    $listMois = $xpath->query('//*[@id="liste_mois"]/li');
    foreach ($listMois as $li) {
        if (!$li instanceof DOMElement) continue; // Vérifie que $li est un DOMElement

        $currentMois = '';
        $h3s = $li->getElementsByTagName('h3');
        if ($h3s->length > 0) {
            $currentMois = trim($h3s->item(0)->firstElementChild->nodeValue);
        }

        $tables = $li->getElementsByTagName('table');
        foreach ($tables as $table) {
            if (!$table instanceof DOMElement) continue; // Vérifie que $table est un DOMElement
            $events = parseFormationsTable($table, $currentMois, $annee);
            $allEvents = array_merge($allEvents, $events);
        }
    }

    return $allEvents;
}


function parseAnnee(DOMXPath $xpath): string
{
    $annee = $xpath->query('//*[@class="year"]');
    $an =  $annee[0]->nodeValue;
    if (!$an) {
        die("Imposssible de parser l'année");
    }
    return $an;
}





function downloadHTML(): string
{
    // Récupérer le contenu de la page avec cURL
    $url = 'http://catalogue-cnge.dendreo.com/calendrier';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    if ($html === false) {
        die("Erreur cURL : " . curl_error($ch));
    }
    curl_close($ch);

    // Enregistrer le contenu pour vérification
    //file_put_contents('debug.html', $html);
    return $html;
}

if (!function_exists('getCNGEFormationEvents')) {
    function getCNGEFormationEvents(): array
    {
        // Créer un objet DOMDocument
        $html = downloadHTML();
        $html = preg_replace('/<head>/i', '<head><meta charset="UTF-8">', $html); //encodage du parser

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        // Appel de la fonction principale
        $annee = parseAnnee($xpath);
        $allEvents = parseListeMois($xpath, $annee);
        return $allEvents;
    }
}


// $aaa = getCNGEFormationEvents();



// Exemple d'utilisation :

/**
 * Sauvegarde les événements dans un fichier csv.
 *
 * @param array $allEvents Tableau d'objets représentant les événements.
 * @param string $filename Nom du fichier de sortie (par défaut : events.csv).
 * @return bool Retourne true si l'écriture a réussi, false sinon.
 */
// function saveEventsToFile(array $allEvents, string $filename = 'events.csv'): bool
// {
//     $file = fopen($filename, 'w');
//     if ($file === false) {
//         return false;
//     }

//     // Écrire le BOM (Byte Order Mark) pour UTF-8 (optionnel mais recommandé pour Excel)
//     fwrite($file, "\xEF\xBB\xBF");

//     // Écrire l'en-tête
//     fputcsv($file, ['Début', 'Fin', 'Nom', 'URL', 'Lieu', 'Abrev'], "\t");

//     // Parcourir chaque événement
//     foreach ($allEvents as $event) {
//         // Écrire les données
//         fputcsv($file, [$event->debut->format("d m y H:i"), $event->fin->format("d m y H:i"), $event->nom, $event->url, $event->lieu_physique, $event->abrev], "\t");
//     }

//     fclose($file);
//     return true;
// }


// saveEventsToFile($aaa);
