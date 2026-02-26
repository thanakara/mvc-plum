<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@/active</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #111118;
            --border: #1e1e2e;
            --accent: #7c6aff;
            --accent-dim: rgba(124, 106, 255, 0.12);
            --accent-glow: rgba(124, 106, 255, 0.25);
            --text: #e8e8f0;
            --text-muted: #5a5a7a;
            --text-dim: #8888a8;
            --green: #3dffa0;
            --green-dim: rgba(61, 255, 160, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Mono', monospace;
            min-height: 100vh;
            padding: 48px 32px;
            overflow-x: hidden;
        }

        /* Ambient background */
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            left: 20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(ellipse, rgba(124, 106, 255, 0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Header */
        .header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--border);
        }

        .header-left {
            display: flex;
            flex-direction: column;
        }

        .label {
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 10px;
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 42px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            line-height: 1;
        }

        h1 span {
            color: var(--accent);
        }

        .meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: 7px;
            background: var(--green-dim);
            border: 1px solid rgba(61, 255, 160, 0.2);
            color: var(--green);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            padding: 8px 14px;
            border-radius: 4px;
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--green);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Stats row */
        .stats {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 16px 22px;
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .stat::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--accent-dim) 0%, transparent 60%);
            pointer-events: none;
        }

        .stat-label {
            font-size: 9px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }

        /* Table wrapper */
        .table-wrapper {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .table-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
        }

        .table-title {
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-dim);
        }

        .source-tag {
            font-size: 10px;
            color: var(--text-muted);
            background: var(--accent-dim);
            border: 1px solid rgba(124, 106, 255, 0.2);
            color: rgba(124, 106, 255, 0.7);
            padding: 3px 10px;
            border-radius: 3px;
            letter-spacing: 0.05em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 14px 24px;
            text-align: left;
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            border-bottom: 1px solid rgba(30, 30, 46, 0.6);
            transition: background 0.15s ease;
            animation: fadeIn 0.4s ease both;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: var(--accent-dim);
        }

        tbody tr:hover td:first-child {
            color: var(--accent);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        tbody tr:nth-child(1) {
            animation-delay: 0.05s;
        }

        tbody tr:nth-child(2) {
            animation-delay: 0.10s;
        }

        tbody tr:nth-child(3) {
            animation-delay: 0.15s;
        }

        tbody tr:nth-child(4) {
            animation-delay: 0.20s;
        }

        tbody tr:nth-child(5) {
            animation-delay: 0.25s;
        }

        tbody tr:nth-child(6) {
            animation-delay: 0.30s;
        }

        tbody tr:nth-child(7) {
            animation-delay: 0.35s;
        }

        tbody tr:nth-child(8) {
            animation-delay: 0.40s;
        }

        tbody tr:nth-child(9) {
            animation-delay: 0.45s;
        }

        tbody tr:nth-child(10) {
            animation-delay: 0.50s;
        }

        td {
            padding: 15px 24px;
            font-size: 13px;
            color: var(--text-dim);
            vertical-align: middle;
        }

        td.email {
            color: var(--text);
            font-weight: 400;
        }

        td.account {
            font-weight: 500;
            color: var(--text);
        }

        .region-pill {
            display: inline-flex;
            align-items: center;
            background: var(--accent-dim);
            border: 1px solid rgba(124, 106, 255, 0.2);
            color: rgba(160, 150, 255, 0.9);
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 3px;
        }

        td.date {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Empty state */
        .empty {
            padding: 80px 24px;
            text-align: center;
        }

        .empty-icon {
            font-size: 40px;
            opacity: 0.2;
            margin-bottom: 16px;
        }

        .empty-text {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            color: var(--text-muted);
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.1em;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div class="header-left">
                <div class="label">plum.active_users</div>
                <h1>Active <span>Users</span></h1>
                <div class="meta">Joined accounts · real-time view</div>
            </div>
            <div class="badge">LIVE DATA</div>
        </div>

        <!-- Stats row — populate via PHP -->
        <div class="stats">
            <div class="stat">
                <div class="stat-label">Total Active</div>
                <div class="stat-value"><?= count($this->activeUsers) ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Regions</div>
                <div class="stat-value"><?= count(array_unique(array_column($this->activeUsers, 'region'))) ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Accounts</div>
                <div class="stat-value"><?= count(array_unique(array_column($this->activeUsers, 'account_name'))) ?></div>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-header-bar">
                <span class="table-title">User Records</span>
                <span class="source-tag">view: active_users</span>
            </div>

            <?php if (! $this->activeUsers): ?>
                <div class="empty">
                    <div class="empty-icon">◎</div>
                    <div class="empty-text">No active users found</div>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Account</th>
                            <th>Region</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->activeUsers as $user): ?>
                            <tr>
                                <td class="email"><?= htmlspecialchars($user["email"]) ?></td>
                                <td class="account"><?= htmlspecialchars($user["account_name"]) ?></td>
                                <td><span class="region-pill"><?= htmlspecialchars($user["region"]) ?></span></td>
                                <td class="date"><?= htmlspecialchars($user["created_at"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="footer">
            <?= date('Y-m-d H:i:s') ?> UTC &nbsp;·&nbsp; plum schema
        </div>

    </div>
</body>

</html>