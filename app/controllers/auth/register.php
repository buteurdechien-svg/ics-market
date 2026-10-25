<?php
/** Inscription client : saisie puis OTP de vérification. */
$pdo = Database::pdo();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (!csrf_check())                          { $errors[] = "Session expirée, réessayez."; }
    if (mb_strlen($name) < 3)                   { $errors[] = "Indiquez votre nom complet."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Adresse e-mail invalide."; }
    if (!preg_match('/^\+?\d{8,15}$/', $phone))  { $errors[] = "Numéro de téléphone invalide."; }
    if (strlen($pass) < 6)                       { $errors[] = "Le mot de passe doit faire au moins 6 caractères."; }
    if ($pass !== $pass2)                        { $errors[] = "Les deux mots de passe ne correspondent pas."; }

    if (!$errors) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $st->execute([$email]);
        if ((int)$st->fetchColumn() > 0) { $errors[] = "Impossible de créer le compte avec ces informations."; }
    }

    if (!$errors) {
        $code = (string)random_int(100000, 999999);
        $pdo->prepare('INSERT INTO otp_challenges (phone, code, purpose, expires_at) VALUES (?,?,?,?)')
            ->execute([$phone, $code, 'signup', date('Y-m-d H:i:s', time() + 600)]);
        $_SESSION['pending_signup'] = [
            'full_name'=>$name, 'email'=>$email, 'phone'=>$phone,
            'pass_hash'=>password_hash($pass, PASSWORD_BCRYPT),
        ];
        Mailer::sendOtp($phone, $code);
        log_auth('otp_sent', $email);
        flash('info', "Un code de vérification a été envoyé. Consultez-le dans Mailpit.");
        redirect('/verify');
    }
    $_SESSION['_old'] = $_POST;
}
render('auth/register', ['title'=>'Créer un compte','errors'=>$errors]);
