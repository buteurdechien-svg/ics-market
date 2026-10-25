<?php
/** Mot de passe oublié (simulé). */
$pdo = Database::pdo();
$errors = [];
$info = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!csrf_check()) { $errors[] = "Session expirée, réessayez."; }
    if (!$errors) {
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch();
        if (!$u) {
            $errors[] = "Cette adresse n'est associée à aucun compte.";
        } else {
            $code = (string)random_int(100000, 999999);
            $pdo->prepare('INSERT INTO otp_challenges (user_id,phone,code,purpose,expires_at) VALUES (?,?,?,?,?)')
                ->execute([$u['id'], $u['phone'], $code, 'reset', date('Y-m-d H:i:s', time() + 600)]);
            Mailer::sendOtp($u['phone'], $code);
            log_auth('reset_requested', $email);
            $info = "Un code de réinitialisation a été envoyé. Consultez Mailpit.";
        }
    }
}
render('auth/forgot', ['title'=>'Mot de passe oublié','errors'=>$errors,'info'=>$info]);
