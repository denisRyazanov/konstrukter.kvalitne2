<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "info@kovarumelecky.cz";
    $subject = "Nová zpráva z webu - kontaktní formulář";

    $jmeno = htmlspecialchars($_POST["jmeno"]);
    $email = htmlspecialchars($_POST["email"]);
    $telefon = htmlspecialchars($_POST["telefon"]);
    $zprava = htmlspecialchars($_POST["zprava"]);

    $message = "Nová zpráva z webu:\n\n";
    $message .= "Jméno: $jmeno\n";
    $message .= "E-mail: $email\n";
    $message .= "Telefon: $telefon\n\n";
    $message .= "Zpráva:\n$zprava\n";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Zpracování přílohy
    if (isset($_FILES["soubor"]) && $_FILES["soubor"]["error"] == 0) {
        $file_tmp = $_FILES["soubor"]["tmp_name"];
        $file_name = $_FILES["soubor"]["name"];
        $file_type = $_FILES["soubor"]["type"];
        $file_size = $_FILES["soubor"]["size"];
        $file_data = chunk_split(base64_encode(file_get_contents($file_tmp)));

        $boundary = md5(time());

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= "$message\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
        $body .= "$file_data\r\n";
        $body .= "--$boundary--";
    } else {
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        $body = $message;
    }

    if (mail($to, $subject, $body, $headers)) {
        echo " Zpráva byla úspěšně odeslána.";
    } else {
        echo "? Došlo k chybě při odesílání zprávy.";
    }
}
?>
