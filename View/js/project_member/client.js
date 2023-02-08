LogOutButton.addEventListener("click", () => {
    fetch("http://localhost/public/user/logout", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
        })
        .then((response) => {
            if (response.ok) {
                window.location.replace("http://localhost/public/user/login");
                return;
            }
            if (!response.ok) {
                response.json();
            }
        })
        .then((data) => {
            if (data.message != undefined && data.message != undefined) {
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error(error)
        });
});

const ws_connection = new WebSocket('ws://localhost:8080/projects?forum=14');

ws_connection.onopen = (ev) => {
    console.log(ev.data);
};

ws_connection.onmessage = (ev) => {

    // Create a new message element
    const messageElement = document.createElement("div");
    messageElement.innerHTML = ev.data;

    // Add the message to the conversation
    conversation.appendChild(messageElement);

    console.log(ev.data);
};

ws_connection.onerror = (ev) => {
    console.error(ev.data);
};

ws_connection.onclose = (ev) => {
    console.log(ev.data)
};

// Get the input field and the send button
const inputField = document.querySelector("input[type='text']");
const sendButton = document.querySelector(".send-button");

// Get the conversation container
const conversation = document.querySelector(".conversation");

// Listen for clicks on the send button
sendButton.addEventListener("click", function() {
    // Get the value of the input field
    const message = inputField.value;

    ws_connection.send(message);

    // Create a new message element
    const messageElement = document.createElement("div");
    messageElement.innerHTML = message;

    // Add the message to the conversation
    conversation.appendChild(messageElement);

    // Clear the input field
    inputField.value = "";
});