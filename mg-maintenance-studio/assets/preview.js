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
    const brand = document.querySelector(".brand");
    if (["text", "logo", "logo-and-text"].includes(values.brand_presentation)) {
      brand?.classList.remove("brand--text", "brand--logo", "brand--logo-and-text");
      brand?.classList.add(`brand--${values.brand_presentation}`);
    }
    if (["editorial", "centered", "split", "poster"].includes(values.layout)) {
      document.body.classList.remove(
        "mgms-layout-editorial", "mgms-layout-centered", "mgms-layout-split", "mgms-layout-poster",
      );
      document.body.classList.add(`mgms-layout-${values.layout}`);
    }
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
    const localImage = (value) => {
      if (!value) return "";
      try {
        const url = new URL(value, location.href);
        return url.origin === location.origin && ["http:", "https:"].includes(url.protocol)
          ? url.href
          : "";
      } catch {
        return "";
      }
    };
    const logoSlot = document.querySelector(".mgms-logo-slot");
    if (logoSlot) {
      const url = localImage(event.data.assets?.logo);
      if (!url) logoSlot.replaceChildren();
      else {
        let image = logoSlot.querySelector("img");
        if (!image) {
          image = document.createElement("img");
          image.className = "logo";
          logoSlot.append(image);
        }
        image.src = url;
        image.alt = typeof values.logo_alt === "string" ? values.logo_alt : "";
      }
    }
    const backgroundUrl = localImage(event.data.assets?.background);
    let background = document.querySelector(".background-image");
    if (!backgroundUrl && background) background.remove();
    else if (backgroundUrl) {
      if (!background) {
        background = document.createElement("img");
        background.className = "background-image";
        background.alt = "";
        document.body.prepend(background);
      }
      background.src = backgroundUrl;
    }
  });
  parent.postMessage({ type: "mgms-ready" }, location.origin);
})();
