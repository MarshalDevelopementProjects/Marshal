console.log(jsonData)
const uploadedFiles = document.querySelector('.uploadedfiles');

var uploadFileCode = ""

if(jsonData['files']){
    jsonData['files'].forEach(file => {
        uploadFileCode += `<a href="${file['filePath']}" target="_blank" style="text-decoration: none; "text-style: none; color: black; >
                                <div class="uploadedfile">
                                    <img src="${file['profile']}" alt="dp">
                                    <div class="uploadedFileDetails">
                                        <p class="fileName">${file['fileName']}</p>
                                        <div class="uploaderDetails">
                                            <p class="uploderName">${file['uploaderName']}</p>
                                            <p class="uploadedDate">${file['date']}</p>
                                        </div>
                                        
                                    </div>
                                </div>
                            </a>`
    });
}
uploadedFiles.innerHTML = uploadFileCode

const fileUploadForm = document.getElementById("upload-file-form");

fileUploadForm.addEventListener('submit', async function(event) {
    event.preventDefault();
    const inputFile = document.getElementById("uploadedfile");

    let formData = new FormData();
    let file = inputFile.files[0];
    formData.append("uploadedfile", file);
    try {
        let response = await fetch("http://localhost/public/projectmember/fileupload", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: formData,
            headers: {
                "FILE_TYPE": file.type,
                "FILE_NAME": file.name
            }
        });
        // make this a pop up
        let message = (await response.json()).message;
        if (response.ok) {
            console.log(message);
            location.reload();
        } else {
            console.log(message);
        }

    } catch (error) {
        console.error(error);
    }
});

// set user profile picture
const profile_pic = document.querySelector('.profile-image');
profile_pic.src = jsonData['user_data']['profile_picture']