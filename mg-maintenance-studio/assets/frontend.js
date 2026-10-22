(() => {
  "use strict";
  // Ohne Dialog-Unterstützung bleiben die normalen Ankerlinks erhalten.
  if (typeof HTMLDialogElement !== "undefined") {
    document.querySelectorAll("[data-modal]").forEach((link) => {
      const section = document.getElementById(link.dataset.modal);
      if (!section) return;
      const dialog = document.createElement("dialog");
      dialog.setAttribute(
        "aria-labelledby",
        section.getAttribute("aria-labelledby"),
      );
      const close = document.createElement("button");
      close.type = "button";
      close.className = "modal-close";
      close.textContent = "Schließen";
      const marker = document.createComment("Platz des Rechtstextes");
      section.before(marker);
      document.body.append(dialog);
      dialog.append(close);
      let trigger;
      link.addEventListener("click", (event) => {
        event.preventDefault();
        trigger = link;
        dialog.append(section);
        section.querySelector(".legal-back").hidden = true;
        dialog.showModal();
        document.body.classList.add("modal-open");
        close.focus();
      });
      close.addEventListener("click", () => dialog.close());
      dialog.addEventListener("close", () => {
        marker.after(section);
        section.querySelector(".legal-back").hidden = false;
        document.body.classList.remove("modal-open");
        trigger?.focus();
      });
      dialog.addEventListener("click", (event) => {
        if (event.target !== dialog || document.body.dataset.backdrop !== "1")
          return;
        const bounds = dialog.getBoundingClientRect();
        if (
          event.clientX < bounds.left ||
          event.clientX > bounds.right ||
          event.clientY < bounds.top ||
          event.clientY > bounds.bottom
        )
          dialog.close();
      });
      dialog.addEventListener("keydown", (event) => {
        if (event.key !== "Tab") return;
        const nodes = [
          ...dialog.querySelectorAll(
            'a[href],button:not([disabled]),[tabindex="0"]',
          ),
        ].filter((node) => !node.hidden && node.getClientRects().length);
        const first = nodes[0],
          last = nodes[nodes.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      });
    });
  }
  const countdown = document.querySelector("[data-until]");
  if (countdown) {
    const update = () => {
      const seconds = Math.max(
        0,
        Math.floor((Date.parse(countdown.dataset.until) - Date.now()) / 1000),
      );
      countdown.textContent = seconds
        ? `${Math.floor(seconds / 86400)} Tage · ${Math.floor(seconds / 3600) % 24} Std. · ${Math.floor(seconds / 60) % 60} Min.`
        : "Wir sind bald wieder für Sie da.";
    };
    update();
    setInterval(update, 30000);
  }
})();
