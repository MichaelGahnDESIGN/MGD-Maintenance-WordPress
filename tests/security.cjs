const { chromium } = require("playwright");
const fs = require("node:fs");
const assert = require("node:assert/strict");
const { execFileSync } = require("node:child_process");
const base = "http://127.0.0.1:18865";
const fixture = (action) =>
  execFileSync(
    "docker",
    [
      "exec",
      "-e",
      "MGMS_TEST=isolated-local",
      "mgms-local-wordpress-1",
      "php",
      "/tmp/mgms-security.php",
      action,
    ],
    { stdio: "pipe" },
  );
const ok = (label) => console.log(`PASS ${label}`);
(async () => {
  fixture("reader");
  execFileSync("docker", [
    "cp",
    "mgms-local-wordpress-1:/tmp/mgms-reader.json",
    ".local/reader.json",
  ]);
  fs.chmodSync(".local/reader.json", 0o600);
  const browser = await chromium.launch({ headless: true, channel: "chrome" });
  const login = async (file) => {
    const data = JSON.parse(fs.readFileSync(file));
    const context = await browser.newContext();
    const page = await context.newPage();
    await page.goto(base + "/wp-login.php");
    await page.locator("#user_login").fill(data.user);
    await page.locator("#user_pass").fill(data.password);
    await page.locator("#wp-submit").click();
    await page.waitForURL("**/wp-admin/**");
    return { context, page };
  };
  const admin = await login(".local/login.json"),
    reader = await login(".local/reader.json");
  let r = await reader.page.goto(base);
  assert.equal(r.status(), 503);
  assert.equal(await reader.page.locator(".mgms-badge").count(), 0);
  ok("Angemeldeter Abonnent bleibt ohne Freigabe in Wartung");
  const url = base + "/wp-admin/options-general.php?page=mg-maintenance-studio";
  await admin.page.goto(url);
  const preview = await admin.page
    .locator("#preview-form")
    .evaluate((el) => Object.fromEntries(new FormData(el)));
  r = await reader.context.request.post(base + "/wp-admin/admin-post.php", {
    form: preview,
  });
  assert.equal(r.status(), 403);
  ok("Vorschau für fremden Benutzer gesperrt");
  const anon = await browser.newContext();
  r = await anon.request.post(base + "/wp-admin/admin-post.php", {
    form: preview,
  });
  assert.equal(r.status(), 400);
  ok("Vorschau für anonyme Benutzer gesperrt");
  fixture("expire");
  r = await admin.context.request.post(base + "/wp-admin/admin-post.php", {
    form: preview,
  });
  assert.equal(r.status(), 403);
  ok("Abgelaufener Vorschau-Token abgewiesen");
  const state = await admin.page
    .locator("#panel-0 form")
    .evaluate((el) => Object.fromEntries(new FormData(el)));
  r = await admin.context.request.post(base + "/wp-admin/admin-post.php", {
    form: { ...state, intent: "disable" },
  });
  assert.equal(r.status(), 400);
  ok("Statusänderung ohne Bestätigung abgewiesen");
  r = await reader.context.request.post(base + "/wp-admin/admin-post.php", {
    form: { ...state, intent: "disable", confirm_state: "1" },
  });
  assert.equal(r.status(), 403);
  ok("Schreibaktion ohne Administratorberechtigung abgewiesen");
  const post = await admin.page
    .locator("#mgms-editor")
    .evaluate((el) => Object.fromEntries(new FormData(el)));
  r = await admin.context.request.post(base + "/wp-admin/admin-post.php", {
    form: { ...post, intent: "publish" },
  });
  assert.equal(r.status(), 400);
  ok("Veröffentlichung ohne Bestätigung serverseitig abgewiesen");
  r = await admin.context.request.post(base + "/wp-admin/admin-post.php", {
    form: { ...post, revision: "stale", intent: "draft" },
  });
  assert.equal(r.status(), 409);
  ok("Veralteter Entwurf abgewiesen");
  await admin.page
    .getByRole("tab", { name: "IP-Freigaben", exact: true })
    .click();
  const ip = (await admin.page.locator("#panel-4 code").textContent()).trim();
  await admin.page.locator("#mgms-ip_enabled").check();
  await admin.page.locator("#mgms-ip_list").fill(ip);
  await admin.page.locator("[name=confirm_publish]").check();
  await admin.page
    .getByRole("button", { name: "Einstellungen veröffentlichen", exact: true })
    .click();
  await admin.page.waitForURL("**mgms_notice=published");
  r = await anon.request.get(base);
  assert.equal(r.status(), 200);
  ok("IP-Freigabe funktioniert im echten HTTP-Zugriff");
  await admin.page
    .getByRole("tab", { name: "IP-Freigaben", exact: true })
    .click();
  await admin.page.locator("#mgms-ip_enabled").uncheck();
  await admin.page.locator("#mgms-ip_list").fill("");
  await admin.page.locator("[name=confirm_publish]").check();
  await admin.page
    .getByRole("button", { name: "Einstellungen veröffentlichen", exact: true })
    .click();
  await admin.page.waitForURL("**mgms_notice=published");
  r = await anon.request.get(base);
  assert.equal(r.status(), 503);
  ok("Entfernte IP-Freigabe sperrt wieder");
  await admin.page.getByRole("tab", { name: "Erweitert", exact: true }).click();
  await admin.page.locator("#mgms-preview_enabled").uncheck();
  await admin.page
    .getByRole("button", { name: "Entwurf speichern", exact: true })
    .click();
  await admin.page.waitForURL("**mgms_notice=saved");
  assert.equal(await admin.page.locator("#preview-form").count(), 0);
  ok("Vorschau separat deaktivierbar");
  await admin.page.getByRole("tab", { name: "Erweitert", exact: true }).click();
  await admin.page.locator("#mgms-preview_enabled").check();
  await admin.page
    .getByRole("button", { name: "Entwurf speichern", exact: true })
    .click();
  await admin.page.waitForURL("**mgms_notice=saved");
  fixture("finish");
  await browser.close();
})().catch((e) => {
  console.error(e.message);
  process.exit(1);
});
