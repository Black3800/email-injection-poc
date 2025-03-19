<?php
require '../vendor/autoload.php';
use GuzzleHttp\Client;

function sendEmail($to, $subject, $body) {
    $domain = "mail.anakint.com";
    $apiKey = "---";

    $client = new Client();
    try {
        $response = $client->request('POST', "https://api.eu.mailgun.net/v3/$domain/messages", [
            'auth' => ['api', $apiKey],
            'form_params' => [
                'from' => "Support <no-reply@$domain>",
                'to' => $to,
                'subject' => $subject,
                'text' => $body
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}
?>
