<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | Product Manager</title>
    <style>
        :root { --ink: #17221d; --muted: #68736d; --paper: #f5f0e8; --panel: #fffdf8; --line: #d9d4c9; --coral: #e85d43; --mint: #c8e6d1; --shadow: 0 24px 70px rgba(31, 39, 32, .14); }
        * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: var(--paper); color: var(--ink); font: 16px/1.5 Georgia, 'Times New Roman', serif; }
    .shell { min-height: 100vh; display: grid; grid-template-columns: minmax(280px, .85fr) minmax(360px, 1.15fr); }
    .brand { position: relative; overflow: hidden; display: flex; align-items: flex-end; padding: clamp(2rem, 6vw, 6rem); background: var(--ink); color: var(--paper); }
    .brand:before { content: ''; position: absolute; width: 24rem; height: 24rem; right: -9rem; top: -8rem; border: 1px solid rgba(245, 240, 232, .22); border-radius: 50%; box-shadow: 0 0 0 3rem rgba(245, 240, 232, .04), 0 0 0 7rem rgba(245, 240, 232, .03); }
    .brand:after { content: ''; position: absolute; left: 13%; top: 20%; width: 7rem; height: 7rem; background: var(--coral); transform: rotate(14deg); }
    .brand-content { position: relative; z-index: 1; max-width: 30rem; }
    .kicker { margin: 0 0 1.2rem; color: var(--mint); font: 700 .75rem/1.2 Arial, sans-serif; letter-spacing: .18em; text-transform: uppercase; }
    h1 { max-width: 12ch; margin: 0; font-size: clamp(3rem, 7vw, 6.5rem); line-height: .9; letter-spacing: -.04em; font-weight: 400; }
    .brand-note { max-width: 25rem; margin: 2rem 0 0; color: #c9d0ca; font: 1rem/1.6 Arial, sans-serif; }
    .form-side { display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .form-wrap { width: min(100%, 27rem); }
    .eyebrow { margin: 0 0 .55rem; color: var(--coral); font: 700 .72rem/1.2 Arial, sans-serif; letter-spacing: .16em; text-transform: uppercase; }
    .form-wrap h2 { margin: 0; font-size: clamp(2rem, 4vw, 3.1rem); line-height: 1; font-weight: 400; }
    .subtitle { margin: .75rem 0 2rem; color: var(--muted); font: .95rem/1.5 Arial, sans-serif; }
    .notice { margin-bottom: 1rem; padding: .8rem 1rem; border-left: 4px solid var(--coral); background: #fff0eb; color: #8b3426; font: .85rem/1.4 Arial, sans-serif; }
    .notice.info { border-color: #6f9f7b; background: var(--mint); color: #245033; }
    form { padding: 1.75rem; background: var(--panel); border: 1px solid var(--line); box-shadow: var(--shadow); }
    label { display: block; margin: 0 0 .4rem; font: 700 .78rem/1.2 Arial, sans-serif; letter-spacing: .04em; text-transform: uppercase; }
    input { width: 100%; margin: 0 0 1.2rem; padding: .85rem .9rem; border: 1px solid var(--line); border-radius: 0; background: #fff; color: var(--ink); font: 1rem Arial, sans-serif; }
    input:focus { outline: 2px solid var(--coral); outline-offset: 2px; border-color: var(--coral); }
    button { width: 100%; padding: .9rem 1rem; border: 0; background: var(--coral); color: #fff; cursor: pointer; font: 700 .85rem Arial, sans-serif; letter-spacing: .08em; text-transform: uppercase; }
    button:hover { background: #c94732; }
    .footer-link { margin: 1.25rem 0 0; color: var(--muted); text-align: center; font: .88rem Arial, sans-serif; }
    .footer-link a { color: var(--ink); font-weight: 700; text-decoration-thickness: 2px; text-underline-offset: 3px; }
        @media (max-width: 760px) { .shell { grid-template-columns: 1fr; } .brand { min-height: 18rem; align-items: center; padding: 2.5rem 1.5rem; } .brand:after { left: auto; right: 12%; top: 15%; } .brand-note { margin-top: 1.25rem; } .form-side { padding: 2.5rem 1.25rem 3rem; } }
    </style>
</head>
<body>
<main class="shell">
    <section class="brand" aria-label="Product Manager">
        <div class="brand-content">
            <p class="kicker">Product Manager / 01</p>
            <h1>Make stock feel simple.</h1>
            <p class="brand-note">A clear workspace for the products your team moves every day.</p>
        </div>
    </section>
    <section class="form-side">
        <div class="form-wrap">
            <p class="eyebrow">Welcome back</p>
            <h2>Sign in.</h2>
            <p class="subtitle">Pick up where you left off in your product desk.</p>

            <?php if (!empty($denied)): ?><div class="notice info">Please sign in to continue.</div><?php endif; ?>
            <?php if (!empty($registered)): ?><div class="notice info">Account created. You can now sign in.</div><?php endif; ?>
            <?php if (!empty($error)): ?><div class="notice"><?= htmlspecialchars($error); ?></div><?php endif; ?>
            <form method="post" action="">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autocomplete="username" required autofocus>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <button type="submit">Enter workspace</button>
            </form>
            <p class="footer-link">New here? <a href="<?= base_url('register'); ?>">Create an account</a></p>
        </div>
    </section>
</main>
</body>
</html>
<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Product Manager</title>
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
        .msg {
            padding: .7rem .9rem;
            border-radius: 8px;
            font-size: .85rem;
            margin-bottom: 1rem;
        }
        .msg.error { background: #fee2e2; color: #991b1b; }
        .msg.info { background: #dbeafe; color: #1e40af; }
        .msg.success { background: #dcfce7; color: #166534; }
        .footer-link { text-align: center; margin-top: 1.25rem; font-size: .85rem; color: #6b7280; }
        .footer-link a { color: #2563eb; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="card">
    <h1>Welcome back</h1>
    <p class="subtitle">Sign in to manage your products.</p>

    <?php if (!empty($denied)): ?>
        <div class="msg info">Please log in to continue.</div>
    <?php endif; ?>
    <?php if (!empty($registered)): ?>
        <div class="msg success">Account created. You can now log in.</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="msg error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('login'); ?>">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <button type="submit">Log In</button>
    </form>

    <div class="footer-link">
        Don't have an account? <a href="<?= base_url('register'); ?>">Register</a>
    </div>
</div>
</body>
</html>
