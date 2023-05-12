const dateInputs = document.querySelectorAll(".min-date");
const today = new Date();
const formattedDate = today.toISOString().split("T")[0]; // Format date as yyyy-mm-dd

dateInputs.forEach(function(input) {
  input.setAttribute("min", formattedDate);
});
