<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@index</title>
    <link rel="stylesheet" href="/fg/css/base.css">
</head>

<body>
    <div class="page">

        <div class="topbar">
            <div class="route">
                <span class="method-badge">GET</span>
                /
            </div>
            <div class="timestamp" id="ts"></div>
        </div>

        <div class="title-block">
            <div class="eyebrow">route handler</div>
            <h1><span class="at">@</span>index</h1>
        </div>

        <div class="curl-hint">
            <span class="prompt">$</span>
            <code>curl -X GET "http://nginx/"</code>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">$_GET</span>
                <span class="param-count"><strong id="count">0</strong> param(s)</span>
            </div>

            <?php if (!$this->fromGet): ?>
                <div class="empty-state">
                    <div class="empty-icon">∅</div>
                    <div class="empty-text">No GET parameters</div>
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

    <script src="/fg/js/utils.js"></script>
    <script>
        const getParams = <?= json_encode($this->fromGet) ?>;

        renderTimestamps();
        document.getElementById('count').textContent = Object.keys(getParams).length;
        renderJson(getParams, 'json-output');

        console.log('%c@index GET params', 'font-weight:bold;color:#c97c10');
        console.log(JSON.stringify(getParams, null, 2));
    </script>
</body>

</html>