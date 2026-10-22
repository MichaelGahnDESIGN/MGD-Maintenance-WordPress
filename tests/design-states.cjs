/* Lange Texte und optionale Inhalte ausschließlich in der lokalen Browseransicht prüfen. */
const { chromium } = require("playwright");
const { execFileSync } = require("node:child_process");
const assert = require("node:assert/strict");
const fs = require("node:fs");
const fixture = (action) =>
  execFileSync(
    "docker",
    [
      "exec",
      "-e",
      "MGMS_TEST=isolated-local",
      "mgms-local-wordpress-1",
      "php",
      "/tmp/mgms-visual.php",
      action,
    ],
    { stdio: "pipe" },
  );
(async () => {
  fixture("save");
  fixture("elegant-gold");
  const browser = await chromium.launch({ headless: true, channel: "chrome" });
  try {
    const page = await browser.newPage();
    const errors = [];
    const outside = [];
    page.on("pageerror", (error) => errors.push(error.message));
    page.on("request", (request) => {
      if (new URL(request.url()).hostname !== "127.0.0.1")
        outside.push(new URL(request.url()).hostname);
    });
    for (const [width, height] of [
      [1440, 900],
      [768, 1024],
      [390, 844],
    ]) {
      await page.setViewportSize({ width, height });
      await page.goto("http://127.0.0.1:18865");
      await page
        .locator("h1")
        .evaluate(
          (el) =>
            (el.textContent =
              "Unsere Website wird überarbeitet. Wir sind in Kürze wieder für Sie erreichbar."),
        );
      await page
        .locator(".description")
        .evaluate(
          (el) =>
            (el.textContent =
              "Bis dahin erreichen Sie uns über die angegebenen Kontaktmöglichkeiten. Dieser längere Beispieltext prüft Lesbarkeit und Zeilenumbrüche auf kleinen Bildschirmen."),
        );
      assert.equal(
        await page.evaluate(
          () => document.documentElement.scrollWidth <= innerWidth,
        ),
        true,
      );
      await page.screenshot({
        path: `artifacts/redesign/long-text-${width}.png`,
        fullPage: true,
      });
      await page
        .locator("[data-live=notice]")
        .evaluate((el) => (el.textContent = ""));
      assert.equal(await page.locator(".message-details").isVisible(), false);
      console.log(
        `PASS ${width}: lange Texte und vollständig leerer Zusatzbereich`,
      );
    }
    assert.equal(errors.length, 0);
    assert.equal(outside.length, 0);
    console.log("PASS Keine Laufzeitfehler oder externen Ressourcen");
  } finally {
    fixture("restore");
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
