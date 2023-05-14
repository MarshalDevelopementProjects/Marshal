const dateInput = document.querySelector(".min-date");
const timeInput = document.getElementById("conf_at"); // Replace "conf_at" with the ID of your time input

dateInput.setAttribute("type", "date"); // Change input type to date
timeInput.setAttribute("type", "time"); // Set the type of the time input to time

const today = new Date();
const formattedDate = today.toISOString().split("T")[0]; // Format date as yyyy-mm-dd
const formattedTime = today.toTimeString().split(" ")[0]; // Format time as hh:mm:ss

dateInput.setAttribute("min", formattedDate); // Set minimum date

dateInput.addEventListener("input", function() {
  const selectedDate = new Date(dateInput.value);
  const currentTime = new Date();
  const currentHours = currentTime.getHours();
  const currentMinutes = currentTime.getMinutes();
  const minTime = `${currentHours.toString().padStart(2, "0")}:${currentMinutes.toString().padStart(2, "0")}`;
 
  if (selectedDate.toLocaleDateString() === today.toLocaleDateString()) {
    timeInput.setAttribute("min", minTime);
  } else {
    timeInput.removeAttribute("min");
  }
  
});
