<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@/active</title>
    <link rel="stylesheet" href="/fg/css/active.css">
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

            <?php if (!$this->activeUsers): ?>
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
                                <td class="email"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="account"><?= htmlspecialchars($user['account_name']) ?></td>
                                <td><span class="region-pill"><?= htmlspecialchars($user['region']) ?></span></td>
                                <td class="date"><?= htmlspecialchars($user['created_at']) ?></td>
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