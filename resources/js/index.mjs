// src/resources/js/index.mjs
import { getProduct } from "./scraper.js";

async function f(arg) {
    return await getProduct(arg)();
}

if (import.meta.url === `file://${process.argv[1]}`) {
    const arg = process.argv[2]; // Get the first argument passed to the script
    f(arg).then(result => {
        console.log(JSON.stringify(result));
    }).catch(error => {
        console.error("Error:", error);
    });
}
