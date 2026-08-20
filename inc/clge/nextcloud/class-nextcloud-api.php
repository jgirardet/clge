<?php
/**
 * CLGE Nextcloud - API Handler
 *
 * Centralise TOUS les appels API vers Nextcloud
 * - Test de connexion
 * - Récupération des calendriers (CalDAV PROPFIND)
 * - Récupération des événements (iCalendar export)
 */

defined('ABSPATH') || exit;

class Clge_Nextcloud_API
{
    /**
     * Enregistre les hooks AJAX pour l'API.
     */
    public static function register_hooks(): void
    {
        add_action('wp_ajax_clge_test_nextcloud_connection', [self::class, 'test_connection']);
    }

    /**
     * Effectue une requête HTTP vers Nextcloud avec gestion centralisée.
     *
     * @param string $url URL complète de la requête
     * @param string $method Méthode HTTP (GET, POST, PROPFIND, etc.)
     * @param array $args Arguments supplémentaires pour wp_remote_request
     * @return array|WP_Error Résultat ou erreur
     */
    private static function request(string $url, string $method = 'GET', array $args = []): array|WP_Error
    {
        $default_args = [
            'timeout' => 30,
            'headers' => [],
        ];

        $merged_args = array_merge($default_args, $args);
        $merged_args['method'] = $method;

        $response = wp_remote_request($url, $merged_args);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);

        if ($status_code < 200 || $status_code >= 300) {
            $error_msg = 'Erreur HTTP ' . $status_code . ' pour l\'URL: ' . esc_url($url);
            if (!empty($body)) {
                if (preg_match('/<s:message>(.*?)<\/s:message>/', $body, $matches)) {
                    $error_msg .= ' | Message: ' . trim($matches[1]);
                } elseif (preg_match('/<message>(.*?)<\/message>/', $body, $matches)) {
                    $error_msg .= ' | Message: ' . trim($matches[1]);
                } else {
                    $error_msg .= ' | Corps: ' . esc_html(substr($body, 0, 200));
                }
            }
            return new WP_Error('nextcloud_http_error', $error_msg);
        }

        return [
            'status_code' => $status_code,
            'body' => $body,
            'headers' => $headers,
        ];
    }

    /**
     * Teste la connexion à Nextcloud.
     * Vérifie que les identifiants sont valides.
     */
    public static function test_connection(): void
    {
        // Vérification de sécurité
        if (!check_ajax_referer('clge_test_nextcloud_connection', '_wpnonce')) {
            echo '<div class="clge-test-connection-result error">Erreur de sécurité. Veuillez réessayer.</div>';
            wp_die();
        }

        // Vérification des capabilities
        if (!current_user_can('manage_options')) {
            echo '<div class="clge-test-connection-result error">Vous n\'avez pas les droits nécessaires.</div>';
            wp_die();
        }

        $credentials = Clge_Nextcloud_Settings::get_credentials(true);
        if (empty($credentials['url']) || empty($credentials['username']) || empty($credentials['password'])) {
            echo '<div class="clge-test-connection-result error">Configuration incomplète. Veuillez d\'abord sauvegarder l\'URL, le nom d\'utilisateur et le mot de passe.</div>';
            wp_die();
        }

        // Normaliser l'URL de base pour le test de connexion
        $normalized_url = rtrim($credentials['url'], '/');
        if (strpos($normalized_url, '/remote.php/dav') === false) {
            $normalized_url .= '/remote.php/dav';
        }

        // Construire les credentials Basic Auth
        $credentials_base64 = base64_encode($credentials['username'] . ':' . $credentials['password']);
        unset($credentials['password']);

        // Effectuer la requête
        $args = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials_base64,
            ],
        ];
        unset($credentials_base64);

        $response = self::request($normalized_url, 'GET', $args);

        if (is_wp_error($response)) {
            echo '<div class="clge-test-connection-result error">Erreur de connexion: ' . esc_html($response->get_error_message()) . '</div>';
            wp_die();
        }

        echo '<div class="clge-test-connection-result success">Connexion réussie (Code: ' . esc_html($response['status_code']) . ')</div>';
        wp_die();
    }

    /**
     * Récupère la liste des calendriers depuis Nextcloud via CalDAV PROPFIND.
     *
     * @return array|WP_Error Tableau de calendriers ou erreur
     */
    public static function fetch_calendars(): array|WP_Error
    {
        if (!Clge_Nextcloud_Settings::is_configured()) {
            return new WP_Error(
                'nextcloud_not_configured',
                'Configuration Nextcloud incomplète. Veuillez d\'abord configurer l\'URL, le nom d\'utilisateur et le mot de passe.'
            );
        }

        $credentials = Clge_Nextcloud_Settings::get_credentials(true);
        $base_url = untrailingslashit($credentials['url']);
        $username = $credentials['username'];
        $password = $credentials['password'];

        // Normaliser l'URL de base pour CalDAV
        $normalized_base_url = rtrim($base_url, '/');
        if (strpos($normalized_base_url, '/remote.php/dav') === false) {
            $normalized_base_url .= '/remote.php/dav';
        }
        $caldav_url = $normalized_base_url . '/calendars/' . rawurlencode($username) . '/';

        $auth = base64_encode($username . ':' . $password);
        unset($password);

        // Requête PROPFIND pour récupérer les calendriers
        $xml_body = '<?xml version="1.0" encoding="utf-8" ?>
<d:propfind xmlns:d="DAV:" xmlns:cs="http://calendarserver.org/ns/" xmlns:cal="urn:ietf:params:xml:ns:caldav">
  <d:prop>
    <d:displayname/>
    <d:resourcetype/>
    <cs:getctag/>
  </d:prop>
</d:propfind>';

        $args = [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/xml',
                'Depth' => '1',
            ],
            'body' => $xml_body,
        ];
        unset($auth, $xml_body);

        $response = self::request($caldav_url, 'PROPFIND', $args);
        if (is_wp_error($response)) {
            return $response;
        }

        $body = $response['body'];
        if (empty($body) || trim($body) === '') {
            return new WP_Error(
                'caldav_empty_response',
                'La réponse CalDAV est vide pour l\'URL: ' . esc_url($caldav_url) . '. Vérifiez que l\'URL et les identifiants sont corrects.'
            );
        }

        // Parser la réponse XML
        $calendars = [];
        try {
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($body);
            if ($xml === false) {
                $errors = libxml_get_errors();
                $error_msg = 'Erreur de parse XML';
                if (!empty($errors)) {
                    $error_msg .= ': ' . $errors[0]->message;
                }
                return new WP_Error('caldav_parse_error', $error_msg);
            }

            $xml->registerXPathNamespace('d', 'DAV:');
            $xml->registerXPathNamespace('cs', 'http://calendarserver.org/ns/');
            $xml->registerXPathNamespace('caldav', 'urn:ietf:params:xml:ns:caldav');

            $responses = $xml->xpath('//d:response');
            foreach ($responses as $response_node) {
                $href_elements = $response_node->xpath('.//d:href');
                if (empty($href_elements)) {
                    continue;
                }
                $url = (string) $href_elements[0];

                $displayname_elements = $response_node->xpath('.//d:displayname');
                $displayname = empty($displayname_elements) ? 'Calendrier sans nom' : (string) $displayname_elements[0];

                // Vérifier que c'est un calendrier (et non un autre type de ressource)
                $resourcetype_elements = $response_node->xpath('.//d:resourcetype');
                if (!empty($resourcetype_elements)) {
                    $resourcetype = $resourcetype_elements[0];
                    $calendar_comp = $resourcetype->xpath('.//cal:calendar | .//cs:calendar | .//d:calendar');
                    if (empty($calendar_comp)) {
                        continue; // Skip si ce n'est pas un calendrier
                    }
                }

                $calendar_id = basename(rtrim($url, '/'));
                $calendars[] = [
                    'name' => $displayname,
                    'id' => $calendar_id,
                    'url' => $url,
                ];
            }
        } catch (Exception $e) {
            return new WP_Error('caldav_parse_error', 'Erreur lors du parse du XML CalDAV: ' . $e->getMessage());
        }

        return $calendars;
    }

    /**
     * Récupère les événements d'un calendrier Nextcloud.
     * Utilise l'export iCalendar via ?export.
     *
     * @param string $calendar_url URL complète du calendrier CalDAV
     * @param string|null $start_date Date de début au format Y-m-d (optionnel)
     * @param string|null $end_date Date de fin au format Y-m-d (optionnel)
     * @return array|WP_Error Tableau d'événements ou erreur
     */
    public static function fetch_calendar_events(
        string $calendar_url,
        ?string $start_date = null,
        ?string $end_date = null
    ): array|WP_Error {
        if (!Clge_Nextcloud_Settings::is_configured()) {
            return new WP_Error(
                'nextcloud_not_configured',
                'Configuration Nextcloud incomplète. Veuillez d\'abord configurer l\'URL, le nom d\'utilisateur et le mot de passe.'
            );
        }

        $credentials = Clge_Nextcloud_Settings::get_credentials(true);
        $auth = base64_encode($credentials['username'] . ':' . $credentials['password']);
        unset($credentials['password']);

        // Ajouter ?export à l'URL pour récupérer tous les événements en format iCalendar
        $export_url = add_query_arg('export', '', $calendar_url);

        $args = [
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Accept' => 'text/calendar',
            ],
        ];
        unset($auth);

        $response = self::request($export_url, 'GET', $args);
        if (is_wp_error($response)) {
            return $response;
        }

        $body = $response['body'];
        if (empty($body) || trim($body) === '') {
            return new WP_Error(
                'caldav_events_empty_response',
                'La réponse CalDAV pour les événements est vide pour l\'URL: ' . esc_url($export_url)
            );
        }

        // Parser le contenu iCalendar
        try {
            $events = clge_parse_icalendar_content($body);
            if (!empty($events)) {
                // Ajouter le champ calendar pour indiquer la source
                foreach ($events as &$event) {
                    $event['calendar'] = $calendar_url;
                }
            }
            return $events;
        } catch (Exception $e) {
            return new WP_Error(
                'caldav_events_parse_error',
                'Erreur lors du parse du contenu iCalendar: ' . $e->getMessage()
            );
        }
    }
}
