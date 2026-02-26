<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400&family=IBM+Plex+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f2eb;
            --bg-card: #fdfaf4;
            --border: #ddd9ce;
            --border-dark: #c8c3b5;
            --ink: #1a1914;
            --ink-mid: #5a5649;
            --ink-dim: #9e9889;
            --amber: #c97c10;
            --amber-bg: #fef3e0;
            --amber-border: #f0c060;
            --red: #b53a2f;
            --red-bg: #fdf0ee;
            --red-border: #e8b0a8;
            --mono: 'IBM Plex Mono', monospace;
            --sans: 'IBM Plex Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: var(--sans);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.35;
            pointer-events: none;
        }

        .page {
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 1;
            animation: rise 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-dark);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Red top stripe */
        .stripe {
            height: 3px;
            background: linear-gradient(90deg, var(--red) 0%, #d4705f 100%);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ink-mid);
        }

        .status-badge {
            font-family: var(--mono);
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.12em;
            background: var(--red-bg);
            border: 1px solid var(--red-border);
            color: var(--red);
            padding: 2px 8px;
            border-radius: 2px;
        }

        .card-body {
            padding: 40px 36px 36px;
            text-align: center;
        }

        .code {
            font-family: var(--mono);
            font-size: 72px;
            font-weight: 300;
            color: var(--border-dark);
            line-height: 1;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
            /* Stamp-like effect */
            position: relative;
            display: inline-block;
        }

        .code::after {
            content: 'NOT FOUND';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.25em;
            color: var(--red);
            border: 2px solid var(--red);
            border-radius: 4px;
            opacity: 0.18;
            transform: rotate(-8deg);
            pointer-events: none;
        }

        .message {
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 400;
            color: var(--ink);
            margin-top: 16px;
            margin-bottom: 6px;
        }

        .path {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink-dim);
            font-style: italic;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0 22px;
        }

        .hint {
            font-size: 12px;
            font-weight: 300;
            color: var(--ink-mid);
            line-height: 1.6;
        }

        .hint a {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--amber);
            text-decoration: none;
            border-bottom: 1px solid var(--amber-border);
            padding-bottom: 1px;
            transition: border-color 0.15s;
        }

        .hint a:hover {
            border-color: var(--amber);
        }

        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink-dim);
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="card">
            <div class="stripe"></div>
            <div class="card-header">
                <span class="card-title">HTTP Response</span>
                <span class="status-badge">404</span>
            </div>
            <div class="card-body">
                <div class="code">404</div>
                <div class="message">Page Not Found</div>
                <div class="path">/mvc-plum</div>
                <hr class="divider">
                <div class="hint">
                    The route you requested doesn't exist.<br>
                    Head back to <a href="/">@index</a> or check your URL.
                </div>
            </div>
        </div>

        <div class="footer">
            <span>plum · error</span>
            <span id="ts"></span>
        </div>
    </div>

    <script>
        const now = new Date();
        document.getElementById('ts').textContent =
            now.toISOString().replace('T', ' ').slice(0, 19) + ' UTC';
    </script>
</body>

</html>