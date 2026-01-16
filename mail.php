<?php
// On force l'encodage en UTF-8 pour éviter les caractères bizarres
header('Content-Type: text/html; charset=utf-8');

// 1. Récupération des données
$name    = htmlspecialchars($_POST['name'] ?? 'Inconnu');
$email   = htmlspecialchars($_POST['email'] ?? 'Non précisé');
$message = htmlspecialchars($_POST['message'] ?? '');

// 2. Ta clé API Resend
$apiKey ="re_cNa7isXf_5Fa44vzZQ6A8FB9HMA8d14t2"; // Vérifie bien qu'il n'y a pas d'espace au milieu

// 3. Préparation des données
$data = [
    'from'    => 'onboarding@resend.dev',
    'to'      => 'nathan.romero@iut-tarbes.fr',
    'subject' => 'Nouveau contact de : ' . $name,
    'html'    => "
        <h3>Nouveau message de ton portfolio</h3>
        <p><strong>Nom :</strong> {$name}</p>
        <p><strong>Email :</strong> {$email}</p>
        <p><strong>Message :</strong><br>" . nl2br($message) . "</p>
    "
];

// 4. Envoi via cURL
$ch = curl_init('https://api.resend.com/emails');

// VERIFICATION LOCALE : On détecte si on est sur ton ordi ou sur Alwaysdata
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['SERVER_NAME'] == 'localhost';

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ],
    // CORRECTION ICI : Si c'est local, on met FALSE pour ignorer l'erreur SSL
    CURLOPT_SSL_VERIFYPEER => $isLocal ? false : true 
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

// 5. Affichage du résultat
if ($err) {
    echo "Erreur technique (cURL) : " . $err;
} elseif ($httpCode >= 200 && $httpCode < 300) {
    // Redirection vers ton index avec un paramètre de succès
    header('Location: Untitled-1.html?sent=success#contact');
    exit();
} else {
    // Affiche l'erreur de Resend pour comprendre ce qui bloque
    echo "Erreur Resend (Code $httpCode) : " . $response;
}
?>