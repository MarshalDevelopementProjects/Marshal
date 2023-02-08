// TODO: IMPLEMENT THE SSE LISTENER IN HERE
// const sse_connection = new EventSource('http://localhost/bin/NotificationHandler.php');
const sse_connection = new EventSource('http://localhost:9000', { mode: "cors" });
const DivElement = document.getElementById('div-element');
const ListElement = document.getElementById('list-element');

sse_connection.addEventListener("bobby", (event) => {
    const newElement = document.createElement("li");
    newElement.textContent = `message: ${event.data}`;
    ListElement.appendChild(newElement);
    console.log(event.data);
});

sse_connection.onmessage = (event) => {
    console.log(`Event data: ${event.data}`);
};

sse_connection.onopen = (event) => {
    console.log(`Event data: ${event.data}`);
};

sse_connection.onerror = (error) => {
    console.error(error);
};

/*
const ws_connection = new WebSocket('ws://localhost:8080/projects?forum=14');

ws_connection.onopen = (ev) => {
    console.log(ev.data);
};

ws_connection.onmessage = (ev) => {
    console.log(ev.data);
};

ws_connection.onerror = (ev) => {
    console.error(ev.data);
};

ws_connection.onclose = (ev) => {
    console.log(ev.data)
};*/
