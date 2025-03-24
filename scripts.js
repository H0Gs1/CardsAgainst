const form = document.querySelector("#form");
const UserName = document.querySelector("#UserName");
const Email = document.querySelector("#Email");
const Password = document.querySelector("#Password");
const CPassword = document.querySelector("#CPassword");

form.addEventListener("submit", (e) => {
  if (!validateInputs()) {
    e.preventDefault();
  }
});

function validateInputs() {
  const usernameVal = UserName.value.trim();
  const emailVal = Email.value.trim();
  const passwordVal = Password.value.trim();
  const cpasswordVal = CPassword.value.trim();
  let success = true;

  if (usernameVal === "") {
    success = false;
    setError(UserName, "Username is required");
  } else {
    setSuccess(UserName);
  }

  if (emailVal === "") {
    success = false;
    setError(Email, "Email is required");
  } else if (!validateEmail(emailVal)) {
    success = false;
    setError(Email, "Please enter a valid email");
  } else {
    setSuccess(Email);
  }

  if (passwordVal === "") {
    success = false;
    setError(Password, "Password is required");
  } else if (passwordVal.length < 8) {
    success = false;
    setError(Password, "Password must be atleast 8 characters long");
  } else {
    setSuccess(Password);
  }

  if (cpasswordVal === "") {
    success = false;
    setError(cpasswordVal, "Confirm password is required");
  } else if (cpasswordVal !== passwordVal) {
    success = false;
    setError(cpasswordVal, "Password does not match");
  } else {
    setSuccess(cpassword);
  }

  return success;
}
//element - password, msg- pwd is reqd
function setError(element, message) {
  const inputGroup = element.parentElement;
  const errorElement = inputGroup.querySelector(".error");

  errorElement.innerText = message;
  inputGroup.classList.add("error");
  inputGroup.classList.remove("success");
}

function setSuccess(element) {
  const inputGroup = element.parentElement;
  const errorElement = inputGroup.querySelector(".error");

  errorElement.innerText = "";
  inputGroup.classList.add("success");
  inputGroup.classList.remove("error");
}

var button = 
document.getElementById("CPassword");
button.addEventListener("click", clearInputField)

function clearInputField() {
  document.getElementById('NewPassword').reset();
}

var button =
document.getElementById("CPassword");
button.addEventListener("click", checkPassword)

function checkPassword(form) {
  const password1 = form.password1?.value || '';
  const password2 = form.password2?.value || '';

  // If password not entered
  if (password1 === '') {
      alert("Please enter new Password");
  }
  // If confirm password not entered
  else if (password2 === '') {
      alert("Please enter confirm password");
  }
  // If passwords do not match
  else if (password1 !== password2) {
      alert("\nPassword did not match: Please try again...");
      clearInputField();
      return false;
  }
  // If passwords match
  else {
      alert("Password Match");
      return true;
  }
}

const validateEmail = (email) => {
  return String(email)
    .toLowerCase()
    .match(
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
};
