// src/resources/js/index.mjs
import AliexpressProductScraper from "./scraper.js"
function f(arg) {
    const result = {
        message: `Hello, ${arg}!`,
        status: 'success'
    };
    return result;
}



if (import.meta.url === `file://${process.argv[1]}`) {
    const arg = process.argv[2]; // Get the first argument passed to the script
    const result = f(arg);
    console.log(JSON.stringify(result))
}