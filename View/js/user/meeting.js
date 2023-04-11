const ConnectionForm = document.getElementById("connection-form");

let peerConnection;
let ws;
let localUsername;
let remoteUsername;
const projectId = 1;

function createWsConnection(username) {
    ws = new WebSocket(`ws://localhost:8080/signal/projects/feedback?user=${username}&project=${projectId}`);
    ws.onopen = (event) => wsOnOpen(event);
    ws.onclose = (event) =>  wsOnClose(event);
    ws.onmessage = (event) => wsOnMessage(event);
    ws.onerror = (event) => wsOnError(event);
}

function wsOnOpen(event) {
    console.log(JSON.parse(event.data));
}

function wsOnClose(event) {
    console.log(JSON.parse(event.data));
}

function wsOnError(event) {
    console.error(JSON.parse(event.data));
}

function wsOnMessage(event) {
    let message = JSON.parse(event.data);
    console.log(`name :: ${message.name} target:: ${message.target} TYPE:: ${message.type}`);
    console.log(message);
    switch (message.type) {
        case "video-offer":  // Invitation and offer to chat
            handleVideOfferMsg(message);
            break;

        case "video-answer":  // Callee has answered our offer
            handleVideoAnswerMsg(message);
            break;

        case "new-ice-candidate": // A new ICE candidate has been received
            handleNewICECandidateMsg(message);
            break;

        case "hang-up": // The other peer has hung up the call
            handleHangUpMsg(message);
            break;

        // Unknown message; output to console for debugging.
        default:
            console.log("Unknown message received:");
            console.log(message);
    }
}


function sendToServer(message) {
    ws.send(JSON.stringify(message));
}


// What we want to get from the local browser
const mediaConstraints = {
    audio: true,
    video: true
};

ConnectionForm.addEventListener('submit', event => {
        event.preventDefault();
        invite(event);
    }
);

// start a call to another user
function invite(event) {
    if(peerConnection) {
        alert("You are already on a call, you cannot start another call");
    } else {
        let formData = new FormData(ConnectionForm);
        let formObj = Object.fromEntries(formData);
        if (formObj.remoteUsername === formObj.localUsername) {
            alert("You cannot call yourself.");
            return;
        }

        localUsername = formObj.localUsername;
        remoteUsername = formObj.remoteUsername;

        createPeerConnection();
         navigator.mediaDevices.getUserMedia(mediaConstraints).then((stream) => {
             let localStream = stream;
             document.getElementById("local-video").srcObject = localStream;
             localStream.getTracks().forEach((track) => peerConnection.addTrack(track, localStream));
         }).catch (handleGetUserMediaError);
    }
}

// handling errors that may occur while opening the media devices
function handleGetUserMediaError(error) {
    switch(error.name) {
        case "NotFoundError": {
            alert("Unable to open your call because no camera and/or microphone were found");
        }
            break;
        case "SecurityError":
        case "PermissionDeniedError": // DO NOTHING SINCE THIS IS A REJECTION OF THE CALL
            break;
        default: {
            alert(`Error opening your camera and/or microphone: ${error.message}`);
        }
            break;
    }
    // End call and free the resources
    closeVideoCall();
}


// creating the peer connection
function createPeerConnection() {
    // peerConnection = new RTCPeerConnection({iceServers: [{urls: "stun:stun.l.google.com:19302",},],});
    peerConnection = new RTCPeerConnection();

    // adding handlers to the peerConnection to handle various event
    // these three are a must

    peerConnection.onicecandidate = handleICECandidateEvent;
    peerConnection.ontrack = handleTrackEvent;
    peerConnection.onnegotiationneeded = handleNegotiationNeededEvent;

    peerConnection.onremovetrack = handleRemoveTrackEvent;
    peerConnection.oniceconnectionstatechange = handleICEConnectionStateChangeEvent;
    // peerConnection.onicegatheringstatechange = handleICEGatheringStateChangeEvent; // read the documentation or look at the 213 line
    peerConnection.onsignalingstatechange = handleSignalingStateChangeEvent;
}

// start the negotiation process
async function handleNegotiationNeededEvent() {
    console.log("*** Offering the Call recipient an offer to join the call");
    if(peerConnection) {
        try {
            let offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            sendToServer({
                name: localUsername,
                target: remoteUsername,
                type: "video-offer", // later change this to offer
                sdp: peerConnection.localDescription,
            });
        } catch(error) {
            reportError(error);
        }
    }
}

// sending ICE candidates to the other client
function handleICECandidateEvent(event) {
    console.log("*** Sending a new ICE Candidate");
    if(event.candidate) {
        sendToServer({
            type: "new-ice-candidate",
            target: remoteUsername,
            candidate: event.candidate,
        });
    }
}

// receiving ICE candidates +> called by the WebSocket's onmessage()
function handleNewICECandidateMsg(msg) {
    console.log("*** New ICE Candidate is received");
    const candidate = new RTCIceCandidate(msg.candidate);
    peerConnection.addIceCandidate(candidate).catch(reportError);
}

// handling the incoming invitation +> called from the WebSocket's onmessage()
function handleVideOfferMsg(msg){
    console.log("*** Call recipient received the offer");
    let localStream = null;
    remoteUsername = msg.name;
    createPeerConnection();
    const description = new RTCSessionDescription(msg.sdp);
    peerConnection.setRemoteDescription(description)
        .then(() => navigator.mediaDevices.getUserMedia(mediaConstraints))
        .then((stream) => {
                localStream = stream;
                document.getElementById("local-video").srcObject = localStream;
                localStream.getTracks().forEach((track) => peerConnection.addTrack(track, localStream));
            }
        )
        .then(() => peerConnection.createAnswer())
        .then((answer) => peerConnection.setLocalDescription(answer))
        .then(() => {
            const msg = {
                name: localUsername,
                target: remoteUsername,
                type: "video-answer",
                sdp: peerConnection.localDescription,
            };
            console.log(msg);
            sendToServer(msg);
        })
        .catch(reportError);
}

// handling a new track being added
function handleTrackEvent(event) {
    document.getElementById("remote-video").srcObject = event.streams[0];
    document.getElementById("hangup").disabled = false;
}

// handling a new track being removed
function handleRemoveTrackEvent(event) {
    const stream = document.getElementById("remote-video").srcObject;
    const trackList = stream.getTracks();

    if(trackList.length === 0) {
        closeVideoCall();
    }
}

async function handleVideoAnswerMsg(msg) {
    console.log("*** Call recipient has accepted our call");
    // Configure the remote description, which is the SDP payload
    // in our "video-answer" message.

    let description = new RTCSessionDescription(msg.sdp);
    await peerConnection.setRemoteDescription(description).catch(reportError);
}

// handling state changes(all the state changes)

// handles the peer connection state
function handleICEConnectionStateChangeEvent(event) {
    switch (peerConnection.iceConnectionState) {
        /*
        // can handle other state here as well
        case "checking":
        case "completed":
        case "connected":
        case "disconnected":
        case "new":
        */
        case "closed":
        case "failed":
            closeVideoCall();
            break;
    }
}

// useful when determining, when to send the ice candidates
// to the remote, when all the candidates are gathered or
// instantly deliver the candidate on the go
// you can use this for debugging purposes as well
/*function handleICEGatheringStateChangeEvent(event) {
  // generally not implemented
  switch (peerConnection.iceGatheringState) {
    case "new":
    case "complete":
    case "gathering":
  }
}*/

// if the signaling state changes to "closed" close the call
function handleSignalingStateChangeEvent(event) {
    switch (peerConnection.signalingState) {
        case "closed":
            closeVideoCall();
            break;
    }
}

function handleHangUpMsg(msg) {
    closeVideoCall();
}

// hanging up a call and performing clean up
function hangUpCall() {
    closeVideoCall();
    sendToServer({
        name: localUsername,
        target: remoteUsername,
        type: "hang-up",
    });
}

// closing the streams(or stopping the streams), cleaning up and disposing of the RTCPeerConnection object
function closeVideoCall() {
    const remoteVideo = document.getElementById("remote-video");
    const localVideo = document.getElementById("local-video");

    if(peerConnection) {
        peerConnection.ontrack = null;
        peerConnection.onremovetrack = null;
        peerConnection.onicecandidate = null;
        peerConnection.oniceconnectionstatechange = null;
        peerConnection.onsignalingstatechange = null;
        peerConnection.onicegatheringstatechange = null;
        peerConnection.onnegotiationneeded = null;

        if (remoteVideo.srcObject) {
            remoteVideo.srcObject.getTracks().forEach((track) => track.stop());
        }
        if (localVideo.srcObject) {
            localVideo.srcObject.getTracks().forEach((track) => track.stop());
        }

        peerConnection.close();
        peerConnection = null;
    }

    remoteVideo.removeAttribute("src");
    remoteVideo.removeAttribute("srcObject");
    localVideo.removeAttribute("src");
    remoteVideo.removeAttribute("srcObject");

    document.getElementById("hangup").disabled = true;
    remoteUsername = null;
}

// reporting errors
function reportError(errMessage) {
    log_error(`Error ${errMessage.name}: ${errMessage.message}`);
}

function log_error(text) {
    let time = new Date();

    console.trace("[" + time.toLocaleTimeString() + "] " + text);
}

