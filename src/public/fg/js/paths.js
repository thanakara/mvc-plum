import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const root = path.dirname(path.dirname(path.dirname(path.dirname(__dirname))));


export const paths = {
    templateJson: path.join(root, "scripts", "requests", "template.json")
    // ..
}
