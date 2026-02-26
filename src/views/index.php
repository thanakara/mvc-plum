<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@index</title>
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
            --green: #2d7a4f;
            --green-bg: #eaf5ee;
            --red: #b53a2f;
            --blue: #2255aa;
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

        /* Subtle grid texture */
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
            z-index: 0;
        }

        .page {
            width: 100%;
            max-width: 680px;
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
            letter-spacing: 0.05em;
            color: var(--ink-dim);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .route .method {
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            color: var(--amber);
            font-weight: 500;
            font-size: 9px;
            letter-spacing: 0.12em;
            padding: 2px 7px;
            border-radius: 2px;
        }

        .timestamp {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink-dim);
        }

        /* Title block */
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

        .subtitle {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 300;
            color: var(--ink-mid);
        }

        /* curl hint */
        .curl-hint {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--ink);
            border-radius: 5px;
            padding: 11px 16px;
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .curl-hint .prompt {
            color: var(--amber);
            font-family: var(--mono);
            font-size: 11px;
            white-space: nowrap;
            user-select: none;
        }

        .curl-hint code {
            font-family: var(--mono);
            font-size: 11px;
            color: #c8ffc8;
            white-space: nowrap;
        }

        /* JSON card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-dark);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
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

        .param-count {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink-dim);
        }

        .param-count strong {
            color: var(--ink);
            font-weight: 500;
        }

        /* JSON content */
        .json-body {
            padding: 20px 22px;
            overflow-x: auto;
        }

        pre {
            font-family: var(--mono);
            font-size: 13px;
            line-height: 1.75;
            color: var(--ink);
            white-space: pre;
        }

        /* Syntax colouring applied via JS */
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
            color: #b53a2f;
            font-style: italic;
        }

        .json-null {
            color: var(--ink-dim);
            font-style: italic;
        }

        /* Empty state */
        .empty-state {
            padding: 36px 22px;
            text-align: center;
        }

        .empty-icon {
            font-size: 28px;
            margin-bottom: 10px;
            opacity: 0.3;
        }

        .empty-text {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink-dim);
        }

        .empty-hint {
            margin-top: 6px;
            font-size: 11px;
            color: var(--ink-dim);
        }

        .empty-hint code {
            font-family: var(--mono);
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 1px 5px;
            border-radius: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
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
                <span class="method">GET</span>
                /
            </div>
            <div class="timestamp" id="ts"></div>
        </div>

        <div class="title-block">
            <div class="eyebrow">route handler</div>
            <h1><span class="at">@</span>index</h1>
            <!-- <div class="subtitle">Incoming GET parameters</div> -->
        </div>

        <div class="curl-hint">
            <span class="prompt">$</span>
            <code>curl -X GET "http://nginx/?<?= htmlspecialchars(http_build_query($this->fromGet ?: ['key' => 'value'])) ?>"</code>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">$_GET params</span>
                <span class="param-count">
                    <strong id="count">0</strong> param(s)
                </span>
            </div>

            <?php if (! ($this->fromGet)): ?>
                <div class="empty-state">
                    <div class="empty-icon">∅</div>
                    <div class="empty-text">No GET parameters</div>
                    <!-- <div class="empty-hint">Try <code>/?foo=bar&baz=1</code></div> -->
                </div>
            <?php else: ?>
                <div class="json-body">
                    <pre id="json-output"></pre>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <span>plum · index</span>
            <span id="footer-ts"></span>
        </div>

    </div>

    <script>
        const getParams = <?= json_encode($this->fromGet) ?>;

        // Timestamp
        const now = new Date();
        const fmt = d => d.toISOString().replace('T', ' ').slice(0, 19) + ' UTC';
        document.getElementById('ts').textContent = fmt(now);
        document.getElementById('footer-ts').textContent = fmt(now);

        // Param count
        const keys = Object.keys(getParams);
        document.getElementById('count').textContent = keys.length;

        // Syntax-highlighted JSON
        const el = document.getElementById('json-output');
        if (el) {
            const raw = JSON.stringify(getParams, null, 2);
            el.innerHTML = raw
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/("(\\u[a-fA-F0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?|[{}\[\],])/g, (match) => {
                    if (/^[{}\[\],]$/.test(match)) return `<span class="json-brace">${match}</span>`;
                    if (/^".*":$/.test(match)) return `<span class="json-key">${match}</span>`;
                    if (/^"/.test(match)) return `<span class="json-str">${match}</span>`;
                    if (/true|false/.test(match)) return `<span class="json-bool">${match}</span>`;
                    if (/null/.test(match)) return `<span class="json-null">${match}</span>`;
                    return `<span class="json-num">${match}</span>`;
                });
        }

        console.log('%c@__index GET params', 'font-weight:bold;color:#c97c10');
        console.log(JSON.stringify(getParams, null, 2));
    </script>
</body>

</html>