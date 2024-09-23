// src/resources/js/index.mjs
import { getProduct } from "./scraper.js"
function f(arg) {
    return getProduct(arg);
}



if (import.meta.url === `file://${process.argv[1]}`) {
    const arg = process.argv[2]; // Get the first argument passed to the script
    const result = f(arg);
    console.log(JSON.stringify(result))
}