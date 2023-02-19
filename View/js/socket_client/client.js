export class Client {
    constructor(target_url) {
        this.target_url = target_url;
        this.ws_connection = new WebSocket(this.target_url);

        this.ws_connection.onopen = (ev) => {
            console.log(ev.data);
        };

        this.ws_connection.onmessage = (ev) => {
            console.log(ev.data);
        };

        this.ws_connection.onerror = (ev) => {
            console.error(ev.data);
        };
    }
}