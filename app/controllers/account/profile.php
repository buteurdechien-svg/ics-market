<?php
/** Profil client : informations + photo + mot de passe. */
$u = require_login();
$pdo = Database::pdo();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'info';

    if ($action === 'info') {
        $name  = trim($_POST['full_name'] ?? '');
        $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
        if (!csrf_check()) { $errors[] = "Session expirée, réessayez."; }
        if (mb_strlen($name) < 3) { $errors[] = "Nom invalide."; }
        if (!$errors) {
            $pdo->prepare('UPDATE users SET full_name=?, phone=? WHERE id=?')->execute([$name,$phone,$u['id']]);
            flash('ok', "Profil mis à jour.");
            redirect('/profile');
        }
    }
    elseif ($action === 'avatar') {
        if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $dir = config()['storage_path'] . '/../public/uploads';
            @mkdir($dir, 0775, true);
            $fname = basename($_FILES['avatar']['name']);
            move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . '/' . $fname);
            $pdo->prepare('UPDATE users SET avatar=? WHERE id=?')->execute([$fname, $u['id']]);
            flash('ok', "Photo de profil mise à jour.");
            redirect('/profile');
        } else {
            $errors[] = "Aucun fichier reçu.";
        }
    }
    elseif ($action === 'password') {
        $cur=$_POST['current']??''; $new=$_POST['new']??''; $new2=$_POST['new2']??'';
        if (!csrf_check()) { $errors[]="Session expirée."; }
        if (!password_verify($cur, $u['pass_hash'])) { $errors[]="Mot de passe actuel incorrect."; }
        if (strlen($new) < 6) { $errors[]="Le nouveau mot de passe est trop court."; }
        if ($new !== $new2) { $errors[]="La confirmation ne correspond pas."; }
        if (!$errors) {
            $pdo->prepare('UPDATE users SET pass_hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_BCRYPT),$u['id']]);
            flash('ok',"Mot de passe mis à jour.");
            redirect('/profile');
        }
    }
}
$u = current_user();
render('account/profile', ['title'=>'Mon profil','user'=>$u,'errors'=>$errors]);
