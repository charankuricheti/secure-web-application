const form = document.querySelector("#contact-form");
const message = document.querySelector("#message");
const contacts = document.querySelector("#contacts");
const refreshButton = document.querySelector("#refresh");

async function loadContacts() {
  contacts.replaceChildren();
  try {
    const response = await fetch("api.php", { headers: { "Accept": "application/json" } });
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || "Could not load contacts.");
    for (const contact of data.contacts) {
      const item = document.createElement("li");
      // textContent treats database values as text, not executable HTML.
      item.textContent = `${contact.name} — ${contact.email}`;
      contacts.appendChild(item);
    }
  } catch (error) {
    message.textContent = error.message;
  }
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  message.textContent = "";
  const name = form.elements.name.value.trim();
  const email = form.elements.email.value.trim();

  // Client-side checks improve usability; PHP repeats validation on the server.
  if (!name || name.length > 80 || !email || email.length > 254) {
    message.textContent = "Enter a name (up to 80 characters) and a valid email.";
    return;
  }

  try {
    const response = await fetch("api.php", {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({ name, email })
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || "Could not save contact.");
    form.reset();
    message.textContent = "Contact saved.";
    await loadContacts();
  } catch (error) {
    message.textContent = error.message;
  }
});

refreshButton.addEventListener("click", loadContacts);
loadContacts();
