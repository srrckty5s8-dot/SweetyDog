<?php
// api_piste_handler.php

class PisteService {
    private $clientId;
    private $clientSecret;
    // URL exacte pour l'authentification PISTE
    private $authUrl = "https://piste.gouv.fr/cas/oauth2.0/token";

    public function __construct($envPath) {
        $this->loadEnv($envPath);
        // On utilise trim() ici aussi par sécurité
        $this->clientId = isset($_ENV['CLIENT_ID']) ? trim($_ENV['CLIENT_ID']) : '';
        $this->clientSecret = isset($_ENV['CLIENT_SECRET']) ? trim($_ENV['CLIENT_SECRET']) : '';
    }

    private function loadEnv($path) {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
            
            list($name, $value) = explode('=', $line, 2);
            // On nettoie les espaces et les éventuels guillemets autour de la valeur
            $cleanValue = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[trim($name)] = $cleanValue;
        }
    }

    public function getToken() {
        $ch = curl_init();
        
        // Préparation des données proprement formatées
        $postFields = http_build_query([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'openid'
        ]);

        curl_setopt($ch, CURLOPT_URL, $this->authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        
        // Ajout des en-têtes indispensables pour éviter les redirections 302
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]);

        // Optionnel : Désactiver la vérification SSL si tu es sur un vieux MAMP (à retirer en production)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['access_token'] ?? "Erreur : Token absent de la réponse JSON";
        }
        
        // En cas de 302 ou autre, on renvoie le contenu pour comprendre
        return "Erreur HTTP $httpCode : (Vérifiez votre Client Secret sur PISTE)";
    }
}