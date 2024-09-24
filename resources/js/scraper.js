import puppeteer from "puppeteer";

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

    try {
        console.log("Navigating to " + url);
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));

        await page.goto(url, { waitUntil: 'networkidle2' });

        // Set screen size.
        await page.setViewport({ width: 1080, height: 1024 });

        const pageTitle = await page.title();
        data.logs.push("Page title: " + pageTitle);
        data.product.title = pageTitle;

        const productTitle = await page.evaluate(() => {
            return document.querySelector('h1.product-title').innerText;
        });
        data.product.title = productTitle;

        await page.evaluate(() => {
            debugger;
        });

        // Additional scraping logic can be added here

    } catch (error) {
        data.logs.push("Error: " + error.stack);
    } finally {
        await browser.close();
    }

    return data;
};

export { getProduct };
