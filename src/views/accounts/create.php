<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@accounts/create</title>
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
            --amber-hover: #a86208;
            --green: #2d7a4f;
            --green-bg: #eaf5ee;
            --green-border: #90cca8;
            --blue: #2255aa;
            --red: #b53a2f;
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
            align-items: flex-start;
            justify-content: center;
            padding: 60px 24px 80px;
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

        /* Top bar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
        }

        .route {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--ink-dim);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .method-badge {
            font-family: var(--mono);
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.12em;
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            color: var(--amber);
            padding: 2px 8px;
            border-radius: 2px;
        }

        .timestamp {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink-dim);
        }

        /* Title */
        .title-block {
            margin-bottom: 28px;
        }

        .eyebrow {
            font-family: var(--mono);
            font-size: 9px;
            font-style: italic;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink-dim);
            margin-bottom: 6px;
        }

        h1 {
            font-family: var(--mono);
            font-size: 28px;
            font-weight: 500;
            color: var(--ink);
            letter-spacing: -0.01em;
        }

        h1 .at {
            color: var(--amber);
        }

        h1 .seg {
            color: var(--ink-mid);
        }

        .subtitle {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 300;
            color: var(--ink-mid);
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-dark);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        .stripe {
            height: 3px;
            background: linear-gradient(90deg, var(--amber) 0%, #e8a040 100%);
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

        .post-badge {
            font-family: var(--mono);
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.12em;
            background: var(--green-bg);
            border: 1px solid var(--green-border);
            color: var(--green);
            padding: 2px 8px;
            border-radius: 2px;
        }

        /* Form */
        .form-body {
            padding: 28px 28px 32px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field:last-of-type {
            margin-bottom: 0;
        }

        label {
            display: block;
            font-family: var(--mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ink-mid);
            margin-bottom: 7px;
        }

        input[type="text"],
        select {
            width: 100%;
            font-family: var(--mono);
            font-size: 13px;
            color: var(--ink);
            background: var(--bg);
            border: 1px solid var(--border-dark);
            border-radius: 4px;
            padding: 10px 13px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            appearance: none;
            -webkit-appearance: none;
        }

        input[type="text"]::placeholder {
            color: var(--ink-dim);
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(201, 124, 16, 0.12);
        }

        /* Custom select arrow */
        .select-wrap {
            position: relative;
        }

        .select-wrap::after {
            content: '▾';
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--ink-dim);
            pointer-events: none;
        }

        select {
            cursor: pointer;
            padding-right: 32px;
        }

        /* Inline row for account + region */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        .field-row .field {
            margin-bottom: 0;
        }

        /* Divider */
        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        /* Submit */
        .submit-row {
            margin-top: 24px;
        }

        button[type="submit"] {
            width: 100%;
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #fff;
            background: var(--ink);
            border: 1px solid var(--ink);
            border-radius: 4px;
            padding: 12px;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, transform 0.1s;
        }

        button[type="submit"]:hover {
            background: var(--amber);
            border-color: var(--amber);
        }

        button[type="submit"]:active {
            transform: scale(0.99);
        }

        /* GET params card (collapsible feel) */
        .params-card {
            margin-bottom: 16px;
        }

        .params-card .json-body {
            padding: 16px 18px;
            overflow-x: auto;
        }

        pre {
            font-family: var(--mono);
            font-size: 12px;
            line-height: 1.75;
            color: var(--ink);
            white-space: pre;
        }

        .json-brace {
            color: var(--ink-mid);
        }

        .json-key {
            color: var(--blue);
        }

        .json-str {
            color: var(--green);
        }

        .json-num {
            color: var(--amber);
        }

        .json-bool {
            color: var(--red);
            font-style: italic;
        }

        .json-null {
            color: var(--ink-dim);
            font-style: italic;
        }

        .empty-inline {
            padding: 14px 18px;
            font-family: var(--mono);
            font-size: 11px;
            color: var(--ink-dim);
            font-style: italic;
        }

        /* Footer */
        .footer {
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

        <div class="topbar">
            <div class="route">
                <span class="method-badge">GET</span>
                /accounts/create
            </div>
            <div class="timestamp" id="ts"></div>
        </div>

        <div class="title-block">
            <div class="eyebrow">route handler</div>
            <h1><span class="at">@</span><span class="seg">accounts/</span>create</h1>
            <div class="subtitle">Create a new account</div>
        </div>

        <!-- GET params inspector -->
        <div class="card params-card">
            <div class="card-header">
                <span class="card-title">$_GET</span>
                <span style="font-family:var(--mono);font-size:10px;color:var(--ink-dim)"><strong style="color:var(--ink)"><?= count($this->fromGet) ?></strong> param(s)</span>
            </div>
            <?php if (! ($this->fromGet)): ?>
                <div class="empty-inline">∅ &nbsp;</div>
            <?php else: ?>
                <div class="json-body">
                    <pre id="get-json"></pre>
                </div>
            <?php endif; ?>
        </div>

        <!-- Create form -->
        <div class="card">
            <div class="stripe"></div>
            <div class="card-header">
                <span class="card-title">New Account</span>
                <span class="post-badge">POST /accounts</span>
            </div>
            <div class="form-body">
                <form action="/accounts" method="post">

                    <div class="field-row">
                        <div class="field">
                            <label for="account_name">account_name</label>
                            <input type="text" id="account_name" name="account_name" placeholder="IGN" required>
                        </div>
                        <div class="field">
                            <label for="region">region</label>
                            <div class="select-wrap">
                                <select id="region" name="region" required>
                                    <option value="">— select —</option>
                                    <option value="EU">EU</option>
                                    <option value="EUW">EUW</option>
                                    <option value="NA">NA</option>
                                    <option value="KR">KR</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="email">email</label>
                        <input type="text" id="email" name="email" placeholder="a@b.com" required>
                    </div>

                    <div class="submit-row">
                        <button type="submit">Submit →</button>
                    </div>

                </form>
            </div>
        </div>

        <div class="footer">
            <span>plum · accounts/create</span>
            <span id="footer-ts"></span>
        </div>

    </div>

    <script>
        const getParams = <?= json_encode($this->fromGet) ?>;

        // Timestamps
        const now = new Date();
        const fmt = d => d.toISOString().replace('T', ' ').slice(0, 19) + ' UTC';
        document.getElementById('ts').textContent = fmt(now);
        document.getElementById('footer-ts').textContent = fmt(now);

        // Syntax-highlighted JSON
        const el = document.getElementById('get-json');
        if (el) {
            const raw = JSON.stringify(getParams, null, 2);
            el.innerHTML = raw
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/("(\\u[a-fA-F0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?|[{}\[\],])/g, match => {
                    if (/^[{}\[\],]$/.test(match)) return `<span class="json-brace">${match}</span>`;
                    if (/^".*":$/.test(match)) return `<span class="json-key">${match}</span>`;
                    if (/^"/.test(match)) return `<span class="json-str">${match}</span>`;
                    if (/true|false/.test(match)) return `<span class="json-bool">${match}</span>`;
                    if (/null/.test(match)) return `<span class="json-null">${match}</span>`;
                    return `<span class="json-num">${match}</span>`;
                });
        }

        console.log('%c@__acc/create GET params', 'font-weight:bold;color:#c97c10');
        console.log(JSON.stringify(getParams, null, 2));
    </script>
</body>

</html>