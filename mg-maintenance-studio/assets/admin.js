(() => {
  "use strict";
  const editor = document.getElementById("mgms-editor");
  if (!editor) return;
  // Ohne JavaScript bleiben alle Bereiche über normale Anker erreichbar.
  const tabs = [...document.querySelectorAll(".studio-tabs a")];
  document.querySelector(".studio-tabs").setAttribute("role", "tablist");
  const activateTab = (tab, focus = false) => {
    tabs.forEach((item) => {
      const selected = item === tab;
      item.setAttribute("aria-selected", String(selected));
      item.tabIndex = selected ? 0 : -1;
      document.querySelector(item.getAttribute("href")).hidden = !selected;
    });
    document.querySelector(".editor-layout").hidden =
      ["#panel-0", "#panel-9"].includes(tab.getAttribute("href"));
    if (focus) tab.focus();
  };
  tabs.forEach((tab, index) => {
    const panel = document.querySelector(tab.getAttribute("href"));
    tab.id = `studio-tab-${index}`;
    tab.setAttribute("role", "tab");
    tab.setAttribute("aria-controls", panel.id);
    panel.setAttribute("role", "tabpanel");
    panel.setAttribute("aria-labelledby", tab.id);
    tab.addEventListener("click", (event) => {
      event.preventDefault();
      activateTab(tab);
      history.replaceState(null, "", tab.getAttribute("href"));
    });
    tab.addEventListener("keydown", (event) => {
      const target = {
        ArrowRight: (index + 1) % tabs.length,
        ArrowLeft: (index + tabs.length - 1) % tabs.length,
        Home: 0,
        End: tabs.length - 1,
      }[event.key];
      if (target !== undefined) {
        event.preventDefault();
        activateTab(tabs[target], true);
      }
    });
  });
  activateTab(
    tabs.find((tab) => tab.getAttribute("href") === location.hash) || tabs[0],
  );
  editor.addEventListener(
    "invalid",
    (event) => {
      const panel = event.target.closest("[role=tabpanel]");
      const tab = tabs.find(
        (item) => item.getAttribute("aria-controls") === panel?.id,
      );
      if (tab) activateTab(tab);
    },
    true,
  );
  const frame = document.getElementById("mgms-preview");
  let dirty = false;
  const undo = [];
  const values = () =>
    Object.fromEntries(
      [...editor.querySelectorAll("[data-key]")].map((el) => [
        el.dataset.key,
        el.type === "checkbox" ? (el.checked ? "1" : "0") : el.value,
      ]),
    );
  const contrast = (a, b) => {
    const luminance = (hex) => {
      const rgb = hex
        .slice(1)
        .match(/../g)
        .map((v) => parseInt(v, 16) / 255)
        .map((v) => (v <= 0.04045 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4));
      return rgb[0] * 0.2126 + rgb[1] * 0.7152 + rgb[2] * 0.0722;
    };
    const x = luminance(a),
      y = luminance(b);
    return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05);
  };
  const refresh = () => {
    const data = values();
    frame?.contentWindow.postMessage(
      { type: "mgms-preview", values: data },
      location.origin,
    );
    const surface = data.container === "card" ? data.card : data.background;
    const minimum = Math.min(
      contrast(data.text, surface),
      contrast(data.accent, surface),
      contrast(data.button_text, data.button),
    );
    document.getElementById("contrast-hint").textContent =
      minimum < 4.5
        ? `Kontrastwarnung: ${minimum.toFixed(2)}:1. Bitte mindestens 4,5:1 für normalen Text erreichen.`
        : `Textkontrast: mindestens ${minimum.toFixed(2)}:1 in den geprüften Farbpaaren. Hintergrundbilder separat prüfen.`;
  };
  const change = () => {
    dirty = true;
    document.getElementById("dirty-state").textContent =
      "Ungespeicherte Änderungen";
    refresh();
  };
  editor.addEventListener("input", change);
  editor.addEventListener("change", change);
  editor.addEventListener("submit", (event) => {
    if (
      event.submitter?.value === "publish" &&
      !editor.querySelector("[name=confirm_publish]").checked
    ) {
      event.preventDefault();
      alert(
        "Bitte die Veröffentlichung ausdrücklich mit dem Kontrollkästchen bestätigen.",
      );
      return;
    }
    dirty = false;
  });
  window.addEventListener("beforeunload", (event) => {
    if (dirty) {
      event.preventDefault();
      event.returnValue = "";
    }
  });
  window.addEventListener("message", (event) => {
    if (
      event.origin === location.origin &&
      event.source === frame?.contentWindow &&
      event.data?.type === "mgms-ready"
    )
      refresh();
  });
  const apply = (data) => {
    for (const [key, value] of Object.entries(data)) {
      const field = document.getElementById(`mgms-${key}`);
      if (!field) continue;
      if (field.type === "checkbox")
        field.checked = value === true || value === "1";
      else field.value = value;
    }
    change();
  };
  document.querySelectorAll("[data-preset]").forEach((button) =>
    button.addEventListener("click", () => {
      undo.push({
        ...values(),
        preset: document.getElementById("mgms-preset").value,
      });
      apply(mgmsPresets[button.dataset.preset].values);
      document.getElementById("mgms-preset").value = button.dataset.preset;
      document.getElementById("undo-preset").disabled = false;
    }),
  );
  document.getElementById("undo-preset").addEventListener("click", (event) => {
    if (undo.length) apply(undo.pop());
    event.target.disabled = undo.length === 0;
  });
  document.querySelectorAll(".media-picker").forEach((button) =>
    button.addEventListener("click", () => {
      const picker = wp.media({
        title: "Lokale Datei auswählen",
        multiple: false,
        library: {
          type: button.dataset.kind === "image" ? "image" : "font/woff2",
        },
      });
      picker.on("select", () => {
        document.getElementById(button.dataset.target).value = picker
          .state()
          .get("selection")
          .first().id;
        change();
      });
      picker.open();
    }),
  );
  document
    .getElementById("preview-form")
    ?.addEventListener("submit", (event) => {
      const button = event.target.querySelector("button");
      setTimeout(() => {
        button.disabled = true;
        button.textContent =
          "Vorschau geöffnet · für neue Vorschau Editor neu laden";
      }, 0);
    });
  refresh();
})();
