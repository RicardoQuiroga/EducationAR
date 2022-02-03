<?php
    /* Database credentials. Assuming you are running MySQL
    server with default setting (user 'root' with no password) */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'educarps_defUser');
    define('DB_PASSWORD', 'thisIsOurDefaultUser');
    define('DB_NAME', 'educarps_DisplayFiles');
 
    // Attempt to connect to MySQL database
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
    // Check for good connection
    if (!$conn) {
        die("Connection failed: ".mysqli_connect_error());
    }

    //----------------vv-------MARKER SPECIFIC FUNCTIONS--------vv-------------------//

    // Handles uploading new files to the server and their respective information to the DB
    function uploadFile($file, $projectionType) {
        global $conn;
        // Taken from https://www.youtube.com/watch?v=2jxM7IwpiXc
        if (isset($file)) {
            // List of errors that could come up when uploading, use print_r['taskUploadFile'] somewhere below to check for code
            $uploadErrors = array(
                0 => 'Success',
                1 => 'File is larger than specified size limit',
                2 => 'File is too large for HTML form',
                3 => 'Partial upload',
                4 => 'No file uploaded',
                6 => 'Missing temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'Something else stopped the upload'
            );

            $name = str_replace(' ', '_', $file['name']);
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $path = '/materials/imgs/'; //Could be changed to check for file type, and store it in appropriate folder, but we only need images

            //Handle the upload of the image
            if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . $name)) {
                //CHECK FOR IF AUDIENCE UPLOAD, IF SO, REMOVE ORIGINAL AUDIENCE UPLOAD FROM DATABASES AND SERVER
                if ($projectionType == 'Audience') {
                    //GET NAME OF ORIGINAL FILE IN AUDIENCE
                    $sql = "SELECT Source FROM ControlData WHERE MarkerArea = 'Audience';";
                    if ($result = mysqli_query($conn, $sql)) {
                        if ($row = mysqli_fetch_assoc($result)) {
                            $toDelete = getcwd() . $row['Source'];
                        }
                    } else {
                        echo 'Error: could not find orignal audience file in server storage';
                    }
                    //DELETE ORIGINAL FILE FROM SERVER IF THAT IMAGES IS NOT ALSO USED FOR MARKER GALLERY
                    $trip = false; //Is tripped if the name of the file we want to delete is also a file in the image gallery
                    $sql = "SELECT fileName FROM DisplayFiles WHERE projectionType = 'Marker';";
                    if ($result = mysqli_query($conn, $sql)) { 
                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($row['fileName'] == $name) {
                                echo 'That file exists';
                                $trip = true;
                            }
                        }
                    }
                    if ($trip == false) {
                        if (unlink($toDelete) == 0) {
                            echo 'Error: Could not remove original audience file from server storage';
                        }
                    }
                    //REMOVE OLD IMAGE FROM DISPLAYFILES AND CONTROL DATA
                    $sql = "DELETE FROM DisplayFiles WHERE projectionType = 'Audience';";
                    mysqli_query($conn, $sql);
                }
                //INSERT NEW UPLOAD INTO DATABASE
                $sql = "INSERT INTO DisplayFiles (fileName, extension, filepath, projectiontype) VALUES ('$name', '$extension', '$path', '$projectionType');";
                if (mysqli_query($conn, $sql)) {
                    echo "Uploaded successfully.";
                    if ($projectionType == 'Marker') {
                        echo " Re-enter page to see " . $name;
                    } else {
                        //In case of audience file upload, immediately push, send image position
                        $xAxis = $_POST['xAxisMod'];
                        $yAxis = $_POST['yAxisMod'];
                        $sql = "UPDATE ControlData SET Source = '" . $path . $file . "', XPos = '$xAxis', YPos = '$yAxis' WHERE MarkerArea = '$projectionType';";
                        if (mysqli_query($conn, $sql)) {
                            echo " Re-enter page to see & select " . $name;
                        } else {
                            echo 'Error: Could not push file to ControlData';
                        }
                        pushFile($projectionType, $path . $name);
                    }
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            } else {
                echo "Error: Could not upload file to server";
            }
        }
    }

    //Simply pushes the currently selected image to be displayed, either Marker or audience
    function pushFile($projectionType, $forAudience) {
        global $conn;
        if ($projectionType == 'Marker') {
            $fileName = $_POST['markerSrcToPush'];
        } else {
            $fileName = $forAudience;
        }
        if ($fileName != "") {
            $sql = "UPDATE ControlData SET Source = '$fileName' WHERE MarkerArea = '$projectionType';";
            if (mysqli_query($conn, $sql)) {
                
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }

    // Pulls names of images for marker projection from DB and stores them in an array
    function getGalleryInfo() {
        global $conn;
        $imgNameArray = [];
        $sql = "SELECT fileName, filePath FROM DisplayFiles WHERE projectionType = 'Marker';";
        if ($result = mysqli_query($conn, $sql)) {
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($imgNameArray, $row['filePath'] . $row['fileName']);
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $arraySize = count($imgNameArray);
        return $imgNameArray;
    }

    function removeImage() {
        global $conn;
        $fileName = $_POST['imageToRemove'];
        $fileName = str_replace("/materials/imgs/", "", $fileName);

        if ($fileName != "") {
            $sql = "DELETE FROM DisplayFiles WHERE fileName = '" . $fileName . "' LIMIT 1;";
            if (mysqli_query($conn, $sql)) {
                $sql = "UPDATE ControlData SET Source = '' WHERE MarkerArea = 'Marker';";
                if (mysqli_query($conn, $sql)) {
                    echo "Deletion successful. Refresh page to see deletion.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }

    //--------------^^---------MARKER SPECIFIC FUNCTIONS---------^^------------------//

    // Gets the current image for that projection type from the ControlData table
    function getCurrentImage($projectionType) {
        global $conn;
        $sql = "SELECT Source FROM ControlData WHERE MarkerArea = '$projectionType';";
        if ($result = mysqli_query($conn, $sql)) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo $row['Source'];
            }
        } else {
            echo 'Something went wrong';
        }
    }
?>

<!DOCTYPE html>

<html lang="en">
    
    <head>
        <meta charset="utf-8">
        <title>ARPS: Instructor Control Panel</title>
        <link rel="shortcut icon" href="" type="image/x-icon">
        <link rel="stylesheet" href="./CSS/bootstrap.min.css">
        <link rel="stylesheet" href="./CSS/control.css">
        <script src="./JS/control.js"></script>
    </head>
    <body>
        <!--TOP "ARPS: INSTRUCTOR CONTROL PANEL" BAR-->
        <div class="container-fluid" id="topBar">
            <h4>ARPS: Instructor Control Panel</h4>
        </div>
        <div class="container" id="separator" style="margin-bottom: 2%;">
        </div>
        <!--MAIN WORKSPACE OF CONTROL PANEL INCLUDES
            1) LEFTHAND CONTROL PANEL
            2) "TOP" TASK & ANSWER MARKER CONTROL AREAS
            3) RIGHTHAND CONTROL PANEL
            4) "BOTTOM" MODEL MARKER CONTROL AREA-->
        <?php //postData() //DEPRECATED. ORIGINAL USED TO FILL IN CONTROLS FOR MARKERS. DOESN'T FIT NEW SPEC. ?>
        <!--<form action="" method="post" enctype="multipart/form-data">-->
            <div class="container-fluid h-100" id="mainWorkspaceDiv">
                <div class="row" id="mainWorkspace" style="margin-bottom:20px;">
                    <!-- <div class="col-sm-4 align-self-center" id="leftControl" style="display:none;">
                        <div class="card">
                            <div class="card-header">
                                <h6>Controls</h6>
                            </div>
                            <div class="card-body">
                                <p><b>Display Task:</b></p>
                                <input onclick="displayToggle('taskToggle')" type="checkbox" class="btn btn-danger" id="taskToggle" value="Off" name="taskToggle">
                                <p></p>
                                <p><b>Task Position:</b></p>
                                <div class="slidecontainer">
                                    <input type="range" min="-5" max="5" value="0" class="slider" id="xAxisTask" name="xAxisTask">
                                    <p id="xValTask"></p>
									<input type="range" min="-5" max="5" value="0" class="slider" id="yAxisTask" name="yAxisTask">
									<p id="yValTask"></p>
                                </div>
								<hr class="solid"><br>
                                <p><b>Display Answer:</b></p>
                                <input onclick="displayToggle('answerToggle')" type="checkbox" class="btn btn-danger" id="answerToggle" value="Off" name="answerToggle">
                                <p></p>
                                <p><b>Hint Position:</b></p>
                                <div class="slidecontainer">
                                    <input type="range" min="-128" max="127" value="0" class="slider" id="xAxisAns" name="xAxisAns">
									<p id="xValAns"></p>
                                    <input type="range" min="-128" max="127" value="0" class="slider" id="yAxisAns" name="yAxisAns">
									<p id="yValAns"></p>
								</div>
                            </div>
                        </div>
                    </div> -->
                    <!-- ^^DEPRECATED: ORIGINALLY ALLOWED FOR INSTRUCTOR TO MANIPULATE DISPLAYED IMAGES. NO LONGER MEETS CLIENT SPEC. -->
                    <div class="col-sm-12 align-self-center" id="markerSpace">
                        <div class="row" id="topMarkers">
                            <div class="col-sm-6 align-self-center" id="galleryContainer">
                                <div class="jumbotron" style="height:570px;">
                                    <h6 id="galleryHeader">MARKER IMAGE GALLERY</h6>
                                    <div id="gallery">
                                        <script>
                                            //FILLS GALLERY WITH EVERYTHING IN DISPLAYFILES TABLE
                                            var array = <?php echo json_encode(getGalleryInfo()); ?>;
                                            GalleryFill(array);
                                        </script>
                                    </div>
                                    <?php uploadFile($_FILES['markerUploadFile'], 'Marker'); ?>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="btn btn-group" role="group" id="markerButtons">
                                            <span class="btn btn-file btn-primary">Choose New<input type="file" oninput="prepUploadFile()" id="markerUploadFile" name="markerUploadFile" accept="image/png, image/jpeg, image/jpg"></span>
                                            <button type="submit" class="btn btn-success disabled" id="markerSubmit" disabled>Upload: No Image Chosen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-6 align-self-center" id="markerDisplay">
                                <div class="jumbotron">
                                    <h6 id="markerAreaHeader">CURRENT MARKER IMAGE</h6>
                                    <?php pushFile('Marker', NULL); //Second variable NULL because it is only needed for auidence projection?>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <span id="markerCenter">
                                            <img src="<?php getCurrentImage('Marker');?>" class="img-fluid" id="markerImg" style="width:400px;height:400px;margin:20px;" onload="getOldImage()">
                                        </span>
                                        <input type="hidden" id="srcToPush" name="markerSrcToPush" value="<?php getCurrentImage('Marker');?>">
                                        <div class="btn btn-group" role="group" id="pushMarkerButtons">
                                            <!-- <button class="btn btn-primary" id="sequencePrev"><</button>
                                            <button class="btn btn-primary" id="sequenceNext">></button> -->
                                            <!-- Used for stepping through a sequence of images, not implemented -->
                                            <button type="submit" class="btn btn-success disabled" id="pushMarkerFile" disabled>Push Image</button>
                                        </div>
                                    </form>
                                    <?php removeImage() ?>
                                    <form action="" method="POST" enctype="multiplart/form-data">
                                        <button type="button" class="btn btn-danger" id="markerRemoveButton" onclick="prepRemove()">Remove Image From Gallery</button>
                                        <input type="hidden" id="imageToRemove" name="imageToRemove" value="">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 align-self-center" id="audienceMarker">
                                <div class="jumbotron">
                                    <h6 id="audienceAreaHeader">CURRENT AUDIENCE IMAGE</h6>
                                    <span id="mCenter">
                                        <div class="row">	
                                    
                                            <div style="margin-left:100px;margin-right:100px;" id="audienceImgHolder">
                                                <img src="<?php getCurrentImage('Audience');?>" class="img-fluid" id="audienceImg" style="width:400px;height:400px;margin:20px;">
                                            </div>
                                            
                                    <?php uploadFile($_FILES['audienceUploadFile'], 'Audience')?>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                            <div id="positioningContainer">
                                                <p><b>Audience Image Position:</b></p>
                                                <div class="slidecontainer">
                                                    <input type="range" min="-4" max="4" value="0" class="slider" id="xAxisMod" name="xAxisMod">
                                                    <p id="xValMod"></p>
                                                    <input type="range" min="-4" max="4" value="0" class="slider" id="yAxisMod" name="yAxisMod">
                                                    <p id="yValMod"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </span>
                                        <div class="btn btn-group" id=AudienceButtons>
                                            <span class="btn btn-file btn-primary">Choose New<input type="file" oninput="uploadFile('audienceUploadFile', 'audienceImgHolder', 'audienceImg')" id="audienceUploadFile" name="audienceUploadFile"></span>
                                            <button class="btn btn-success disabled" id="modelSubmit" disabled>Upload & Push</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
						<!-- <div class="text-center">
							<button type="submit" class="btn btn-success" id="masterSubmit higher">Submit</button>
						</div> -->
                </div>
            </div> 
       <!-- </form> -->
    </body>
    <footer>
        <script>
            //Stops form resubmission prompt
            //NOTE: DOES NOT WORK WITH SAFARI
            if (window.history.replaceState) {                                        
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </footer>
</html>
