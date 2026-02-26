/* ─── utils.js — shared utilities for parchment views ───────────────────── */

/**
 * Renders the current UTC timestamp into all elements matching the selector.
 * Default selectors: #ts, #footer-ts
 */
function renderTimestamps(selectors = ['#ts', '#footer-ts']) {
    const now = new Date();
    const formatted = now.toISOString().replace('T', ' ').slice(0, 19) + ' UTC';
    selectors.forEach(sel => {
        const el = document.querySelector(sel);
        if (el) el.textContent = formatted;
    });
}

/**
 * Syntax-highlights a JSON value and writes it into a <pre> element.
 * @param {object|array} data  - the JS value to display
 * @param {string}       preId - id of the target <pre> element
 */
function renderJson(data, preId = 'json-output') {
    const el = document.getElementById(preId);
    if (!el) return;

    const raw = JSON.stringify(data, null, 2);
    el.innerHTML = raw
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(
            /("(\\u[a-fA-F0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?|[{}\[\],])/g,
            match => {
                if (/^[{}\[\],]$/.test(match)) return `<span class="json-brace">${match}</span>`;
                if (/^".*":$/.test(match)) return `<span class="json-key">${match}</span>`;
                if (/^"/.test(match)) return `<span class="json-str">${match}</span>`;
                if (/true|false/.test(match)) return `<span class="json-bool">${match}</span>`;
                if (/null/.test(match)) return `<span class="json-null">${match}</span>`;
                return `<span class="json-num">${match}</span>`;
            }
        );
}
