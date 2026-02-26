
const URL = process.env.HEALTH_CHECK_URL ?? "http://localhost:8000/health"
const INTERVAL_MS = 30_000;

async function check() {
    const timestamp = new Date().toISOString();
    try {
        const response = await fetch(URL);
        const data = await response.json();

        if (response.ok) {
            console.log(`[${timestamp}] ${data.status} — database: ${data.checks.database.status}`);
        } else {
            console.error(`[${timestamp}] ${data.status} — database: ${data.checks.database.status}`);
            if (data.checks.database.message) {
                console.error(`message: ${data.checks.database.message}`);
            }
        }
    } catch (err) {
        console.error(`[${timestamp}] unreachable — ${err.message}`);
    }
}

check();
setInterval(check, INTERVAL_MS);
