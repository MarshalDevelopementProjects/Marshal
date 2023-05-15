// from jsonData get => "progress" property and get the job done
console.log(jsonData);

const ProgressBarDiv = document.querySelector('.progress');
const ProgressBarContainerDiv = document.querySelector('.bar');

// ProgressBarDiv.setAttribute('style', `width: ${jsonData.progress}`);
ProgressBarDiv.setAttribute('style', `width: ${jsonData.progress}%`);