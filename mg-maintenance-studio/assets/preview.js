(() => {
  "use strict";
  // Nur Nachrichten des eigenen Admin-Fensters, niemals fremdes HTML übernehmen.
  window.addEventListener("message", (event) => {
    if (
      event.origin !== location.origin ||
      event.source !== parent ||
      event.data?.type !== "mgms-preview"
    )
      return;
    const values = event.data.values;
    if (!values || typeof values !== "object") return;
    for (const key of [
      "title",
      "description",
      "brand",
      "tagline",
      "notice",
      "newsletter",
    ]) {
      const node = document.querySelector(`[data-live="${key}"]`);
      if (node && typeof values[key] === "string")
        node.textContent = values[key];
    }
    for (const key of [
      "background",
      "text",
      "accent",
      "button",
      "button_text",
      "card",
      "border_color",
    ]) {
      if (/^#[0-9a-f]{6}$/i.test(values[key]))
        document.body.style.setProperty(
          `--${key.replaceAll("_", "-")}`,
          values[key],
        );
    }
    for (const key of [
      "width",
      "padding",
      "padding_tablet",
      "padding_mobile",
      "radius",
      "border",
      "logo_width",
    ]) {
      if (/^\d{1,4}$/.test(values[key]))
        document.body.style.setProperty(
          `--${key.replaceAll("_", "-")}`,
          `${values[key]}px`,
        );
    }
    if (["left", "center", "right"].includes(values.align))
      document.body.style.setProperty("--align", values.align);
    document.body.style.setProperty(
      "--heading-font",
      values.font === "serif" ? "Georgia,serif" : "system-ui,sans-serif",
    );
    document
      .querySelector(".content")
      .classList.toggle("is-card", values.container === "card");
    document
      .querySelector(".content")
      .classList.toggle("has-shadow", values.shadow === "1");
    document.body.classList.toggle("animate", values.animation === "1");
  });
  parent.postMessage({ type: "mgms-ready" }, location.origin);
})();
