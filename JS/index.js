// window.onload = function () {
//     document.querySelector(".hint-button").addEventListener("click", function () {
//             // here you can change also a-scene or a-entity properties, like
//             // changing your 3D model source, size, position and so on
//             // or you can just open links, trigger actions...
//             alert("Hint Box");
//         });
// }

// function loaddata() {
//     var name = document.getElementById("username");
//     if (name) {
//         $.ajax({
//             type: 'post',
//             url: 'Config/loaddata.php',
//             data: {
//                 user_name: name,
//             },
//             success: function (response) {
//                 // We get the element having id of display_info and put the response inside it
//                 InsertData(response);
//             }
//         });
//     }
//     else {
//         console.log("Error loading data from Database");
//     }
// }

// function InsertData(data) {
//     document.getElementById("Tasktexture").src = "";
//     document.getElementById("Answertexture").src = "";
//     document.getElementById("Modeltexture").src = "";
//     document.getElementById("3dobj").src = "";
//     document.getElementById("Prerecordedvid").src = "";
//     document.getElementById("Aentity").position = "";
//     document.getElementById("Mentity").position = "";
//     document.getElementById("Mentity").position = "";
//     document.getElementById("Aentity").scale = "";
//     document.getElementById("Mentity").scale = "";
//     document.getElementById("Mentity").scale = "";
//     document.getElementById("Aentity").animation = "";
//     document.getElementById("Mentity").animation = "";
//     document.getElementById("Mentity").animation = "";
// }

// document.addEventListener("DOMContentLoaded", function (event) {
//     var scene = document.querySelector("a-scene");
//     var vid = document.getElementById("Prerecordedvid");
//     var videoShere = document.getElementById("3dvid");

//     if (scene.hasLoaded) {
//         run();
//     } else {
//         scene.addEventListener("loaded", run);
//     }

//     function run() {
//         if (AFRAME.utils.device.isMobile()) {
//             document.querySelector('#splash').style.display = 'flex';
//             document.querySelector('#splash').addEventListener('click', function () {
//                 playVideo();
//                 this.style.display = 'none';
//             })
//         } else {
//             playVideo();
//         }
//     }

//     function playVideo() {
//         vid.play();
//         videoShere.components.material.material.map.image.play();
//     }
//})

//---------------------------------------------------------------  
//DEPRECATED CODE, NO LONGER FIT NEW SPECS
var entityMarker;
window.onload = function() {
    var mMarkerRef = document.getElementById("Mmarker");
    mMarkerRef.addEventListener("markerFound", (e)=>{
        console.log("M FOUND");
        entityMarker = "Mentity";
    })
    mMarkerRef.addEventListener("markerLost", (e)=>{
        console.log("M LOST");
    })

    var aMarkerRef = document.getElementById("Amarker");
    aMarkerRef.addEventListener("markerFound", (e)=>{
        console.log("A FOUND");
        entityMarker = "Aentity";
    })
    aMarkerRef.addEventListener("markerLost", (e)=>{
        console.log("A LOST");
    })
}
var xStart = null;                                                        
var yStart = null;  
var posDelta = .12; 
var posLimit = 4;
var posTolerance = .01;
document.addEventListener('touchstart', originalPos, false);   
document.addEventListener('touchmove', moveImage, false);
document.addEventListener('touchend', resetRotation, false);

//Get original press location
function originalPos(event) {
    xStart = event.touches[0].clientX;
    yStart = event.touches[0].clientY;
}

//MODIFIED FROM https://stackoverflow.com/questions/2264072/detect-a-finger-swipe-through-javascript-on-the-iphone-and-android
//Best answer. Uses same general logic to determine which way is being swiped on screen. Additionally resets rotation of image 
//On every call to moveImage().
function moveImage(event) {
    setTimeout(null, 10);
    var entity = document.getElementById(entityMarker);
    var rotLock = entity.getAttribute('rotation');
    if (xStart == 0 || yStart == 0) {
        rotLock.x = -90;
        rotLock.y = 0;
        entity.setAttribute('rotation', rotLock);
        return;
    }
    
    rotLock.x = -90;
    rotLock.y = 0;
    entity.setAttribute('rotation', rotLock);
    
    var xNew = event.touches[0].clientX;                                    
    var yNew = event.touches[0].clientY;
    var xDiff = xStart - xNew;
    var yDiff = yStart - yNew;
    
    var position = entity.getAttribute('position');
    if (Math.abs(xDiff) > Math.abs(yDiff)) {
        if (xDiff > 0) {
            if (Math.abs(position.x) < posLimit) {
                position.x -= posDelta;
                if (Math.abs(position.x) >= posLimit) {
                    position.x = -Math.abs(posLimit) + posTolerance;
                }
                console.log("LEFT");
            }
        } else {
            if (position.x < posLimit) {
                position.x += posDelta;
                if (position.x >= posLimit) {
                    position.x = posLimit - posTolerance;
                }
                console.log("RIGHT");
            }
            
        }                       
    } else {
        console.log(position.z);
        if (yDiff > 0) {
            if (Math.abs(position.z) < posLimit) {
                position.z -= posDelta; 
                if (Math.abs(position.z) >= posLimit) {
                    position.z = -Math.abs(posLimit) + posTolerance;
                }
            }
            console.log("UP");
        } else {
            if (position.z < posLimit) {
                position.z += posDelta;
                if (position.z >= posLimit) {
                    position.z = posLimit - posTolerance;
                }
            }
            console.log("DOWN");
        }                                                                 
    } 
    entity.setAttribute('position', position);
    rotLock.x = -90;
    rotLock.y = 0;
    entity.setAttribute('rotation', rotLock);
    console.log(rotLock);
}

//Double check rotation suppression on letup
function resetRotation(event) {
    var rotLock = document.getElementById('Mentity').getAttribute("rotation");
    rotLock.x = -90;
    rotLock.y = 0;
    document.getElementById('Mentity').setAttribute('rotation', rotLock);
}