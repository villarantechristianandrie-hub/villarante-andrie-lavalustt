<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$is_admin = (($_SESSION['role'] ?? null) === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Product Manager</title>
    <style>
        :root { --ink: #17221d; --muted: #68736d; --paper: #f5f0e8; --panel: #fffdf8; --line: #d9d4c9; --coral: #e85d43; --mint: #c8e6d1; --yellow: #f1c75b; --shadow: 0 18px 50px rgba(31, 39, 32, .1); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 90% 0, #fff8d9 0, transparent 28rem), var(--paper); color: var(--ink); font: 16px/1.5 Arial, sans-serif; }
        .wrap { width: min(1160px, calc(100% - 3rem)); margin: 0 auto; padding: 3rem 0 4rem; }
        .topbar { display: flex; align-items: flex-end; justify-content: space-between; gap: 1.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--line); }
        .kicker { margin: 0 0 .7rem; color: var(--coral); font: 700 .72rem/1.2 Arial, sans-serif; letter-spacing: .18em; text-transform: uppercase; }
        h1 { margin: 0; font: 400 clamp(2.6rem, 6vw, 5.4rem)/.9 Georgia, 'Times New Roman', serif; letter-spacing: -.04em; }
        .top-actions { display: flex; align-items: center; gap: .7rem; flex-wrap: wrap; justify-content: flex-end; }
        .user { color: var(--muted); font-size: .85rem; }
        .role { display: inline-block; margin-left: .35rem; padding: .2rem .45rem; background: var(--mint); color: #245033; font-size: .68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 2.5rem; padding: .65rem .9rem; border: 1px solid transparent; border-radius: 0; text-decoration: none; cursor: pointer; font: 700 .76rem Arial, sans-serif; letter-spacing: .06em; text-transform: uppercase; }
        .btn-primary { background: var(--coral); color: #fff; }
        .btn-primary:hover { background: #c94732; }
        .btn-quiet { border-color: var(--line); background: transparent; color: var(--ink); }
        .btn-quiet:hover { border-color: var(--ink); }
        .btn-danger { padding: .35rem .5rem; border: 0; background: transparent; color: #ad3d2e; }
        .btn-danger:hover { text-decoration: underline; }
        .overview { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; margin: 2rem 0; border: 1px solid var(--line); background: var(--line); }
        .metric { padding: 1.1rem 1.2rem; background: var(--panel); }
        .metric-label { margin: 0 0 .4rem; color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .metric-value { margin: 0; font: 2rem/1 Georgia, 'Times New Roman', serif; }
        .metric-alert .metric-value { color: var(--coral); }
        .notice { margin-bottom: 1.25rem; padding: .8rem 1rem; border-left: 4px solid #6f9f7b; background: var(--mint); color: #245033; font-size: .88rem; }
        .notice.error { border-color: var(--coral); background: #fff0eb; color: #8b3426; }
        .table-frame { overflow: hidden; border: 1px solid var(--line); background: var(--panel); box-shadow: var(--shadow); }
        .table-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; padding: 1.15rem 1.25rem; border-bottom: 1px solid var(--line); }
        .table-heading h2 { margin: 0; font: 400 1.6rem/1 Georgia, 'Times New Roman', serif; }
        .table-heading span { color: var(--muted); font-size: .82rem; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; min-width: 760px; border-collapse: collapse; }
        th, td { padding: 1rem 1.25rem; text-align: left; border-bottom: 1px solid var(--line); vertical-align: middle; }
        th { color: var(--muted); font-size: .7rem; letter-spacing: .1em; text-transform: uppercase; }
        tbody tr:hover { background: #fff9ed; }
        tbody tr:last-child td { border-bottom: 0; }
        .product-name { font-weight: 700; }
        .product-id { display: block; margin-top: .2rem; color: var(--muted); font-size: .72rem; }
        .description { max-width: 25rem; color: var(--muted); }
        .price { font-weight: 700; white-space: nowrap; }
        .quantity { font-weight: 700; }
        .quantity.low { color: var(--coral); }
        .created { color: var(--muted); font-size: .8rem; white-space: nowrap; }
        .row-actions { display: flex; align-items: center; gap: .55rem; white-space: nowrap; }
        .edit-link { color: var(--ink); font-size: .8rem; font-weight: 700; text-decoration-thickness: 2px; text-underline-offset: 3px; }
        form.inline { display: inline; }
        .empty { padding: 4rem 1.5rem; color: var(--muted); text-align: center; }
        .empty strong { display: block; margin-bottom: .35rem; color: var(--ink); font: 400 1.7rem Georgia, 'Times New Roman', serif; }
        @media (max-width: 720px) { .wrap { width: min(100% - 2rem, 1160px); padding-top: 2rem; } .topbar { align-items: flex-start; flex-direction: column; } .top-actions { justify-content: flex-start; } .overview { grid-template-columns: 1fr; } .table-heading { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
<main class="wrap">
    <header class="topbar">
        <div>
            <p class="kicker">Product Manager / Inventory</p>
            <h1>Products.</h1>
        </div>
        <div class="top-actions">
            <span class="user">Signed in as <strong><?= htmlspecialchars($_SESSION['username'] ?? ''); ?></strong><?php if (!$is_admin): ?><span class="role">View only</span><?php endif; ?></span>
            <?php if ($is_admin): ?><a class="btn btn-primary" href="<?= base_url('products/create'); ?>">+ Add product</a><?php endif; ?>
            <a class="btn btn-quiet" href="<?= base_url('logout'); ?>">Sign out</a>
        </div>
    </header>

    <section class="overview" aria-label="Inventory summary">
        <div class="metric"><p class="metric-label">Catalog size</p><p class="metric-value"><?= $product_count; ?></p></div>
        <div class="metric metric-alert"><p class="metric-label">Needs attention</p><p class="metric-value"><?= $low_stock; ?></p></div>
        <div class="metric"><p class="metric-label">Access level</p><p class="metric-value"><?= $is_admin ? 'Admin' : 'Read'; ?></p></div>
    </section>

    <?php if (!empty($success)): ?><div class="notice"><?= htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="notice error"><?= htmlspecialchars($error); ?></div><?php endif; ?>

    <section class="table-frame">
        <div class="table-heading"><h2>Current catalog</h2><span><?= $product_count; ?> <?= $product_count === 1 ? 'item' : 'items'; ?> tracked</span></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th>Description</th><th>Price</th><th>Quantity</th><th>Created</th><?php if ($is_admin): ?><th>Actions</th><?php endif; ?></tr></thead>
                <tbody>
                <?php if (!empty($products)): foreach ($products as $product): ?>
                    <tr>
                        <td><span class="product-name"><?= htmlspecialchars($product['product_name']); ?></span><span class="product-id">#<?= htmlspecialchars($product['id']); ?></span></td>
                        <td class="description"><?= htmlspecialchars($product['description']); ?></td>
                        <td class="price">&#8369;<?= number_format((float) $product['price'], 2); ?></td>
                        <td class="quantity <?= (int) $product['quantity'] <= 5 ? 'low' : ''; ?>"><?= htmlspecialchars($product['quantity']); ?></td>
                        <td class="created"><?= htmlspecialchars($product['created_at'] ?? ''); ?></td>
                        <?php if ($is_admin): ?><td><div class="row-actions"><a class="edit-link" href="<?= base_url('products/edit/' . $product['id']); ?>">Edit</a><form class="inline" method="post" action="<?= base_url('products/delete/' . $product['id']); ?>" onsubmit="return confirm('Delete this product?');"><button type="submit" class="btn-danger">Delete</button></form></div></td><?php endif; ?>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="<?= $is_admin ? 6 : 5; ?>" class="empty"><strong>Your catalog is clear.</strong><?= $is_admin ? 'Add a product to start tracking inventory.' : 'There are no products to display yet.'; ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Product Manager</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f4f7fb 0%, #e8edf5 100%);
            color: #1f2937;
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
        }
        .wrap { max-width: 1000px; margin: 0 auto; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        h1 { font-size: 1.6rem; }
        .actions { display: flex; gap: .6rem; align-items: center; }
        .btn {
            display: inline-block;
            padding: .55rem 1rem;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-muted { background: #e5e7eb; color: #1f2937; }
        .btn-muted:hover { background: #d1d5db; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-sm { padding: .4rem .75rem; font-size: .8rem; }
        .msg { padding: .7rem .9rem; border-radius: 8px; font-size: .85rem; margin-bottom: 1.25rem; }
        .msg.success { background: #dcfce7; color: #166534; }
        .msg.error { background: #fee2e2; color: #991b1b; }
        .panel {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .85rem 1.1rem; text-align: left; font-size: .9rem; }
        th { background: #2563eb; color: #fff; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eef2ff; }
        td { border-bottom: 1px solid #f1f5f9; }
        td.desc { max-width: 260px; color: #4b5563; }
        td.numeric { text-align: right; white-space: nowrap; }
        .row-actions { display: flex; gap: .5rem; }
        .empty { padding: 2rem; text-align: center; color: #6b7280; }
        form.inline { display: inline; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <h1>Products</h1>
        <div class="actions">
            <span style="font-size:.85rem;color:#6b7280;">
                Signed in as <strong><?= htmlspecialchars($_SESSION['username'] ?? ''); ?></strong>
                <?php if (!$is_admin): ?>
                    <span style="background:#e5e7eb;color:#4b5563;padding:.15rem .5rem;border-radius:6px;font-size:.75rem;margin-left:.4rem;">view only</span>
                <?php endif; ?>
            </span>
            <?php if ($is_admin): ?>
                <a class="btn btn-primary" href="<?= base_url('products/create'); ?>">+ Add Product</a>
            <?php endif; ?>
            <a class="btn btn-muted" href="<?= base_url('logout'); ?>">Logout</a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="msg success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="msg error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Created</th>
                    <?php if ($is_admin): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($product['id']); ?></td>
                            <td><?= htmlspecialchars($product['product_name']); ?></td>
                            <td class="desc"><?= htmlspecialchars($product['description']); ?></td>
                            <td class="numeric">₱<?= number_format((float) $product['price'], 2); ?></td>
                            <td class="numeric"><?= htmlspecialchars($product['quantity']); ?></td>
                            <td><?= htmlspecialchars($product['created_at'] ?? ''); ?></td>
                            <?php if ($is_admin): ?>
                            <td>
                                <div class="row-actions">
                                    <a class="btn btn-muted btn-sm" href="<?= base_url('products/edit/' . $product['id']); ?>">Edit</a>
                                    <form class="inline" method="post" action="<?= base_url('products/delete/' . $product['id']); ?>" onsubmit="return confirm('Delete this product?');">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $is_admin ? 7 : 6; ?>" class="empty">
                        <?= $is_admin ? 'No products yet. Click "Add Product" to create one.' : 'No products yet.'; ?>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
