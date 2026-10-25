<?php
/** Vérification OTP d'inscription puis création du compte. */
$pdo = Database::pdo();
$errors = [];
$pending = $_SESSION['pending_signup'] ?? null;
if (!$pending) { flash('error', "Aucune inscription en attente."); redirect('/register'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    if (!csrf_check()) { $errors[] = "Session expirée, réessayez."; }
    if (!$errors) {
        $st = $pdo->prepare('SELECT * FROM otp_challenges WHERE phone=? AND purpose="signup" AND consumed=0 ORDER BY id DESC LIMIT 1');
        $st->execute([$pending['phone']]);
        $otp = $st->fetch();
        if (!$otp)                                   { $errors[] = "Code introuvable. Recommencez."; }
        elseif (strtotime($otp['expires_at']) < time()) { $errors[] = "Code expiré."; }
        elseif ($otp['attempts'] >= 5)               { $errors[] = "Trop de tentatives."; }
        elseif (!hash_equals($otp['code'], $code))   {
            $pdo->prepare('UPDATE otp_challenges SET attempts=attempts+1 WHERE id=?')->execute([$otp['id']]);
            $errors[] = "Code incorrect.";
        } else {
            $pdo->prepare('UPDATE otp_challenges SET consumed=1 WHERE id=?')->execute([$otp['id']]);
            $pdo->prepare('INSERT INTO users (full_name,email,phone,pass_hash,role) VALUES (?,?,?,?,"customer")')
                ->execute([$pending['full_name'],$pending['email'],$pending['phone'],$pending['pass_hash']]);
            unset($_SESSION['pending_signup']);
            log_auth('signup_ok', $pending['email']);
            flash('ok', "Compte créé ! Vous pouvez vous connecter.");
            redirect('/login');
        }
    }
}
render('auth/verify', ['title'=>'Vérification','errors'=>$errors,'phone'=>$pending['phone']]);
