const body = require("../../../scripts/requests/template.json");

async function postAccount() {
    // containerURL = "http://nginx/accounts";
    const base = "http://localhost:8000/accounts";

    try {
        const response = await fetch(base, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(body),
        });

        if (!response.ok) {
            throw new Error(`__status__: <${response.status}>`);
        }

        const data = await response.json();
        console.log(JSON.stringify(data, null, 2));

    } catch (error) {
        console.log(error);
    }
}

postAccount();
