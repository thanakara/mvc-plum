<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@accounts/index</title>
    <link rel="stylesheet" href="/fg/css/base.css">
    <link rel="stylesheet" href="/fg/css/accounts.css">
</head>

<body>
    <?php
    $method = $_SERVER['REQUEST_METHOD'];
    $params = ($method === 'GET') ? $this->fromGet : $this->fromPost;
    $isEmpty = !($params);
    $isPost  = $method === 'POST';
    ?>
    <script>
        document.body.classList.add('method-<?= strtolower($method) ?>');
    </script>

    <div class="page">

        <div class="topbar">
            <div class="route">
                <span class="method-badge"><?= $method ?></span>
                /accounts
            </div>
            <div class="timestamp" id="ts"></div>
        </div>

        <div class="title-block">
            <div class="eyebrow">route handler</div>
            <h1><span class="at">@</span><span class="seg">accounts/</span>index</h1>
            <div class="subtitle">Incoming <?= $method ?> parameters</div>
        </div>

        <div class="curl-hint">
            <span class="prompt">$</span>
            <?php if ($isPost): ?>
                <code>curl <span class="flag">-X POST</span> <span class="url">"http://nginx/accounts"</span></code>
            <?php else: ?>
                <code>curl <span class="flag">-X GET</span> <span class="url">"http://nginx/accounts"</span></code>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="stripe"></div>
            <div class="card-header">
                <span class="card-title"><?= $isPost ? '$_POST' : '$_GET' ?> params</span>
                <span class="param-count"><strong><?= count($params) ?></strong> param(s)</span>
            </div>

            <?php if ($isEmpty): ?>
                <div class="empty-state">
                    <div class="empty-icon">∅</div>
                    <div class="empty-text">No <?= $method ?> parameters</div>
                </div>
            <?php else: ?>
                <div class="json-body">
                    <pre id="json-output"></pre>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <span>plum · accounts/index</span>
            <span id="footer-ts"></span>
        </div>

    </div>

    <script src="/fg/js/utils.js"></script>
    <script>
        const params = <?= json_encode($params) ?>;

        renderTimestamps();
        renderJson(params, 'json-output');

        console.log(`%c@accounts/index — ${<?= json_encode($method) ?>} params`, 'font-weight:bold;color:#c97c10');
        console.log(JSON.stringify(params, null, 2));
    </script>
</body>

</html>