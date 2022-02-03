<?php
	/* Database credentials. Assuming you are running MySQL
    server with default setting (user 'root' with no password) */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'educarps_defUser');
    define('DB_PASSWORD', 'thisIsOurDefaultUser');
    define('DB_NAME', 'educarps_DisplayFiles');

	$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

	if (!$conn) {
		die("Connection failed: ".mysqli_connect_error());
	}
?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<title>Classroom AR</title>
		<link rel="shortcut icon" href="" type="image/x-icon">
		<link rel="stylesheet" href="./CSS/index.css">
		<script src="./Frameworks/Aframe.min.js"></script>
		<!-- we import arjs version without NFT but with marker + location based support -->
		<script src="./Frameworks/Aframe-ar.js"></script>
		<script src="https://raw.githack.com/fcor/arjs-gestures/master/dist/gestures.js"></script>
		<script src="./Frameworks/jQuery.min.js"></script>
		<script src="./JS/index.js"></script>
	</head>
	<body style="margin : 0px; overflow: hidden;">
		<a-assets>
			<?php
				$sql = 'SELECT * FROM ControlData';
				if ($result = mysqli_query($conn, $sql)) {
					while ($row = mysqli_fetch_assoc($result)) {
						echo '<img id="' . $row['MarkerArea'] . 'texture" src="' . $row['Source'] . '" style="width:100%;">';
					}
					mysqli_free_result($result);
				}
			?>
			<!--<img id="Tasktexture" src="materials/imgs/BaseBinary-HexProblem1.png" style="width:100%;">
			<img id="Answertexture" src="materials/imgs/BaseBinary-HexProblem1TransparentANS.png" style="width:100%;">
			<img id="Modeltexture" src="materials/imgs/BaseBinary-HexProblem1.png" style="width:100%;">
			<video id="Prerecordedvid" autoplay loop="true" src="materials/videos/Digital.mp4"></video>
			<a-asset-item id="3dobj" src="materials/models/dna.obj"></a-asset-item>-->
			<!-- ^^DEPRECATED. INCLUDING 3D MODELS AND VIDEO DOES NOT MEET NEW CLIENT SPEC. -->
			<a-mixin id="normal" scale=".05 .05 .05"></a-mixin>
		</a-assets>
		<a-scene embedded arjs="sourceType: webcam; patternRatio: 0.75 trackingMethod: best maxDetectionRate: 60 detectionMode: mono" 
			renderer="antialias: true; alpha: true; precision: medium; logarithmicDepthBuffer: true;" 
			vr-mode-ui="enabled: false;" gesture-detector id="scene">
			<!-- Marker T is the marker for Pictures with tasks-->
				<?php
					$sql = 'SELECT * FROM ControlData';
					if ($result = mysqli_query($conn, $sql)) {
						while ($row = mysqli_fetch_assoc($result)) {
							$markerLetterUpperCase = $row['MarkerArea'][0];
							$XPos = $row['XPos'];
							$YPos = $row['YPos'];
							echo '<a-marker ';
							echo 'type="pattern" url="Markers/MarkerFile/pattern-' . $markerLetterUpperCase . '.patt" id="' . $markerLetterUpperCase . 'marker" ';
							echo 'raycaster="objects: .clickable" emitevents="true" cursor="fuse: false; rayOrigin: mouse;">';
							echo '<a-entity ';
							echo 'id="' . $markerLetterUpperCase . 'entity" class="clickable" material="src:#' . $row['MarkerArea'] . 'texture;shader:flat;" geometry="primitive:plane; height:2; width:2;" gesture-handler ';
							echo 'position="'.$XPos.' 0 '.$YPos.'" rotation="-90 0 0 ">';
							echo '</a-entity>';
							echo '</a-marker>';
						}
					}
					
				?>
			<!-- <a-marker type="pattern" url="Markers/MarkerFile/pattern-T.patt" raycaster="objects: .clickable" 
				emitevents="true" cursor="fuse: false; rayOrigin: mouse;" id="markerT">
				<a-entity id="Tentity" geometry="primitive:plane;height:2;width:2;" raycaster="objects: .clickable" 
				emitevents="true" cursor="fuse: false; rayOrigin: mouse;"
					position="0 0 0" rotation="-90 0 0" material="src:#Tasktexture;shader:flat;" class="clickable" gesture-handler></a-entity>
			</a-marker> -->

			<!-- Marker A is the marker for Pictures with Answers or Hints-->
			<!-- <a-marker type="pattern" url="Markers/MarkerFile/pattern-A.patt" id="markerA">
				<a-video id="Aentity" src="#Prerecordedvid" width="3" height="3"
					position="0 0 0" rotation="-90 0 0" webkit-playsinline playsinline></a-video>
			</a-marker>
				//Marker M is the marker for Models and Helpful Infographics
			<a-marker type="pattern" url="Markers/MarkerFile/pattern-M.patt" raycaster="objects: .clickable" 
				emitevents="true" cursor="fuse: false; rayOrigin: mouse;" id="markerM">
				<a-entity id="Mentity" mixin="normal" animation="property: rotation; to: 360 0 0; loop: true; dur: 10000" 
					obj-model="obj: #3dobj;" class="clickable" gesture-handler></a-entity>
			</a-marker> -->
			<!-- ^^DEPRECATED. USE OF 3 MARKERS DOES NOT MEET NEW CLIENT SPEC. -->
			<a-entity camera></a-entity>
		</a-scene>
	</body>
</html>
