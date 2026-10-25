<?php
/** Connexion. */
$pdo = Database::pdo();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    if (!csrf_check()) { $errors[] = "Session expirée, réessayez."; }
    if (!$errors) {
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch();
        if (!$u) {
            log_auth('login_fail_unknown', $email);
            $errors[] = "Aucun compte ne correspond à cette adresse e-mail.";
        } elseif ($u['status'] === 'blocked') {
            $errors[] = "Ce compte est bloqué.";
        } elseif (!password_verify($pass, $u['pass_hash'])) {
            log_auth('login_fail_badpass', $email);
            $errors[] = "Mot de passe incorrect.";
        } else {
            $_SESSION['uid'] = (int)$u['id'];
            log_auth('login_ok', $email);
            redirect('/account');
        }
    }
}
render('auth/login', ['title'=>'Connexion','errors'=>$errors]);
