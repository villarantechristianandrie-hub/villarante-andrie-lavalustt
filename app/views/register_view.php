<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Product Manager</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f4f7fb 0%, #e8edf5 100%);
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 2.25rem 2rem;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        h1 { font-size: 1.4rem; margin-bottom: .35rem; }
        p.subtitle { color: #6b7280; font-size: .88rem; margin-bottom: 1.5rem; }
        label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .35rem; }
        input {
            width: 100%;
            padding: .65rem .8rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: .95rem;
            margin-bottom: 1rem;
        }
        input:focus { outline: none; border-color: #2563eb; }
        button {
            width: 100%;
            padding: .7rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .msg.error { padding: .7rem .9rem; border-radius: 8px; font-size: .85rem; margin-bottom: 1rem; background: #fee2e2; color: #991b1b; }
        .footer-link { text-align: center; margin-top: 1.25rem; font-size: .85rem; color: #6b7280; }
        .footer-link a { color: #2563eb; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="card">
    <h1>Create an account</h1>
    <p class="subtitle">Register to manage products.</p>

    <?php if (!empty($error)): ?>
        <div class="msg error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required autofocus>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" autocomplete="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="new-password" minlength="6" required>

        <button type="submit">Register</button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="<?= base_url('login'); ?>">Log in</a>
    </div>
</div>
</body>
</html>
