/* Status sofort ändern, ohne die aktuelle öffentliche Seite zu verlassen. */
document.addEventListener("change", async (event) => {
  const input = event.target.closest("[data-mgms-status-control] input");
  if (!input || !window.mgmsStatusControl) return;

  const control = input.closest("[data-mgms-status-control]");
  const label = control.querySelector("[data-state-label]");
  const message = control.querySelector("[data-status-message]");
  const previous = !input.checked;

  control.classList.add("is-saving");
  input.disabled = true;

  try {
    const body = new URLSearchParams({
      action: "mgms_frontend_state",
      nonce: window.mgmsStatusControl.nonce,
      enabled: input.checked ? "1" : "",
    });
    const response = await fetch(window.mgmsStatusControl.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body,
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error();
    input.checked = Boolean(result.data.enabled);
    label.textContent = result.data.label;
    message.textContent = result.data.message;
  } catch (error) {
    input.checked = previous;
    label.textContent = previous ? "AN" : "AUS";
    message.textContent = window.mgmsStatusControl.error;
  } finally {
    input.disabled = false;
    control.classList.remove("is-saving");
  }
});
