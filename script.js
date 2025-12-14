function contactUs() {
  alert("Redirecting to contact page...");
  // window.location.href = "contact.html"; // Use this for real navigation
}

document.addEventListener("DOMContentLoaded", function () {
  fetch("check-session.php")
    .then(res => res.json())
    .then(data => {
      const userArea = document.getElementById("userArea");
      const authLink = document.getElementById("authLink");

      if (data.loggedIn) {
        userArea.innerHTML = `
          <span>Welcome, ${data.fullname} 👋</span>
          <button id="logoutBtn" style="margin-left: 15px; padding: 6px 12px; border-radius: 8px; background: #e63946; color: white; border: none; cursor: pointer;">Logout</button>
        `;

        document.getElementById("logoutBtn").addEventListener("click", function () {
          fetch("logout.php")
            .then(res => res.text())
            .then(response => {
              if (response === "logged_out") {
                window.location.reload();
              }
            });
        });
      } else {
        authLink.style.display = "inline";
      }
    });
});

  // 🔁 Handle Admin Login click
  document.getElementById("adminLoginBtn").addEventListener("click", () => {
    window.location.href = "admin.html"; // redirect to admin login page
  });

  // 🔁 Check login status via AJAX
  fetch("check-session.php")
    .then(res => res.json())
    .then(data => {
      if (data.loggedIn) {
        // Replace auth link with user name and logout button
        const userArea = document.getElementById("userArea");
        userArea.innerHTML = `
          <span>Toll Number: 123-4568790</span>
          <span id="userInfo">👤 ${data.name}</span>
          <button id="logoutBtn" style="margin-left: 10px; padding: 5px 10px;">Logout</button>
        `;

        document.getElementById("logoutBtn").addEventListener("click", () => {
          fetch("logout.php")
            .then(() => window.location.reload());
        });
      }
    });

form.addEventListener('submit', function(e){
  e.preventDefault();

  if(validateName() && validateEmail() && validateSubject() && validateMessage()){
    const formData = new FormData();
    formData.append("name", nameInput.value);
    formData.append("email", emailInput.value);
    formData.append("subject", subjectInput.value);
    formData.append("message", messageInput.value);

    fetch("contact.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      if(response === "success"){
        alert("Message sent successfully!");
        form.reset();
      } else {
        alert("Something went wrong ⚠️ (" + response + ")");
      }
    })
    .catch(() => alert("Failed to send message ❌"));
  }
});
