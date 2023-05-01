// from jsonData get => "progress" property and get the job done
console.log(jsonData);

const ProgressBarDiv = document.getElementById('progress-bar');
const ProgressBarContainerDiv = document.getElementById('progress-bar-container');

ProgressBarContainerDiv.setAttribute('style', 'width: 100%');

// ProgressBarDiv.setAttribute('style', `width: ${ContainerWidth * jsonData.progress}`);

ProgressBarDiv.setAttribute('style', `width: ${ProgressBarContainerDiv.offsetWidth * .5}`);


