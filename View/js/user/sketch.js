const canvas = document.querySelector('canvas'),
toolBtns = document.querySelectorAll('.tool'),
fillColor = document.querySelector('#fill-color'),
sizeSlider = document.querySelector('#size-slider'),
colorBtns = document.querySelectorAll('.color'),
clearCanvas = document.querySelector('.clear-canvas'),
saveCanvas = document.querySelector('.save'),
ctx = canvas.getContext('2d');

let prevMouseX, prevMouseY, snapshot,
isDrawing = false,
selectedTool = 'brush',
brushWidth = 2,
selectedColor = '#000';

const setCanvasBackground = () =>{
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = selectedColor;
}

window.addEventListener('load', () => {
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
    setCanvasBackground();
})
const drawTriangle = (e) => {
    ctx.beginPath();
    ctx.moveTo(prevMouseX, prevMouseY);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.lineTO((prevMouseX * 2 - e.offsetX), e.offsetY);
    ctx.closePath();
    fillColor.checked ? ctx.fill() : ctx.stroke();

}
const drawLine = (e) => {
    ctx.beginPath();
    ctx.moveTo(prevMouseX, prevMouseY);
    ctx.lineTo(e.offsetX, e.offsetY);
    // ctx.lineTO(prevMouseX * 2 - e.offsetX, e.offsetY);
    ctx.closePath();
    fillColor.checked ? ctx.fill() : ctx.stroke();

}

const drawCircle = (e) => {
    ctx.beginPath();
    let radius = Math.sqrt(Math.pow((prevMouseX - e.offsetX), 2) + Math.pow((prevMouseY - e.offsetY), 2));
    ctx.arc(prevMouseX, prevMouseY, radius, 0, 2 * Math.PI);
    fillColor.checked ? ctx.fill() : ctx.stroke();
}
const drawRect = (e) => {
    if(!fillColor.checked){
        ctx.strokeRect(e.offsetX, e.offsetY, prevMouseX - e.offsetX, prevMouseY - e.offsetY);
    }else{
        ctx.fillRect(e.offsetX, e.offsetY, prevMouseX - e.offsetX, prevMouseY - e.offsetY);
    }
}
const startDraw = (e) => {
    isDrawing = true;
    prevMouseX = e.offsetX;
    prevMouseY = e.offsetY;
    ctx.beginPath();
    ctx.lineWidth = brushWidth;
    ctx.strokeStyle = selectedColor;
    ctx.fillStyle = selectedColor;
    snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
}

const drawing =(e) => {
    if(!isDrawing) return;
    ctx.putImageData(snapshot, 0, 0);

    if(selectedTool === 'brush' || selectedTool === 'eraser') {
        ctx.strokeStyle = selectedTool == 'eraser' ? '#fff' : selectedColor;
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
    }else if(selectedTool === 'rectangle'){
        drawRect(e);
    }else if(selectedTool === 'circle'){
        drawCircle(e);
    }else{
        drawLine(e);
    }
    
}

toolBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        var activeTool = document.querySelector('.activeTool');
        if(activeTool){
            activeTool.classList.remove('activeTool');
        }
        btn.classList.add('activeTool');

        selectedTool = btn.id;
        console.log(selectedTool)
    })
})

sizeSlider.addEventListener('change', () => brushWidth = sizeSlider.value);

colorBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('.selected').classList.remove('selected');
        
        btn.classList.add('selected');
        selectedColor = window.getComputedStyle(btn).getPropertyValue("background-color")
    })
})

clearCanvas.addEventListener('click', () =>	{
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    setCanvasBackground();
})

saveCanvas.addEventListener('click', () =>	{
    const link = document.createElement('a');
    link.download = `${Date.now().jpg}`;
    link.href = canvas.toDataURL();
    link.click();
})

canvas.addEventListener('mousemove', drawing);
canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mouseup', () => isDrawing = false);
