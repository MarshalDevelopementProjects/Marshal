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
};
