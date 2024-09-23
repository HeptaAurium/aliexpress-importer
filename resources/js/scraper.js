// source: https://github.com/sudheer-ranga/aliexpress-product-scraper

import puppeteer from "puppeteer";
import * as cheerio from "cheerio";

import { get as GetVariants } from "./utils/variants.js";
import { get as GetReviews } from "./utils/reviews.js";
import { get as GetShippingDetails } from "./utils/shipping.js";

const getProduct = (productId) => async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    const url = `https://www.aliexpress.com/item/${productId}.html`;
    data.logs.push("Navigating to " + url);
    const data = {
        logs: [],
        product: {},
    };
    // Navigate the page to a URL.
    await page.goto(url);

    // Set screen size.
    await page.setViewport({ width: 1080, height: 1024 });

    const pageTitle = await page.title();
    data.logs.push("Page title: " + pageTitle);
    data.product.title = pageTitle;
    return data;
    await browser.close();
};

export { getProduct };