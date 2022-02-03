/*
//------------==SLIDERS==------------//
//TASK PROJECTION X and Y positioning
var xsliderT = document.getElementById("xAxisTask");
var xoutputT = document.getElementById("xValTask");
var ySliderT = document.getElementById("yAxisTask");
var yOutputT = document.getElementById("yValTask");

xoutputT.innerHTML = "X Pos: " + xsliderT.value; // Display the default slider value
yOutputT.innerHTML = "Y Pos: " + ySliderT.value;

// Update the current slider value (each time you drag the slider handle)
xsliderT.oninput = function () {
  xoutputT.innerHTML = "X Pos: " + this.value;
}

ySliderT.oninput = function () {
  yOutputT.innerHTML = "Y Pos: " + this.value;
}

// ANSWER PROJECTION X and Y positioning
var xSliderA = document.getElementById("xAxisAns");
var xOutputA = document.getElementById("xValAns");
var ySliderA = document.getElementById("yAxisAns");
var yOutputA = document.getElementById("yValAns");

xOutputA.innerHTML = "X Pos: " + xSliderA.value;
yOutputA.innerHTML = "Y Pos: " + ySliderA.value;

xSliderA.oninput = function () {
  xOutputA.innerHTML = "X Pos: " + this.value;
}

ySliderA.oninput = function () {
  yOutputA.innerHTML = "Y Pos: " + this.value;
}
*/
// MODEL PROJECTION X and Y positioning
window.onload = function() {
  xSliderM = document.getElementById("xAxisMod");
  xOutputM = document.getElementById("xValMod");
  ySliderM = document.getElementById("yAxisMod");
  yOutputM = document.getElementById("yValMod");
  xOutputM.innerHTML = "X Pos: " + xSliderM.value;
  yOutputM.innerHTML = "Y Pos: " + ySliderM.value;


  xSliderM.oninput = function () {
    xOutputM.innerHTML = "X Pos: " + this.value;
  }

  ySliderM.oninput = function () {
    yOutputM.innerHTML = "Y Pos: " + this.value;
  }
}
//------------==SLIDERS==------------//

//------------==BUTTONS==------------//
/*
function displayToggle(toggleID) {
  var toggle = document.getElementById(toggleID);
  if (toggle.value == "Off") {
    toggle.value = "On";
    toggle.className = "btn btn-success";
  } else {
    toggle.value = "Off";
    toggle.className = "btn btn-danger";
  }
}
*/
//ABOVE DEPRECATED.

//Originally used to display newly uploaded image, now only does so for audience image
function uploadFile(markerArea, center, image) {
  var file = document.getElementById(markerArea).files[0];
  var span = document.getElementById(center);
  var imgHTML = '<img src="" class="img-fluid" id="audienceImg" style="height:500px;margin:20px;">';
  span.innerHTML = imgHTML;
  var img = document.getElementById(image);
  img.src = URL.createObjectURL(file);
  var uploadButton = document.getElementById("modelSubmit");
  console.log(uploadButton)
  if (uploadButton.disabled) {
    console.log("enabling");
    uploadButton.className = "btn btn-success";
    uploadButton.disabled = false;
  }
}

var oldImage;
function getOldImage() {
  oldImage = document.getElementById('markerImg').src;
  var res = oldImage.split("/");
  oldImage = res[res.length - 1];
}

function showSelectedFile(input) {
  console.log("OLD: " + oldImage);
  console.log(input);
  //CHANGES CURRENT DISPLAYED MARKER IMAGE
  var span = document.getElementById('markerCenter');
  var imgHTML = '<img src="" class="img-fluid" id="markerImg" style="height:400px;margin:20px;">';
  span.innerHTML = imgHTML;
  var img = document.getElementById('markerImg');
  img.src = input;
  img.value = input;

  //CHECK IF FILE WAS ALREADY PUSHED, ALLOW FOR PUSH ACCORDINGLY
  var pushButton = document.getElementById('pushMarkerFile');
  var forCheck = input.split("/");
  forCheck = forCheck[forCheck.length - 1];
  console.log("OLD IMAGE: " + oldImage + " FOR CHECK: " + forCheck);
  if (forCheck != oldImage) {
    //They are different, enable push
    console.log("enabling");
    pushButton.className = "btn btn-success";
    pushButton.disabled = false;
  } else {
    //They are the same, disable push
    pushButton.className = "btn btn-success disabled";
    pushButton.disabled = true;
  }

  //CHANGES SRCTOPUSH FOR WHEN PUSH OCCURS
  var hiddenInput = document.getElementById("srcToPush");
  hiddenInput.value = input;

  //RESET REMOVE IF IT'S BEEN POKED
  var removeButton = document.getElementById("markerRemoveButton");
  if (removeButton.getAttribute("type") == "submit") {
    removeButton.setAttribute("type", "button");
    removeButton.setAttribute("onclick", "prepRemove()");
    removeButton.innerHTML = "Remove Image From Gallery";
  }
}

function changeModelFile() {
  var input = document.getElementById('modelUploadFile').files[0]
  console.log(input);
  var span = document.getElementById('modImgDiv');
  var imgHTML = '<img src="" class="img-fluid" id="modelImg" style="height:500px;margin:20px;">';
  span.innerHTML = imgHTML;
  var img = document.getElementById('modelImg');

  img.src = URL.createObjectURL(input);
}

//Displays name of file to upload next to upload button. 
function prepUploadFile() {
  var file = document.getElementById("markerUploadFile").value;
  var res = file.split("\\");
  file = res[res.length - 1];
  var target = document.getElementById("markerSubmit");
  console.log(file);
  target.innerHTML = "Upload: " + file;
  console.log("I'm doing this");
  if (target.disabled) {
    console.log("enabling");
    target.className = "btn btn-success";
    target.disabled = false;
  }
}

function prepRemove() {
  var imageToRemove = document.getElementById("markerImg").getAttribute("src");
  var hidden = document.getElementById("imageToRemove");
  hidden.value = imageToRemove;
  var changeButton = document.getElementById("markerRemoveButton");
  setTimeout(function() {document.getElementById("markerRemoveButton").setAttribute("type", "submit");}, 100);
  changeButton.setAttribute("onclick", "");
  changeButton.innerHTML = "Press Again To Remove";
}

//------------==BUTTONS==------------//

function GalleryFill(array) {
  for (i = array.length - 1; i >= 0; i--) {
    document.getElementById("gallery").innerHTML += "<button class='galimg'><img src='" + array[i] + "' onclick=showSelectedFile(\'" + array[i] + "\') class=\'galimgsrc\' id=\'galimgsrc\'></button>";
  }
}
