const authForm = document.querySelector("#auth-form");
const emailInput = document.querySelector("#email");
const passwordInput = document.querySelector("#password");
const togglePasswordButton = document.querySelector("#toggle-password");
const submitButton = document.querySelector("#submit-button");
const formStatus = document.querySelector("#form-status");
const emailError = document.querySelector("#email-error");
const passwordError = document.querySelector("#password-error");
const pageType = document.body.dataset.page === "log-in" ? "log in" : "sign in";

function clearErrors() {
  emailInput.removeAttribute("aria-invalid");
  passwordInput.removeAttribute("aria-invalid");
  emailError.textContent = "";
  passwordError.textContent = "";
  formStatus.textContent = "";
  formStatus.className = "form-status";
}

function setFieldError(input, errorElement, message) {
  input.setAttribute("aria-invalid", "true");
  errorElement.textContent = message;
}

togglePasswordButton.addEventListener("click", () => {
  const shouldShow = passwordInput.type === "password";
  passwordInput.type = shouldShow ? "text" : "password";
  togglePasswordButton.textContent = shouldShow ? "HIDE" : "SHOW";
  togglePasswordButton.setAttribute("aria-pressed", String(shouldShow));
});

for (const input of [emailInput, passwordInput]) {
  input.addEventListener("input", () => {
    input.removeAttribute("aria-invalid");
    if (input === emailInput) emailError.textContent = "";
    if (input === passwordInput) passwordError.textContent = "";
    formStatus.textContent = "";
    formStatus.className = "form-status";
  });
}

authForm.addEventListener("submit", (event) => {
  event.preventDefault();
  clearErrors();

  let isValid = true;
  const email = emailInput.value.trim();
  const password = passwordInput.value;

  if (!email) {
    setFieldError(emailInput, emailError, "Please enter your email address.");
    isValid = false;
  } else if (!emailInput.validity.valid) {
    setFieldError(emailInput, emailError, "Please enter a valid email address.");
    isValid = false;
  }

  if (!password) {
    setFieldError(passwordInput, passwordError, "Please enter your password.");
    isValid = false;
  } else if (password.length < 6) {
    setFieldError(passwordInput, passwordError, "Password must be at least 6 characters.");
    isValid = false;
  }

  if (!isValid) {
    formStatus.textContent = "Please check the highlighted fields.";
    formStatus.classList.add("is-error");
    return;
  }

  formStatus.textContent = `Form validated locally. Connect a backend to ${pageType}.`;
  formStatus.classList.add("is-success");
});
