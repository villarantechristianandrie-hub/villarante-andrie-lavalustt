<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$is_edit = ($mode === 'edit');
$form_action = $is_edit ? base_url('products/edit/' . $product['id']) : base_url('products/create');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit product' : 'Add product'; ?> | Product Manager</title>
    <style>
        :root { --ink: #17221d; --muted: #68736d; --paper: #f5f0e8; --panel: #fffdf8; --line: #d9d4c9; --coral: #e85d43; --mint: #c8e6d1; --shadow: 0 22px 60px rgba(31, 39, 32, .12); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 3rem 1.5rem; background: radial-gradient(circle at 8% 0, #fff8d9 0, transparent 25rem), var(--paper); color: var(--ink); font: 16px/1.5 Arial, sans-serif; }
        .layout { width: min(100%, 900px); margin: 0 auto; }
        .back { display: inline-block; margin-bottom: 2.5rem; color: var(--ink); font-size: .8rem; font-weight: 700; text-decoration-thickness: 2px; text-underline-offset: 4px; }
        .intro { display: flex; align-items: flex-end; justify-content: space-between; gap: 2rem; margin-bottom: 1.5rem; }
        .kicker { margin: 0 0 .65rem; color: var(--coral); font: 700 .72rem/1.2 Arial, sans-serif; letter-spacing: .18em; text-transform: uppercase; }
        h1 { margin: 0; font: 400 clamp(2.8rem, 7vw, 5.5rem)/.9 Georgia, 'Times New Roman', serif; letter-spacing: -.04em; }
        .intro-note { max-width: 17rem; margin: 0 0 .2rem; color: var(--muted); font-size: .9rem; }
        .notice { margin-bottom: 1rem; padding: .8rem 1rem; border-left: 4px solid var(--coral); background: #fff0eb; color: #8b3426; font-size: .88rem; }
        .notice.success { border-color: #6f9f7b; background: var(--mint); color: #245033; }
        form { padding: clamp(1.25rem, 4vw, 2.5rem); border: 1px solid var(--line); background: var(--panel); box-shadow: var(--shadow); }
        .section-label { display: flex; align-items: center; gap: .8rem; margin: 0 0 1.35rem; color: var(--muted); font: 700 .72rem Arial, sans-serif; letter-spacing: .12em; text-transform: uppercase; }
        .section-label:after { content: ''; height: 1px; flex: 1; background: var(--line); }
        label { display: block; margin: 0 0 .4rem; font: 700 .78rem/1.2 Arial, sans-serif; letter-spacing: .04em; text-transform: uppercase; }
        input, textarea { width: 100%; margin: 0 0 1.35rem; padding: .9rem 1rem; border: 1px solid var(--line); border-radius: 0; background: #fff; color: var(--ink); font: 1rem Arial, sans-serif; }
        textarea { min-height: 8rem; resize: vertical; }
        input:focus, textarea:focus { outline: 2px solid var(--coral); outline-offset: 2px; border-color: var(--coral); }
        .field-hint { margin: -.9rem 0 1.35rem; color: var(--muted); font-size: .78rem; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .actions { display: flex; align-items: center; justify-content: flex-end; gap: .75rem; margin-top: .5rem; padding-top: 1.5rem; border-top: 1px solid var(--line); }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 2.6rem; padding: .7rem 1rem; border: 1px solid transparent; border-radius: 0; cursor: pointer; font: 700 .76rem Arial, sans-serif; letter-spacing: .06em; text-decoration: none; text-transform: uppercase; }
        .btn-primary { background: var(--coral); color: #fff; }
        .btn-primary:hover { background: #c94732; }
        .btn-quiet { border-color: var(--line); color: var(--ink); }
        .btn-quiet:hover { border-color: var(--ink); }
        @media (max-width: 650px) { body { padding: 2rem 1rem; } .intro { align-items: flex-start; flex-direction: column; gap: .8rem; } .intro-note { max-width: none; } .grid { grid-template-columns: 1fr; gap: 0; } .actions { align-items: stretch; flex-direction: column-reverse; } .btn { width: 100%; } }
    </style>
</head>
<body>
<main class="layout">
    <a class="back" href="<?= base_url('products'); ?>">&larr; Back to catalog</a>
    <header class="intro">
        <div><p class="kicker">Product Manager / <?= $is_edit ? 'Edit' : 'New item'; ?></p><h1><?= $is_edit ? 'Refine product.' : 'Add product.'; ?></h1></div>
        <p class="intro-note"><?= $is_edit ? 'Keep the catalog accurate with the latest product details.' : 'Give the next item a clear name, price, and count.'; ?></p>
    </header>

    <?php if (!empty($error)): ?><div class="notice" role="alert"><?= htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="notice success" role="status"><?= htmlspecialchars($success); ?></div><?php endif; ?>

    <form method="post" action="<?= $form_action; ?>">
        <p class="section-label">Product details</p>
        <label for="product_name">Product name</label>
        <input type="text" id="product_name" name="product_name" maxlength="100" required value="<?= htmlspecialchars($product['product_name'] ?? ''); ?>" autofocus>
        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="What should the team know about this item?"><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
        <p class="field-hint">A short description helps the catalog stay scannable.</p>

        <p class="section-label">Commercial details</p>
        <div class="grid">
            <div><label for="price">Price</label><input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($product['price'] ?? ''); ?>"></div>
            <div><label for="quantity">Quantity</label><input type="number" id="quantity" name="quantity" step="1" min="0" required value="<?= htmlspecialchars($product['quantity'] ?? ''); ?>"></div>
        </div>

        <div class="actions"><a class="btn btn-quiet" href="<?= base_url('products'); ?>">Cancel</a><button class="btn btn-primary" type="submit"><?= $is_edit ? 'Save changes' : 'Create product'; ?></button></div>
    </form>
</main>
</body>
</html>
