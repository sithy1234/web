<?php
require __DIR__ . '/db.php';

if (current_user()) {
    redirect('index.php');
}
if (!ALLOW_REGISTER) {
    redirect('login.php');
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $code = trim($_POST['code'] ?? '');

    if ($old['name'] === '' || mb_strlen($old['name']) > 100)  $errors['name'] = 'សូមបញ្ចូលឈ្មោះរបស់អ្នក ។';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))     $errors['email'] = 'សូមបញ្ចូលអ៊ីមែលឱ្យបានត្រឹមត្រូវ ។';
    if (strlen($password) < 8)                                 $errors['password'] = 'ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច ៨ តួអក្សរ ។';
    elseif ($password !== $confirm)                            $errors['confirm'] = 'ពាក្យសម្ងាត់មិនដូចគ្នា ។';
    if (CLASS_CODE !== '' && !hash_equals(CLASS_CODE, $code))  $errors['code'] = 'លេខកូដថ្នាក់មិនត្រឹមត្រូវ ។';

    if (!$errors) {
        try {
            $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)')
                ->execute([$old['name'], $old['email'], password_hash($password, PASSWORD_DEFAULT)]);
            log_in((int)$pdo->lastInsertId());
            redirect('index.php');
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors['email'] = 'មានគណនីប្រើអ៊ីមែលនេះរួចហើយ ។ សូមចូលគណនី ។';
            } else {
                throw $ex;
            }
        }
    }
}

auth_head('ចុះឈ្មោះ');
?>
  <h1>ចុះឈ្មោះ</h1>

  <form method="post" action="register.php" novalidate>
    <?= csrf_field() ?>
    <label>ឈ្មោះ
      <input type="text" name="name" value="<?= e($old['name']) ?>" required autocomplete="name">
      <span class="err"><?= e($errors['name'] ?? '') ?></span>
    </label>
    <label>អ៊ីមែល
      <input type="email" name="email" value="<?= e($old['email']) ?>" required autocomplete="email">
      <span class="err"><?= e($errors['email'] ?? '') ?></span>
    </label>
    <label>ពាក្យសម្ងាត់ (យ៉ាងតិច ៨ តួអក្សរ)
      <input type="password" name="password" required autocomplete="new-password">
      <span class="err"><?= e($errors['password'] ?? '') ?></span>
    </label>
    <label>បញ្ជាក់ពាក្យសម្ងាត់
      <input type="password" name="confirm" required autocomplete="new-password">
      <span class="err"><?= e($errors['confirm'] ?? '') ?></span>
    </label>
    <?php if (CLASS_CODE !== ''): ?>
    <label>លេខកូដថ្នាក់
      <input type="text" name="code" required autocomplete="off">
      <span class="err"><?= e($errors['code'] ?? '') ?></span>
    </label>
    <?php endif; ?>
    <button class="btn" type="submit">បង្កើតគណនី</button>
  </form>

  <p class="auth-alt">មានគណនីរួចហើយ? <a href="login.php">ចូលគណនី</a></p>
<?php auth_foot(); ?>
