<?php
/-if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "#";
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
?>-/
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===== НАСТРОЙКИ =====
$to = "info@kovarumelecky.cz";          // КУДА приходит письмо
$from = "noreply@kovarumelecky.cz";     // С ТОГО ЖЕ ДОМЕНА
$subject = "Zpráva z kontaktního formuláře";

// ===== POST DATA =====
$jmeno   = trim($_POST['jmeno'] ?? '');
$email   = trim($_POST['email'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$zprava  = trim($_POST['zprava'] ?? '');

// ===== ПРОВЕРКА =====
if (!$jmeno || !$email || !$zprava) {
  die("Chybí povinná pole");
}

// ===== HLAVIČKY =====
$headers  = "From: $from\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// ===== ТЕЛО ПИСЬМА =====
$body  = "Jméno: $jmeno\n";
$body .= "Email: $email\n";
$body .= "Telefon: $telefon\n\n";
$body .= "Zpráva:\n$zprava\n";

// ===== ПРИЛОЖЕНИЕ =====
if (!empty($_FILES['soubor']['name'])) {

  $file_tmp  = $_FILES['soubor']['tmp_name'];
  $file_name = basename($_FILES['soubor']['name']);
  $file_type = $_FILES['soubor']['type'];
  $file_size = $_FILES['soubor']['size'];

  // Ограничение размера (5 MB)
  if ($file_size > 5 * 1024 * 1024) {
    die("Soubor je příliš velký");
  }

  $boundary = md5(time());

  $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";

  $message  = "--$boundary\r\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
  $message .= $body . "\r\n";

  $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));

  $message .= "--$boundary\r\n";
  $message .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
  $message .= "Content-Transfer-Encoding: base64\r\n";
  $message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
  $message .= $file_content . "\r\n";
  $message .= "--$boundary--";

} else {
  $headers .= "Content-Type: text/plain; charset=UTF-8";
  $message = $body;
}

// ===== ODESLÁNÍ =====
if (mail($to, $subject, $message, $headers)) {
  echo "OK";
} else {
  echo "ERROR";
}
