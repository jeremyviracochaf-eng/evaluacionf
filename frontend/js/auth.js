const API_URL = 'http://127.0.0.1:8000/api';

export function saveToken(token) {
  localStorage.setItem('token', token);
}

export function getToken() {
  return localStorage.getItem('token');
}

export function isAuthenticated() {
  return !!getToken();
}

export function logout() {
  localStorage.removeItem('token');
  window.location.href = 'login.html';
}

const form = document.getElementById("loginForm");
const errorText = document.getElementById("error");

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  errorText.classList.add("hidden");

  try {
    const response = await fetch(`${API_URL}/auth/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Error al iniciar sesión");
    }

    // Guardamos token y usuario
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));

    // Redirección según rol
    if (data.user.role === "admin") {
      window.location.href = "admin.html";
    } else {
      window.location.href = "index.html";
    }

  } catch (err) {
    errorText.textContent = err.message;
    errorText.classList.remove("hidden");
  }
});

function login() {
  fetch("http://127.0.0.1:8000/api/auth/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      email: email.value,
      password: password.value
    })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.token) {
      document.getElementById("error").innerText = "Credenciales incorrectas";
      return;
    }

    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));

    if (data.user.role === "admin") {
      window.location.href = "admin.html";
    } else {
      window.location.href = "index.html";
    }
  });
}


