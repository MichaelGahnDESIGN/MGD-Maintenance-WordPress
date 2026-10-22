const { chromium } = require("playwright");
const fs = require("node:fs");
const assert = require("node:assert/strict");
(async () => {
  const browser = await chromium.launch({ headless: true, channel: "chrome" });
  try {
    const page = await browser.newPage();
    const login = JSON.parse(fs.readFileSync(".local/login.json"));
    await page.goto("http://127.0.0.1:18865/wp-login.php");
    await page.locator("#user_login").fill(login.user);
    await page.locator("#user_pass").fill(login.password);
    await Promise.all([
      page.waitForURL("**/wp-admin/**"),
      page.locator("#wp-submit").click(),
    ]);
    fs.mkdirSync("artifacts/updates", { recursive: true });
    await page.goto(
      "http://127.0.0.1:18865/wp-admin/options-general.php?page=mg-maintenance-studio#panel-9",
    );
    assert.equal(await page.locator("#panel-9").isVisible(), true);
    assert.equal(await page.locator(".editor-layout").isVisible(), false);
    assert.equal(
      await page.locator("#panel-9 input[name=enabled]").isChecked(),
      false,
    );
    for (const [width, height] of [
      [1440, 900],
      [768, 1024],
      [390, 844],
    ]) {
      await page.setViewportSize({ width, height });
      await page.evaluate(() => scrollTo(0, 0));
      await page.evaluate(
        fs.readFileSync(".local/node_modules/axe-core/axe.min.js", "utf8"),
      );
      const violations = await page.evaluate(async () =>
        (
          await axe.run(document, {
            runOnly: {
              type: "tag",
              values: ["wcag2a", "wcag2aa", "wcag21aa", "wcag22aa"],
            },
          })
        ).violations.map((x) => x.id),
      );
      assert.deepEqual(violations, []);
      assert(
        await page.evaluate(
          () => document.documentElement.scrollWidth <= innerWidth,
        ),
      );
      await page.screenshot({
        path: `artifacts/updates/settings-${width}.png`,
        fullPage: true,
      });
      console.log(`PASS Updates: ${width}px, kein Überlauf, axe ohne Befund`);
    }
    const denied = await page.request.post(
      "http://127.0.0.1:18865/wp-admin/admin-post.php",
      {
        form: {
          action: "mgms_updates",
          repository: "MichaelGahnDESIGN/Test",
          enabled: "1",
        },
      },
    );
    assert.equal(denied.status(), 403);
    console.log("PASS Updateänderung ohne Nonce abgewiesen");
    await page.locator("#mgms-repository").fill("MichaelGahnDESIGN/Test");
    await Promise.all([
      page.waitForURL("**#panel-9", { waitUntil: "load" }),
      page.getByRole("button", { name: "Speichern und jetzt prüfen" }).click(),
    ]);
    assert.equal(
      await page.locator("#mgms-repository").inputValue(),
      "MichaelGahnDESIGN/Test",
    );
    await page.locator("#mgms-repository").fill("");
    await page
      .getByRole("button", { name: "Speichern und jetzt prüfen" })
      .click();
    await page.waitForLoadState("load");
    console.log(
      "PASS Updatekonfiguration separat gespeichert, Prüfung bleibt ausgeschaltet",
    );
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto("http://127.0.0.1:18865/wp-admin/plugins.php");
    const icon = await page
      .locator(
        'tr[data-plugin="mg-maintenance-studio/mg-maintenance-studio.php"] .plugin-title strong',
      )
      .evaluate((el) => getComputedStyle(el, "::before").backgroundImage);
    assert(icon.includes("icon.svg"));
    await page.screenshot({
      path: "artifacts/updates/plugin-list.png",
      fullPage: true,
    });
    await page
      .getByRole("link", { name: "Details ansehen", exact: true })
      .click();
    await page
      .frameLocator("#TB_iframeContent")
      .getByText(
        "Wartungsseiten gestalten, geschützt prüfen und bewusst veröffentlichen.",
        { exact: false },
      )
      .waitFor();
    console.log("PASS Pluginlisten-Icon und native Details sichtbar");
  } finally {
    await browser.close();
  }
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
