<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i oczyszczenie danych z formularza
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subjectType = strip_tags(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Walidacja pól
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    // Ustalenie tematu wiadomości na podstawie wyboru użytkownika
    $subjectMap = [
        'assignment' => 'Photography Assignment Inquiry',
        'licensing' => 'License Purchase Inquiry',
        'press' => 'Press & Media Accreditation Request',
        'other' => 'General Contact Inquiry'
    ];
    $emailSubject = isset($subjectMap[$subjectType]) ? $subjectMap[$subjectType] : 'New Contact Form Submission';
    $emailSubject = "[IPS PRESS Form] " . $emailSubject;

    // Adres docelowy (Twój mail firmowy)
    $recipient = "news@ipspress.com";

    // Formatowanie treści maila (czysty tekst, żeby nie wpadał do spamu)
    $emailContent = "You have a new submission from the IPS PRESS contact form.\n\n";
    $emailContent .= "Name: $name\n";
    $emailContent .= "Email: $email\n";
    $emailContent .= "Subject Type: $subjectType\n\n";
    $emailContent .= "Message:\n$message\n";

    // Nagłówki mailowe zgodne ze standardami serwerów pocztowych
    $headers = "From: news@ipspress.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Próba wysłania
    if (mail($recipient, $emailSubject, $emailContent, $headers)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Mail function failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
