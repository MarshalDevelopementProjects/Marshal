// Then some JavaScript in the browser:
const conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) { conn.send("Hello Me!"); };
conn.onmessage = function(e) { console.log(e.data); };