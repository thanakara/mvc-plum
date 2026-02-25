// #!/usr/bin/env node

import fs from "fs/promises";
import { paths } from "./paths.js";

let body;
try {
    body = await fs.readFile(paths.templateJson, "utf-8")
    // fetch API accepts string body [optional: JSON.parse()]
} catch (error) {
    if (error.code === "ENOENT") {
        console.log("FileNotFound");
        // return;
        process.exit(1);
    };
}

async function makePostRequest() {
    // node --env-file=.env
    const postUrl = process.env.POST_URL ?? "http://localhost:8000/accounts";

    try {
        const response = await fetch(postUrl, {
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

makePostRequest();