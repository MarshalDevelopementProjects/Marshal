// NOTE: THE JSON DATA MUST HAVE AN ATTRIBUTE CALLED MEMBERS TO USE THIS COMPONENT WITH A HTML FILE
// NOTE:  AND A MEMBER OBJECT IN THE MEMBERS LIST MUST HAVE THE FOLLOWING ATTRIBUTES
//        { profile_picture, state, status }

// Member lists set up divs
// NOTE: MEMBER CONTAINERS SHOULD AND MUST HAVE THE FOLLOWING IDS
const ProjectLeaderListDiv = document.getElementById('leaders-list-container-div');
const ProjectMemberListDiv = document.getElementById('members-list-container-div');
const ClientListDiv = document.getElementById('client-list-container-div');

if (jsonData) {
    createProjectMemberList(jsonData);
}

// Adding project members to the list
function createProjectMemberList(args) {

    if(!ProjectLeaderListDiv && !ProjectMemberListDiv && !ClientListDiv) {
        console.error("MARK UP DOESN'T HAVE THE REQUIRED ELEMENTS");
        return;
    }
    if (args.members) {
        args.members.forEach((member) => {
            console.log(member);
            if (member.role === 'LEADER' && ProjectLeaderListDiv) {
                appendProjectMember(ProjectLeaderListDiv, member);
            } else if (member.role === 'CLIENT' && ClientListDiv) {
                appendProjectMember(ClientListDiv, member);
            } else {
                if (ProjectMemberListDiv) appendProjectMember(ProjectMemberListDiv, member);
            }
        });
    } else {
        console.error('JSON Data did not return the member data');
    }
}

// function appendProjectMember(parent_div, member_details) {
//     if (member_details !== undefined) {

//         let memberCard = document.createElement('div');
//         memberCard.setAttribute('class', 'member-card');

//         let profilePictureDiv = document.createElement('div');
//         profilePictureDiv.setAttribute('class', 'profile-image');

//         memberCard.appendChild(profilePictureDiv);

//         let profileImage = document.createElement('img');
//         profileImage.setAttribute('src', member_details.profile_picture);

//         profilePictureDiv.appendChild(profileImage);

//         let statusIcon = document.createElement('i');
//         statusIcon.setAttribute('class', 'fa fa-circle');
//         statusIcon.setAttribute('aria-hidden', 'true'); // need to ask about this

//         profilePictureDiv.appendChild(profileImage);

//         if (member_details.state === "ONLINE") {
//             statusIcon.setAttribute('style', 'color: green');
//         } else {
//             statusIcon.setAttribute('style', 'color: red');
//         }

//         profilePictureDiv.appendChild(statusIcon);

//         let memberInfoDiv = document.createElement('div');
//         memberInfoDiv.setAttribute('class', 'member-info');

//         memberCard.appendChild(memberInfoDiv);

//         let memberUsername = document.createElement('h6');
//         memberUsername.innerText = member_details.username;

//         memberInfoDiv.appendChild(memberUsername);

//         let memberStatus = document.createElement('p');
//         memberStatus.innerText = member_details.status;

//         let memberDeleteDiv = document.createElement('div');
//         memberDeleteDiv.setAttribute('class', 'delete-icon');

//         memberCard.appendChild(memberDeleteDiv);

//         let TrashIcon = document.createElement('i');
//         TrashIcon.setAttribute('class', 'fa fa-trash-o');
//         TrashIcon.setAttribute('aria-hidden', 'true');

//         memberDeleteDiv.appendChild(TrashIcon);

//         memberInfoDiv.appendChild(memberStatus);

//         // parent_div.appendChild(memberDiv);
//         parent_div.appendChild(memberCard);

//     } else {
//         console.error('empty fields given');
//     }
// }

function appendProjectMember(parent_div, member_details) {
    if (member_details !== undefined) {

        let memberCard = document.createElement('div');
        memberCard.setAttribute('class', 'member-card');

        let profilePictureDiv = document.createElement('div');
        profilePictureDiv.setAttribute('class', 'profile-image');

        memberCard.appendChild(profilePictureDiv);

        let profileImage = document.createElement('img');
        profileImage.setAttribute('src', member_details.profile_picture);

        profilePictureDiv.appendChild(profileImage);

        let statusIcon = document.createElement('i');
        statusIcon.setAttribute('class', 'fa fa-circle');
        statusIcon.setAttribute('aria-hidden', 'true');

        profilePictureDiv.appendChild(statusIcon);

        if (member_details.state === "ONLINE") {
            statusIcon.setAttribute('style', 'color: green');
        } else {
            statusIcon.setAttribute('style', 'color: red');
        }

        let memberInfoDiv = document.createElement('div');
        memberInfoDiv.setAttribute('class', 'member-info');

        memberCard.appendChild(memberInfoDiv);

        let memberUsername = document.createElement('h6');
        memberUsername.innerText = member_details.username;

        memberInfoDiv.appendChild(memberUsername);

        let memberStatus = document.createElement('p');
        memberStatus.innerText = member_details.status;

        memberInfoDiv.appendChild(memberStatus);

        if (member_details.role !== "CLIENT" && member_details.role !== "LEADER") {
            let memberDeleteDiv = document.createElement('div');
            memberDeleteDiv.setAttribute('class', 'delete-icon');

            memberCard.appendChild(memberDeleteDiv);

            let TrashIcon = document.createElement('i');
            TrashIcon.setAttribute('class', 'fa fa-trash-o');
            TrashIcon.setAttribute('aria-hidden', 'true');

            memberCard.classList.add('only-members');

            memberDeleteDiv.appendChild(TrashIcon);

            TrashIcon.addEventListener('click', function(){
                console.log('delete member')
            })
        }

        parent_div.appendChild(memberCard);

    } else {
        console.error('empty fields given');
    }
}
