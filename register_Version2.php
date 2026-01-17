<?php
session_start();

// 仅作示例：服务器端验证与简单持久化到 session 数组。
// 真实项目请使用数据库持久化，并对输入做更严格验证和防护（CSRF、速率限制等）。

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // 基本验证
    if (strlen($username) < 2) {
        $err = "用户名至少2个字符";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "邮箱格式不正确";
    } elseif (strlen($password) < 6) {
        $err = "密码至少6位";
    } elseif ($password !== $confirm) {
        $err = "两次输入的密码不一致";
    } else {
        // 从 session 中读取已有用户（示例）
        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }
        $users = $_SESSION['users'];

        // 检查邮箱是否已被注册
        foreach ($users as $u) {
            if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
                $err = "该邮箱已被注册";
                break;
            }
        }

        if (!isset($err)) {
            // 使用 password_hash 保存安全哈希
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $users[] = [
                'username' => $username,
                'email' => $email,
                'password_hash' => $hash,
                'created_at' => date('c')
            ];

            $_SESSION['users'] = $users;

            // 注册成功，重定向到登录页（或直接登录并跳转首页）
            header('Location: login.html');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>注册 - 结果</title>
  <style>
    body { font-family: Arial, sans-serif; padding:20px; }
    .msg { padding:12px; border-radius:6px; max-width:600px; margin:40px auto; }
    .error { background:#fdecea; color:#611; border:1px solid #f5c6cb; }
    .success { background:#e9f7ef; color:#0a5; border:1px solid #c3e6cb; }
    a { display:inline-block; margin-top:12px; }
  </style>
</head>
<body>
  <?php if (!empty($err)): ?>
    <div class="msg error">
      <strong>注册失败：</strong>
      <p><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p>
      <a href="register.html">返回注册</a>
    </div>
  <?php else: ?>
    <div class="msg success">
      <strong>注册成功！</strong>
      <p>请使用你的账号登录。</p>
      <a href="login.html">去登录</a>
    </div>
  <?php endif; ?>
</body>
</html>