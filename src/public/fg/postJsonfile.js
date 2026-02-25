// #!/usr/bin/env node

// import body from "../../../scripts/requests/template.json" with { type: "json" }
import path from "path";
import fs from "fs/promises";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const __workdir = path.dirname(path.dirname(path.dirname(__dirname)));
const jsonTemplatePath = path.join(__workdir, "scripts", "requests", "template.json");

let body;
try {
    body = await fs.readFile(jsonTemplatePath, "utf-8")
} catch (error) {
    if (error.code === "ENOENT") console.log("FileNotFound");
}

async function postAccount() {
    // containerURL = "http://nginx/accounts";
    const base = "http://localhost:8000/accounts";

    try {
        const response = await fetch(base, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body,
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
