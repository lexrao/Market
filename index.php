<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:    #fdf6ec;
            --brown:    #3b2314;
            --amber:    #c87941;
            --rust:     #a84c2f;
            --gold:     #e8a440;
            --sand:     #e8d5b7;
            --text:     #2a1a0e;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── noise grain overlay ── */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* ── hero ── */
        .hero {
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* left panel */
        .hero-left {
            position: relative;
            background: var(--brown);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5rem 4rem 5rem 6rem;
            z-index: 1;
            clip-path: polygon(0 0, 92% 0, 100% 50%, 92% 100%, 0 100%);
        }

        .hero-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 70%, rgba(200,121,65,0.25) 0%, transparent 60%);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232,164,64,0.15);
            border: 1px solid rgba(232,164,64,0.4);
            color: var(--gold);
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 100px;
            width: fit-content;
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease both;
        }

        .badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
        }

        .store-name {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3.5rem, 6vw, 6rem);
            font-weight: 900;
            line-height: 0.95;
            color: var(--cream);
            letter-spacing: -0.02em;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .store-name em {
            display: block;
            font-style: italic;
            color: var(--gold);
        }

        .tagline {
            margin-top: 1.5rem;
            font-size: 1rem;
            font-weight: 300;
            color: rgba(253,246,236,0.55);
            line-height: 1.7;
            max-width: 340px;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .cta-row {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--gold);
            color: var(--brown);
        }

        .btn-primary:hover {
            background: #f0b050;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(232,164,64,0.35);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid rgba(253,246,236,0.25);
            color: var(--cream);
        }

        .btn-secondary:hover {
            background: rgba(253,246,236,0.08);
            transform: translateY(-2px);
        }

        .btn svg { flex-shrink: 0; }

        /* right panel */
        .hero-right {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5rem 5rem 5rem 6rem;
            gap: 1.5rem;
            animation: fadeIn 0.8s 0.4s ease both;
        }

        /* decorative circles */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .deco-circle-1 {
            width: 420px; height: 420px;
            top: -80px; right: -80px;
            border: 1px solid rgba(168,76,47,0.12);
        }
        .deco-circle-2 {
            width: 260px; height: 260px;
            bottom: 60px; left: 30px;
            border: 1px solid rgba(232,164,64,0.1);
        }

        /* card grid */
        .card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .nav-card {
            background: #fff;
            border: 1px solid var(--sand);
            border-radius: 12px;
            padding: 1.6rem 1.4rem;
            text-decoration: none;
            color: var(--text);
            transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            box-shadow: 0 2px 8px rgba(59,35,20,0.06);
        }

        .nav-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(59,35,20,0.12);
            border-color: var(--amber);
        }

        .nav-card.wide { grid-column: 1 / -1; flex-direction: row; align-items: center; gap: 1rem; }

        .card-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .icon-amber  { background: rgba(200,121,65,0.12); }
        .icon-rust   { background: rgba(168,76,47,0.10); }
        .icon-gold   { background: rgba(232,164,64,0.12); }
        .icon-brown  { background: rgba(59,35,20,0.08); }

        .card-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--amber);
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .card-desc {
            font-size: 0.8rem;
            color: #7a5c47;
            line-height: 1.5;
        }

        /* footer strip */
        .footer-strip {
            position: relative;
            z-index: 1;
            background: var(--brown);
            color: rgba(253,246,236,0.45);
            font-size: 0.75rem;
            text-align: center;
            padding: 1.1rem;
            letter-spacing: 0.06em;
        }

        /* animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* responsive */
        @media (max-width: 768px) {
            .hero {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto;
            }
            .hero-left {
                clip-path: polygon(0 0, 100% 0, 100% 88%, 50% 100%, 0 88%);
                padding: 4rem 2rem 5rem;
            }
            .hero-right {
                padding: 3rem 1.5rem 2rem;
            }
            .card-grid { max-width: 100%; }
            .nav-card.wide { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="hero">
    <!-- LEFT -->
    <div class="hero-left">
        <div class="badge">Est. Your Community</div>
        <h1 class="store-name">
            Tindahan<br>
            <em>Sari-Sari</em>
        </h1>
        <p class="tagline">Your neighborhood store, now with smarter inventory tracking and order management.</p>
        <div class="cta-row">
            <a href="home.php" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Visit Store
            </a>
            <a href="login.php" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Admin Login
            </a>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="hero-right">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>

        <div class="card-grid">

            <a href="home.php" class="nav-card">
                <div class="card-icon icon-amber">🛒</div>
                <span class="card-label">Public</span>
                <div class="card-title">Browse & Order</div>
                <div class="card-desc">View stock levels and place your order.</div>
            </a>

            <a href="employee_portal.php" class="nav-card">
                <div class="card-icon icon-rust">👤</div>
                <span class="card-label">Staff</span>
                <div class="card-title">Employee Portal</div>
                <div class="card-desc">Track and manage assigned orders.</div>
            </a>

            <a href="login.php" class="nav-card wide">
                <div class="card-icon icon-gold" style="width:48px;height:48px;font-size:1.4rem;">📦</div>
                <div>
                    <span class="card-label">Admin</span>
                    <div class="card-title">Inventory Management</div>
                    <div class="card-desc">Full control over stock, sales records, orders, and employee accounts.</div>
                </div>
            </a>

            <a href="order.php" class="nav-card">
                <div class="card-icon icon-brown">📋</div>
                <span class="card-label">Customers</span>
                <div class="card-title">Place Order</div>
                <div class="card-desc">Order directly from our catalog.</div>
            </a>

            <a href="create_account.php" class="nav-card">
                <div class="card-icon icon-amber">✨</div>
                <span class="card-label">New Admin</span>
                <div class="card-title">Create Account</div>
                <div class="card-desc">Register as a new administrator.</div>
            </a>

        </div>
    </div>
</div>

<div class="footer-strip">
    &copy; <?php echo date('Y'); ?> Sari-Sari Store &nbsp;·&nbsp; All rights reserved
</div>

</body>
</html>
