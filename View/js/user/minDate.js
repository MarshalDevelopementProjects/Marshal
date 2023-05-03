const dateInput = document.querySelector(".min-date");
const today = new Date();
const formattedDate = today.toISOString().split("T")[0]; // Format date as yyyy-mm-dd
dateInput.setAttribute("min", formattedDate);
