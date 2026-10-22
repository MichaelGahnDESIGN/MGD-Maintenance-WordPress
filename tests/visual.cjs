const { chromium } = require("playwright");
const fs = require("node:fs");
const assert = require("node:assert/strict");
const { execFileSync } = require("node:child_process");
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
  const browser = await chromium.launch({ headless: true, channel: "chrome" });
  try {
    const page = await browser.newPage();
    const axe = fs.readFileSync(
      ".local/node_modules/axe-core/axe.min.js",
      "utf8",
    );
    fs.mkdirSync("artifacts/redesign", { recursive: true });
    for (const name of [
      "elegant-gold",
      "ruhiges-atelier",
      "minimalistisch-hell",
    ]) {
      fixture(name);
      for (const [width, height] of [
        [1440, 900],
        [768, 1024],
        [390, 844],
      ]) {
        await page.setViewportSize({ width, height });
        await page.goto("http://127.0.0.1:18865");
        await page.screenshot({
          path: `artifacts/redesign/${name}-${width}.png`,
          fullPage: true,
        });
        await page.evaluate(axe);
        const violations = await page.evaluate(
          async () =>
            (
              await axe.run(document, {
                runOnly: {
                  type: "tag",
                  values: ["wcag2a", "wcag2aa", "wcag21aa", "wcag22aa"],
                },
              })
            ).violations,
        );
        assert.equal(violations.length, 0);
        assert.equal(
          await page.evaluate(
            () => document.documentElement.scrollWidth <= innerWidth,
          ),
          true,
        );
        console.log(`PASS ${name} ${width}px: axe und Überlauf`);
      }
    }
    const login = JSON.parse(fs.readFileSync(".local/login.json"));
    await page.goto("http://127.0.0.1:18865/wp-login.php");
    await page.locator("#user_login").fill(login.user);
    await page.locator("#user_pass").fill(login.password);
    await page.locator("#wp-submit").click();
    await page.waitForURL("**/wp-admin/");
    await page.setViewportSize({ width: 1440, height: 1000 });
    await page.goto(
      "http://127.0.0.1:18865/wp-admin/options-general.php?page=mg-maintenance-studio",
    );
    await page.evaluate(axe);
    let violations = await page.evaluate(
      async () =>
        (
          await axe.run(".mgms-admin", {
            runOnly: {
              type: "tag",
              values: ["wcag2a", "wcag2aa", "wcag21aa", "wcag22aa"],
            },
          })
        ).violations,
    );
    fs.writeFileSync(
      ".local/axe-admin.json",
      JSON.stringify(violations, null, 2),
    );
    assert.equal(violations.length, 0);
    console.log("PASS Admin-Oberfläche: axe WCAG 2.1 AA");
    await page.screenshot({ path: "artifacts/redesign/admin-desktop.png" });
    await page.getByRole("tab", { name: "Übersicht", exact: true }).focus();
    await page.keyboard.press("ArrowRight");
    assert.equal(
      await page
        .getByRole("tab", { name: "Wartungsmodus", exact: true })
        .getAttribute("aria-selected"),
      "true",
    );
    for (const width of [1440, 768, 390]) {
      await page.setViewportSize({ width, height: 900 });
      await page.getByRole("tab", { name: "Design", exact: true }).click();
      await page.screenshot({
        path: `artifacts/redesign/admin-design-${width}.png`,
        fullPage: true,
      });
      assert.equal(
        await page
          .locator(".mgms-admin")
          .evaluate((el) => el.scrollWidth <= el.clientWidth + 1),
        true,
      );
    }
    console.log("PASS Admin: Tastatur-Tabs und drei Bildschirmbreiten");
  } finally {
    fixture("restore");
    await browser.close();
  }
})().catch((e) => {
  console.error(e.message);
  process.exit(1);
});
