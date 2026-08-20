<?php
/**
 * CLGE Nextcloud - Encryption Handler
 *
 * Gère le chiffrement/déchiffrement des données sensibles (mot de passe)
 * Utilise SECURE_AUTH_KEY de WordPress pour le chiffrement AES-256-CBC
 */

defined('ABSPATH') || exit;

class Clge_Nextcloud_Encryption
{
    /**
     * Chiffre une donnée avec AES-256-CBC.
     *
     * @param string $data La donnée à chiffrer
     * @return string La donnée chiffrée encodée en base64 (IV + données)
     */
    public static function encrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }

        $key = SECURE_AUTH_KEY;
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);

        if ($encrypted === false) {
            return '';
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Déchiffre une donnée chiffrée avec AES-256-CBC.
     *
     * @param string $encrypted_data La donnée chiffrée (base64 encoded)
     * @return string|false La donnée déchiffrée ou false en cas d'erreur
     */
    public static function decrypt(string $encrypted_data): string|false
    {
        if (empty($encrypted_data)) {
            return false;
        }

        $key = SECURE_AUTH_KEY;

        $data = base64_decode($encrypted_data);
        if ($data === false) {
            return false;
        }

        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);

        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}
