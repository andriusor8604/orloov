<?php
error_reporting(0);
chmod(dirname(__FILE__). '/upload', 0777);
chmod(dirname(__FILE__). '/temp', 0777);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
		
// Change this to your connection info.
$DATABASE_HOST = '45.84.204.103';
$DATABASE_USER = 'u156090805_notfaceit';
$DATABASE_PASS = 'Lopas4567';
$DATABASE_NAME = 'u156090805_klubasonline';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* check if server is alive */
if (mysqli_ping($con)) {
    ///printf ("Our connection is ok!\n");
} else {
    printf ("Error: %s\n", mysqli_error($con));
}


if(isset($_POST['submit'])){
	
	$file = dirname(__FILE__). '/upload/' . $_FILES['image']['name'];
	move_uploaded_file($_FILES['image']['tmp_name'], $file);
	
	///$obj = new TesseractOCR($file);
	///$obj->setTempDir(dirname(__FILE__).'/temp');
	///$text = $obj->recognize();
	///$text = $obj->run();
    $url = 'https://api.ocr.space/parse/imageurl?apikey=b5002fbc8f88957&url=https://orloov.com/ocr/upload/'. $_FILES['image']['name'];
	$json = file_get_contents($url);
	$json = json_decode($json);
	$text = $json->ParsedResults[0]->ParsedText;
	$text = trim(preg_replace('/\s+/', ' ', $text));
	
	$sql = "SELECT klausimas, atsakymas1 FROM klausimai_nuotraukos WHERE klausimas='".$text."'";
				$result = $con->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
							$klausimas = $row['klausimas'];
							$atsakymas = $row['atsakymas1'];
						}
					}
				}




?>

<div style="width:80%; margin:20px auto;">
	<div style="float:left; width:45%;">
		<form method="post" enctype="multipart/form-data" >
			Upload file: <input type="file" name="image" /> <br /><br />
			<!--
			Or <br /><br />
			Image url : <input type="text" name="image_url" /><br /><br /> -->
			
			<input type="submit" value="Submit"  name="submit" />
		</form>
	</div>
	<div style="float:right; width:50%;">
		<?php
			if(isset($_POST['submit'])){
				echo '<b>Recognized Data:</b><br /> <br />' . $text;
				
				echo '<br /><br /> <br /><img style="max-width:500px;" src="./upload/'.$_FILES['image']['name'].'" />'; 
				
				?>
				<div id="question-184179-2" class="que multichoice deferredfeedback correct">
<div class="info">
<h3 class="no">Klausimas
</h3>
<div class="state">Teisinga</div>
<div class="questionflag editable" aria-atomic="true" aria-relevant="text" aria-live="assertive">
<input type="hidden" name="q184179:2_:flagged" value="0"><input type="checkbox" id="q184179:2_:flaggedcheckbox" name="q184179:2_:flagged" value="1"><input type="hidden" value="qaid=2671155&amp;qubaid=184179&amp;qid=58374&amp;slot=2&amp;checksum=131e9288c1b68314a057435f15dc4ca3&amp;sesskey=W3xLDEKHcX&amp;newstate=" class="questionflagpostdata"><label id="q184179:2_:flaggedlabel" for="q184179:2_:flaggedcheckbox"><img src="https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/unflagged" alt="Nepažymėta vėliavėle" id="q184179:2_:flaggedimg"></label>
</div>
</div>
<div class="content">
<div class="formulation clearfix">
<h4 class="accesshide">Klausimo tekstas</h4>
<input type="hidden" name="q184179:2_:sequencecheck" value="3"><div class="qtext"><p><?=$klausimas?></p></div>
<div class="ablock">
<div class="prompt">Teisingi atsakymai:</div>
<div class="answer">
<div class="r0 correct">
<input type="radio" name="q184179:2_answer" disabled="" value="2" id="q184179:2_answer2" checked=""><label for="q184179:2_answer2" class="ml-1"><span class="answernumber">a. </span><?=$atsakymas?></label> <img class="icon " alt="Teisinga" title="Teisinga" src="https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct">
</div>
</div>
</div>
</div>
</div>
</div>
				<?
				
			}
		?>
	</div>
	<div style="clear:both;"></div>
</div>
