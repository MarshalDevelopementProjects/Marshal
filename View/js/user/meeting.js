// Controllers and buttons
const JoinCallBtn = document.getElementById("join-call-btn");
const LeaveCallBtn = document.getElementById("leave-btn");
const AudioOnDiv = document.getElementById("mic-on-div");
const AudioOffDiv = document.getElementById("mic-off-div");
const VideoOnDiv = document.getElementById("cam-on-div");
const VideoOffDiv = document.getElementById("cam-off-div");

// const ConnectionForm = document.getElementById("connection-form");
console.log(jsonData);

// Properties
let peerConnection;
let ws;
let localUsername = jsonData.user_data.username;
let remoteUsername = jsonData.peer.username;
const projectId = jsonData.project_id;

let localStream = null;

document.body.onload = async () => {await onLoad();};

// The start-up function
async function onLoad() {
    createWsConnection(localUsername, projectId);
}

function createWsConnection(localUsername, project_id) {
    ws = new WebSocket(`ws://localhost:8080/signal/projects/feedback?user=${localUsername}&project=${project_id}`);
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
// These are the controls
// by default you get nothing form the browser
const mediaConstraints = {
    audio: true,
    video: true
};

/*
ConnectionForm.addEventListener('submit', event => {
        event.preventDefault();
        invite();
    }
);
*/

// start a call to another user
function invite(localUsername, remoteUsername) {
    if(peerConnection) {
        alert("You are already on a call, you cannot start another call");
    } else {

        // No need to have the form in here
        /*let formData = new FormData(ConnectionForm);
        let formObj = Object.fromEntries(formData);
        if (formObj.remoteUsername === formObj.localUsername) {
            alert("You cannot call yourself.");
            return;
        }*/

        /*localUsername = formObj.localUsername;
        remoteUsername = formObj.remoteUsername;*/

        if (remoteUsername === localUsername) {
            alert("You cannot call yourself.");
            return;
        }

        createPeerConnection();
         navigator.mediaDevices.getUserMedia(mediaConstraints).then((stream) => {
             localStream = stream;
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
    localStream = null;
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
   /* document.getElementById("hangup").disabled = false;*/
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
    // closeVideoCall();
    closeVideoCallOfRemote();
}

// hanging up a call and performing clean up
function hangUpCall() {
    if (peerConnection) {
        closeVideoCall();
        sendToServer({
            name: localUsername,
            target: remoteUsername,
            type: "hang-up",
        });
    } else {
        const localVideo = document.getElementById("local-video");
        if (localVideo.srcObject) {
            localVideo.srcObject.getTracks().forEach((track) => track.stop());
        }
        localVideo.removeAttribute("src");
        localVideo.removeAttribute("srcObject");
    }
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
    localVideo.removeAttribute("srcObject");

    /*document.getElementById("hangup").disabled = true;
    remoteUsername = null;*/
}

function closeVideoCallOfRemote() {
    const remoteVideo = document.getElementById("remote-video");

    if(peerConnection) {
        /*peerConnection.ontrack = null;
        peerConnection.onremovetrack = null;
        peerConnection.onicecandidate = null;
        peerConnection.oniceconnectionstatechange = null;
        peerConnection.onsignalingstatechange = null;
        peerConnection.onicegatheringstatechange = null;
        peerConnection.onnegotiationneeded = null;

        if (remoteVideo.srcObject) {
            remoteVideo.srcObject.getTracks().forEach((track) => track.stop());
        }
        peerConnection.close();
        peerConnection = null;*/

        if (remoteVideo.srcObject) {
            remoteVideo.srcObject.getTracks().forEach((track) => track.stop());
        }
        remoteVideo.removeAttribute("src");
        remoteVideo.removeAttribute("srcObject");
    }



    /*document.getElementById("hangup").disabled = true;
    remoteUsername = null;*/
}


// reporting errors
function reportError(errMessage) {
    log_error(`Error ${errMessage.name}: ${errMessage.message}`);
}

function log_error(text) {
    let time = new Date();
    console.trace("[" + time.toLocaleTimeString() + "] " + text);
}

JoinCallBtn.addEventListener('click', async (event) => {
    console.log("called");
    event.preventDefault();
    invite(localUsername, remoteUsername);
    const LocalVideo = document.getElementById("local-video");
    // LocalVideo.setAttribute("z-index", "100");
    // LocalVideo.setAttribute("background-color", "red");
    // const RemoteVideo = document.getElementById("local-video");
    // TODO: JOIN THE USER TO THE MEETING WITH THE PEER(DON'T TURN ON THE VIDEO AND THE AUDIO)
});

LeaveCallBtn.addEventListener('click', async (event) => {
    event.preventDefault();
    hangUpCall();
});

AudioOnDiv.addEventListener('click',async () => {
    // TODO: TURN THE AUDIO STREAM ON
    if (localStream) localStream.getAudioTracks()[0].enabled = true;
});

AudioOffDiv.addEventListener('click',async () => {
    // TODO: TURN THE AUDIO STREAM OFF
    // TODO: TURN THE VIDEO STREAM OFF
    if (localStream) localStream.getAudioTracks()[0].enabled = false;
});

VideoOnDiv.addEventListener('click', async () => {
    // TODO: TURN THE VIDEO STREAM ON
    if (localStream) localStream.getVideoTracks()[0].enabled = true;
});

VideoOffDiv.addEventListener('click', async () => {
    // TODO: TURN THE VIDEO STREAM OFF
    if (localStream) localStream.getVideoTracks()[0].enabled = false;
});