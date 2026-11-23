// Effekte: Teilchen dürfen pro Bild nur kleine Schritte machen (Flacker-Test), ohne Browser
"use strict";
const fs = require("fs");
const path = require("path");
const assert = require("assert");

const code = fs.readFileSync(
  path.join(__dirname, "..", "mg-maintenance-studio", "assets", "effects.js"),
  "utf8",
);
const W = 1200;
const H = 800;
const FRAMES = 40;
const MAX_STEP = 4; // px pro Bild (Median), bei 60 fps reichlich

const run = (effect) => {
  const circles = [];
  let frame = [];
  const grad = { addColorStop() {} };
  const ctx = new Proxy(
    {
      arc: (x, y) => frame.push([x, y]),
      createRadialGradient: () => grad,
    },
    { get: (t, k) => (k in t ? t[k] : () => {}), set: () => true },
  );
  const canvas = {
    dataset: { effect, intensity: "medium" },
    getContext: () => ctx,
    hidden: false,
  };
  let cb = null;
  const sandbox = {
    document: {
      querySelector: () => canvas,
      body: {},
      hidden: false,
      addEventListener() {},
    },
    window: {
      matchMedia: () => ({ matches: false, addEventListener() {} }),
      innerWidth: W,
      innerHeight: H,
      devicePixelRatio: 1,
      addEventListener() {},
    },
    getComputedStyle: () => ({ getPropertyValue: () => "" }),
    requestAnimationFrame: (fn) => ((cb = fn), 1),
    cancelAnimationFrame() {},
    performance: { now: () => 0 },
    setTimeout,
    clearTimeout,
    Math,
    Event: class {},
  };
  new Function(...Object.keys(sandbox), code)(...Object.values(sandbox));
  let now = 0;
  const runs = [];
  for (let i = 0; i < FRAMES; i++) {
    frame = [];
    now += 16;
    cb(now);
    runs.push(frame.slice());
  }
  return runs;
};

// typischer Schritt (Median) eines Teilchens zwischen zwei Bildern, Randsprünge zählen nicht
const typicalStep = (runs) => {
  const steps = [];
  for (let i = 1; i < runs.length; i++) {
    const a = runs[i - 1];
    const b = runs[i];
    const n = Math.min(a.length, b.length);
    for (let k = 0; k < n; k++) {
      steps.push(Math.abs(a[k][0] - b[k][0]), Math.abs(a[k][1] - b[k][1]));
    }
  }
  steps.sort((x, y) => x - y);
  return steps[Math.floor(steps.length / 2)];
};

for (const effect of ["bokeh", "fireflies", "sparkles"]) {
  const runs = run(effect);
  assert.ok(runs[0].length > 0, `${effect}: zeichnet Teilchen`);
  const jump = typicalStep(runs);
  assert.ok(jump <= MAX_STEP, `${effect}: typischer Schritt ${jump.toFixed(1)} px pro Bild ist zu groß`);
  console.log(`PASS ${effect} ruhig (typischer Schritt ${jump.toFixed(1)} px)`);
}
