// src/resources/js/index.mjs
// import { getProduct } from "./scraper.js";
import puppeteer from "puppeteer";

if (import.meta.url === `file://${process.argv[1]}`) {
    const arg = process.argv[2]; // Get the first argument passed to the script
    getProduct(arg).then(result => {
        console.log(JSON.stringify(result, null, 2));
    }).catch(error => {
        console.error("Error:", error);
    });
}


const getProduct = (productId) => async () => {
    const browser = await puppeteer.launch({
        headless: false,
        devtools: true,
        slowMo: 250, // slow down by 250ms
    });

    const page = await browser.newPage();
    const url = `https://www.aliexpress.com/item/${productId}.html`;
    const data = {
        logs: [],
        product: {},
    };
    await page.goto(url);

    // try {
    console.log("Navigating to " + url);
    data.logs.push("Navigating to " + url);
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));

    await page.evaluate(() => console.log(`url is ${location.href}`));

    // Set screen size.
    await page.setViewport({ width: 1080, height: 1024 });

    const pageTitle = await page.title();
    data.logs.push("Page title: " + pageTitle);
    data.product.title = pageTitle;

    // Wait for the necessary DOM to be rendered
    await page.waitForSelector('.product-price-current');
    await page.waitForSelector('.product-description');

    // Extract the product price
    const price = await page.$eval('.product-price-current', el => el.innerText);
    data.product.price = price;

    // Extract the product description
    const description = await page.$eval('.product-description', el => el.innerText);
    data.product.description = description;

    data.logs.push("Product price: " + price);
    data.logs.push("Product description: " + description);

    // } catch (error) {
    //     data.logs.push("Error: " + error.message);
    // } finally {
    //     await browser.close();
    // }

    return data;
}


