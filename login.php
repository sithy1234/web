<?php
require __DIR__ . '/db.php';

if (current_user()) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (($_SESSION['lock_until'] ?? 0) > time()) {
        $error = 'ព្យាយាមច្រើនដងពេក ។ សូមរង់ចាំមួយនាទី ហើយសាកល្បងម្តងទៀត ។';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($_SESSION['fails'], $_SESSION['lock_until']);
            log_in((int)$user['id']);
            redirect('index.php');
        }

        $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
        if ($_SESSION['fails'] >= 5) {
            $_SESSION['lock_until'] = time() + 60;
            $_SESSION['fails'] = 0;
        }
        $error = 'អ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវ ។';
    }
}

auth_head('ចូលគណនី');
?>
  <h1>ចូលគណនី</h1>
  <p>សូមចូលគណនី ដើម្បីមើលមេរៀន ។</p>
  <?php if ($error): ?><p class="notice bad" role="alert"><?= e($error) ?></p><?php endif; ?>

  <form method="post" action="login.php">
    <?= csrf_field() ?>
    <label>អ៊ីមែល
      <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email" autofocus>
    </label>
    <label>ពាក្យសម្ងាត់
      <input type="password" name="password" required autocomplete="current-password">
    </label>
    <button class="btn" type="submit">ចូលគណនី</button>
  </form>

  <?php if (ALLOW_REGISTER): ?>
    <p class="auth-alt">មិនទាន់មានគណនី? <a href="register.php">ចុះឈ្មោះ</a></p>
  <?php endif; ?>
<?php auth_foot(); ?>
