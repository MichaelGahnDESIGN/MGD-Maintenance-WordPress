/* Ausschließlich gegen die isolierte Testinstallation auf Loopback. Keine Zugangsdaten ausgeben. */
const { chromium } = require("playwright");
const fs = require("node:fs");
const assert = require("node:assert/strict");
const base = process.env.MGMS_TEST_URL || "http://127.0.0.1:18865";
if (!/^http:\/\/127\.0\.0\.1:1886[56]$/.test(base))
  throw new Error("Nur lokale Testadressen erlaubt");
const login = JSON.parse(
  fs.readFileSync(process.env.MGMS_LOGIN_FILE || ".local/login.json", "utf8"),
);
const axe = fs.readFileSync(".local/node_modules/axe-core/axe.min.js", "utf8");
const results = [];
const ok = (label) => {
  results.push(label);
  console.log(`PASS ${label}`);
};
(async () => {
  const browser = await chromium.launch({ headless: true, channel: "chrome" });
  const anon = await browser.newContext();
  const page = await anon.newPage();
  const external = [],
    errors = [];
  page.on("request", (request) => {
    if (!request.url().startsWith(base))
      external.push(new URL(request.url()).origin);
  });
  page.on("pageerror", (error) => errors.push(error.message));
  let response = await page.goto(base);
  assert.equal(response.status(), 503);
  assert.match(
    response.headers()["x-robots-tag"],
    /noindex, nofollow, noarchive/,
  );
  assert.equal(response.headers()["retry-after"], "3600");
  assert.match(response.headers()["cache-control"], /no-store/);
  ok("Anonym: 503, Retry-After, Robots, kein Cache");
  assert.equal(await page.locator(".mgms-badge").count(), 0);
  ok("Kein Wartungsbadge für anonyme Besucher");
  for (const width of [1440, 768, 390]) {
    await page.setViewportSize({ width, height: 1000 });
    assert.equal(
      await page.evaluate(
        () => document.documentElement.scrollWidth <= innerWidth,
      ),
      true,
    );
    await page.screenshot({
      path: `.local/maintenance-${width}.png`,
      fullPage: true,
    });
  }
  ok("Desktop, Tablet, Smartphone ohne horizontalen Überlauf");
  await page.evaluate(axe);
  let audit = await page.evaluate(
    async () =>
      await axe.run(document, {
        runOnly: { type: "tag", values: ["wcag2a", "wcag2aa", "wcag21aa"] },
      }),
  );
  fs.writeFileSync(
    ".local/axe-frontend.json",
    JSON.stringify(audit.violations, null, 2),
  );
  assert.equal(audit.violations.length, 0);
  ok("axe WCAG 2.1 AA: keine gefundenen Verstöße");
  await page.getByRole("link", { name: "Test-Impressum", exact: true }).click();
  await page.locator("dialog[open]").waitFor();
  assert.equal(
    await page
      .getByRole("button", { name: "Schließen" })
      .evaluate((el) => el === document.activeElement),
    true,
  );
  await page.keyboard.press("Shift+Tab");
  assert.equal(
    await page
      .locator("dialog[open]")
      .evaluate((el) => el.contains(document.activeElement)),
    true,
  );
  await page.keyboard.press("Escape");
  assert.equal(await page.locator("dialog[open]").count(), 0);
  assert.equal(
    await page
      .getByRole("link", { name: "Test-Impressum", exact: true })
      .evaluate((el) => el === document.activeElement),
    true,
  );
  ok("Dialog: Fokus, Tastaturfalle, Escape und Fokusrückgabe");
  await page.emulateMedia({ reducedMotion: "reduce" });
  assert.equal(
    await page
      .locator(".content")
      .evaluate((el) => getComputedStyle(el).animationName),
    "none",
  );
  ok("Reduzierte Bewegung berücksichtigt");
  assert.equal(external.length, 0);
  assert.equal(errors.length, 0);
  ok("Keine externen Ressourcen und keine JavaScript-Laufzeitfehler");
  const nojs = await browser.newContext({ javaScriptEnabled: false });
  const plain = await nojs.newPage();
  await plain.goto(base);
  await plain
    .getByRole("link", { name: "Test-Impressum", exact: true })
    .click();
  assert.equal(await plain.locator("#legal-0").isVisible(), true);
  ok("Rechtstexte ohne JavaScript erreichbar");
  response = await anon.request.get(`${base}/?rest_route=/wp/v2/posts`);
  assert.equal(response.status(), 503);
  ok("Anonyme REST-Ausgabe während Wartung gesperrt");
  response = await anon.request.post(`${base}/wp-admin/admin-post.php`, {
    form: { action: "mgms_save" },
  });
  assert.equal(response.status(), 400);
  ok("Anonymes Schreiben ohne registrierten Endpunkt abgewiesen");
  const admin = await browser.newContext();
  const editor = await admin.newPage();
  await editor.goto(`${base}/wp-login.php`);
  await editor.locator("#user_login").fill(login.user);
  await editor.locator("#user_pass").fill(login.password);
  await editor.locator("#wp-submit").click();
  await editor.waitForURL("**/wp-admin/");
  response = await editor.goto(base);
  assert.equal(response.status(), 200);
  assert.equal(await editor.locator(".mgms-badge").count(), 1);
  ok("Administrator sieht normale Website mit Badge");
  await editor.goto(
    `${base}/wp-admin/options-general.php?page=mg-maintenance-studio`,
  );
  await editor.getByRole("tab", { name: "Wartungsmodus", exact: true }).click();
  await editor
    .getByRole("button", { name: "Geschützte Vorschau öffnen", exact: true })
    .click();
  const frame = editor.frameLocator("#mgms-preview");
  await frame.locator(".preview-label").waitFor();
  await editor.locator("#mgms-title").fill("Ein neuer Raum für Ideen.");
  await frame
    .getByRole("heading", { name: "Ein neuer Raum für Ideen." })
    .waitFor();
  ok("Geschützte Vorschau und Live-Textänderung");
  await editor.getByRole("tab", { name: "Design", exact: true }).click();
  await editor
    .getByRole("button", { name: "Minimalistisch Hell", exact: true })
    .click();
  assert.equal(
    await editor.locator("#mgms-background").inputValue(),
    "#ffffff",
  );
  await editor
    .getByRole("button", { name: "Letzte Vorlage rückgängig" })
    .click();
  assert.equal(
    await editor.locator("#mgms-background").inputValue(),
    "#f7f5ef",
  );
  ok("Preset anwenden und eigene Designwerte zurückholen");
  await editor.screenshot({ path: ".local/admin-editor.png", fullPage: true });
  const previewForm = await editor
    .locator("#preview-form")
    .evaluate((el) => Object.fromEntries(new FormData(el)));
  response = await admin.request.post(`${base}/wp-admin/admin-post.php`, {
    form: previewForm,
  });
  assert.equal(response.status(), 403);
  ok("Vorschau-Token nur einmal gültig");
  response = await admin.request.post(`${base}/wp-admin/admin-post.php`, {
    form: { action: "mgms_state", intent: "disable", confirm_state: "1" },
  });
  assert.equal(response.status(), 403);
  ok("Schreiben ohne Nonce abgewiesen");
  await editor
    .getByRole("button", { name: "Entwurf speichern", exact: true })
    .click();
  await editor.waitForURL("**mgms_notice=saved");
  response = await page.goto(base);
  assert.equal(
    await page
      .getByRole("heading", { name: "Wir sind bald wieder da." })
      .count(),
    1,
  );
  ok("Entwurf speichern verändert öffentliche Ausgabe nicht");
  await editor.getByRole("tab", { name: "Wartungsmodus", exact: true }).click();
  await editor.locator("[name=confirm_publish]").check();
  await editor
    .getByRole("button", { name: "Einstellungen veröffentlichen", exact: true })
    .click();
  await editor.waitForURL("**mgms_notice=published");
  await page.goto(base);
  assert.equal(
    await page
      .getByRole("heading", { name: "Ein neuer Raum für Ideen." })
      .count(),
    1,
  );
  ok("Bestätigte Veröffentlichung übernimmt Entwurf");
  const recovery = editor
    .getByRole("button", {
      name: "Vorherige Veröffentlichung wiederherstellen",
    })
    .locator("..")
    .locator("..");
  await recovery.locator("[name=confirm_state]").check();
  await editor
    .getByRole("button", {
      name: "Vorherige Veröffentlichung wiederherstellen",
    })
    .click();
  await editor.waitForURL("**mgms_notice=changed");
  await page.goto(base);
  assert.equal(
    await page
      .getByRole("heading", { name: "Wir sind bald wieder da." })
      .count(),
    1,
  );
  ok("Veröffentlichungs-Rollback über Admin-UI");
  const state = editor
    .getByRole("button", { name: "Wartungsmodus ausdrücklich deaktivieren" })
    .locator("..")
    .locator("..");
  await state.locator("[name=confirm_state]").check();
  await editor
    .getByRole("button", { name: "Wartungsmodus ausdrücklich deaktivieren" })
    .click();
  await editor.waitForURL("**mgms_notice=changed");
  response = await page.goto(base);
  assert.equal(response.status(), 200);
  ok("Ausdrückliches Deaktivieren gibt Website frei");
  fs.writeFileSync(
    ".local/browser-results.json",
    JSON.stringify({ passed: results.length, results }, null, 2),
  );
  await browser.close();
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
