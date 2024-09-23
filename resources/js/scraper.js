import puppeteer from "puppeteer";

const getProduct = (productId) => async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    const url = `https://www.aliexpress.com/item/${productId}.html`;
    const data = {
        logs: [],
        product: {},
    };

    try {
        data.logs.push("Navigating to " + url);
        await page.goto(url);

        // Set screen size.
        await page.setViewport({ width: 1080, height: 1024 });

        const pageTitle = await page.title();
        data.logs.push("Page title: " + pageTitle);
        data.product.title = pageTitle;

        // Additional scraping logic can be added here

    } catch (error) {
        data.logs.push("Error: " + error.message);
    } finally {
        await browser.close();
    }

    return data;
};

export { getProduct };
