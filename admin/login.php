<?php
require_once dirname(__DIR__) . '/config.php';
if (!empty($_SESSION['admin_id'])) redirect('admin/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $username=trim((string)($_POST['username']??'')); $password=(string)($_POST['password']??'');
    $stmt=db()->prepare('SELECT id,username,password_hash FROM admin_users WHERE username=? LIMIT 1'); $stmt->execute([$username]); $user=$stmt->fetch();
    if ($user && password_verify($password,$user['password_hash'])) {
        session_regenerate_id(true); $_SESSION['admin_id']=(int)$user['id']; $_SESSION['admin_username']=$user['username']; redirect('admin/index.php');
    } else $error='Invalid username or password.';
}
$flashes=take_flashes();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login · Shihab Portfolio</title><link rel="stylesheet" href="../assets/css/style.css"><link rel="stylesheet" href="../assets/css/admin.css"></head><body class="admin-body"><main class="login-card"><a class="brand" href="../index.php"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a><p class="admin-top-kicker">PORTFOLIO MANAGEMENT</p><h1>Welcome back.</h1><p class="login-subtitle">Sign in to manage your portfolio content.</p><?php foreach($flashes as $f): ?><div class="flash flash-<?=e($f['type'])?>"><?=e($f['message'])?></div><?php endforeach; ?><?php if($error): ?><div class="flash flash-error"><?=e($error)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label>Username<input name="username" autocomplete="username" required autofocus></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="button button-primary" type="submit">Sign in <span>↗</span></button></form><a class="login-back" href="../index.php">← Back to public website</a></main></body></html>
