// from jsonData get => "progress" property and get the job done
console.log(jsonData);

const ProgressBarDiv = document.querySelector('.progress');
const ProgressBarContainerDiv = document.querySelector('.bar');

ProgressBarContainerDiv.setAttribute('style', 'width: 100%');

// ProgressBarDiv.setAttribute('style', `width: ${ContainerWidth * jsonData.progress}`);

ProgressBarDiv.setAttribute('style', `width: ${ProgressBarContainerDiv.offsetWidth * jsonData.progress}`);