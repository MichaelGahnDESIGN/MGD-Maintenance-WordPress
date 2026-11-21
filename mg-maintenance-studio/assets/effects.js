/* Atmo-Effekte Wartungsseite - regen, schnee, lichter, partikel usw. */
(() => {
  "use strict";
  const canvas = document.querySelector(".mgms-effect");
  if (!canvas || !canvas.getContext) return;
  const ctx = canvas.getContext("2d");
  const motion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const AMOUNT = { light: 0.5, medium: 1, strong: 1.8 };
  // Basis-Anzahl pro 1000x1000 px
  const BASE = { rain: 140, snow: 90, fireflies: 34, sparkles: 110, bokeh: 18, embers: 60, fog: 7 };
  let items = [];
  let raf = 0;
  let width = 0;
  let height = 0;
  let last = 0;

  const css = (name, fallback) =>
    getComputedStyle(document.body).getPropertyValue(name).trim() || fallback;
  const rgb = (hex) => {
    const m = /^#?([0-9a-f]{6})$/i.exec(hex);
    if (!m) return [255, 255, 255];
    const n = parseInt(m[1], 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  };
  const rnd = (a, b) => a + Math.random() * (b - a);
  const rgba = (c, a) => `rgba(${c[0]},${c[1]},${c[2]},${a})`;

  const resize = () => {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = Math.round(width * dpr);
    canvas.height = Math.round(height * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  };

  // ein Teilchen je nach Effekt
  const spawn = (type, fresh) => {
    const y = fresh ? rnd(0, height) : -20;
    switch (type) {
      case "rain":
        return { x: rnd(-50, width), y, len: rnd(16, 34), v: rnd(700, 1100), a: rnd(0.25, 0.6) };
      case "snow":
        return { x: rnd(0, width), y, r: rnd(1, 3.6), v: rnd(28, 70), drift: rnd(0, Math.PI * 2), a: rnd(0.45, 0.95) };
      case "fireflies":
        return { x: rnd(0, width), y: rnd(0, height), r: rnd(2, 3.8), vx: rnd(-14, 14), vy: rnd(-12, 12), t: rnd(0, 6), a: 0 };
      case "sparkles":
        return { x: rnd(0, width), y: rnd(0, height), r: rnd(0.5, 1.7), t: rnd(0, 6), s: rnd(0.6, 2.2), vy: rnd(-6, -1) };
      case "bokeh":
        return { x: rnd(0, width), y: rnd(0, height), r: rnd(24, 90), vx: rnd(-8, 8), vy: rnd(-6, 6), a: rnd(0.05, 0.16) };
      case "embers":
        return { x: rnd(0, width), y: fresh ? rnd(0, height) : height + 10, r: rnd(1.3, 3.4), vy: rnd(-70, -30), vx: rnd(-12, 12), life: rnd(0, 1), a: 1 };
      case "fog":
        return { x: rnd(-200, width), y: rnd(height * 0.35, height), r: rnd(220, 480), vx: rnd(6, 18), a: rnd(0.05, 0.11) };
      default:
        return null;
    }
  };

  const setup = () => {
    const type = canvas.dataset.effect;
    const amount = AMOUNT[canvas.dataset.intensity] || 1;
    resize();
    const count = Math.round(((BASE[type] || 0) * amount * width * height) / 1e6);
    items = Array.from({ length: Math.min(count, 600) }, () => spawn(type, true)).filter(Boolean);
  };

  const draw = (now) => {
    const dt = Math.min((now - last) / 1000 || 0, 0.05);
    last = now;
    const type = canvas.dataset.effect;
    const text = rgb(css("--text", "#ffffff"));
    const accent = rgb(css("--accent", "#ffd27a"));
    ctx.clearRect(0, 0, width, height);
    for (let i = 0; i < items.length; i++) {
      const p = items[i];
      if (type === "rain") {
        p.y += p.v * dt;
        p.x += p.v * 0.18 * dt;
        ctx.strokeStyle = rgba(text, p.a);
        ctx.lineWidth = 1.3;
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.x - p.len * 0.18, p.y - p.len);
        ctx.stroke();
        if (p.y - p.len > height) items[i] = spawn(type, false);
      } else if (type === "snow") {
        p.drift += dt;
        p.y += p.v * dt;
        p.x += Math.sin(p.drift) * 18 * dt;
        ctx.fillStyle = rgba(text, p.a);
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
        if (p.y > height + 10) items[i] = spawn(type, false);
      } else if (type === "fireflies") {
        p.t += dt;
        p.vx += rnd(-20, 20) * dt;
        p.vy += rnd(-20, 20) * dt;
        p.x = (p.x + p.vx * dt + width) % width;
        p.y = (p.y + p.vy * dt + height) % height;
        const glow = 0.35 + 0.65 * Math.abs(Math.sin(p.t * 1.3));
        // Glow + heller Kern, sonst sieht man die kaum
        const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 9);
        g.addColorStop(0, rgba(accent, 0.75 * glow));
        g.addColorStop(0.35, rgba(accent, 0.28 * glow));
        g.addColorStop(1, rgba(accent, 0));
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r * 9, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = `rgba(255,252,235,${0.95 * glow})`;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r * 0.9, 0, Math.PI * 2);
        ctx.fill();
      } else if (type === "sparkles") {
        p.t += dt * p.s;
        p.y += p.vy * dt;
        if (p.y < -5) p.y = height + 5;
        ctx.fillStyle = rgba(text, 0.15 + 0.75 * Math.abs(Math.sin(p.t)));
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
      } else if (type === "bokeh") {
        p.x = (p.x + p.vx * dt + width + 180) % (width + 180) - 90;
        p.y = (p.y + p.vy * dt + height + 180) % (height + 180) - 90;
        const g = ctx.createRadialGradient(p.x, p.y, p.r * 0.2, p.x, p.y, p.r);
        g.addColorStop(0, rgba(accent, p.a));
        g.addColorStop(0.8, rgba(accent, p.a * 0.7));
        g.addColorStop(1, rgba(accent, 0));
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
      } else if (type === "embers") {
        p.life += dt * 0.35;
        p.y += p.vy * dt;
        p.x += (p.vx + Math.sin(p.life * 6) * 10) * dt;
        const a = Math.max(0, 1 - p.life);
        ctx.fillStyle = rgba(accent, a);
        ctx.shadowColor = rgba(accent, a);
        ctx.shadowBlur = 14;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
        if (p.life >= 1 || p.y < -10) items[i] = spawn(type, false);
      } else if (type === "fog") {
        p.x += p.vx * dt;
        if (p.x - p.r > width) p.x = -p.r;
        const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
        g.addColorStop(0, rgba(text, p.a));
        g.addColorStop(1, rgba(text, 0));
        ctx.fillStyle = g;
        ctx.fillRect(p.x - p.r, p.y - p.r, p.r * 2, p.r * 2);
      }
    }
    raf = requestAnimationFrame(draw);
  };

  const start = () => {
    cancelAnimationFrame(raf);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const type = canvas.dataset.effect;
    // keine Effekte bei Bewegung reduzieren oder "none"
    if (!type || type === "none" || motion.matches || document.hidden) {
      canvas.hidden = true;
      return;
    }
    canvas.hidden = false;
    setup();
    last = performance.now();
    raf = requestAnimationFrame(draw);
  };

  let resizeTimer = 0;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(start, 200);
  });
  document.addEventListener("visibilitychange", start);
  motion.addEventListener?.("change", start);
  // Live-Vorschau schickt neue Werte
  document.addEventListener("mgms-effect-change", start);
  start();
})();
