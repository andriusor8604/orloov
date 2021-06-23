  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$DATABASE_HOST = 'host';
		$DATABASE_USER = 'user';
		$DATABASE_PASS = 'pass';
		$DATABASE_NAME = 'dbname';
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


///ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

		$url = 'https://api.ocr.space/parse/imageurl?apikey=RAKTAS&url=https://orloov.com/upload/'. $_POST['paieska_nuotraukos'];
		$json = file_get_contents($url);
		$json = json_decode($json);
		$text2 = $json->ParsedResults[0]->ParsedText;
		$text2 = trim(preg_replace('/\s+/', ' ', $text2));


$sql = "SELECT klausimas, atsakymas1, atsakymas2, atsakymas3, atsakymas4, atsakymas5 FROM klausimai_nuotraukos WHERE klausimas='".$text2."'";
				$result = $con->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
							$klausimas = $row['klausimas'];
							$atsakymas1 = $row['atsakymas1'];

							$atsakymas2 = $row['atsakymas2'];

							$atsakymas3 = $row['atsakymas3'];

							$atsakymas4 = $row['atsakymas4'];

							$atsakymas5 = $row['atsakymas5'];
							
							echo "<div id='question-184179-2' class='que multichoice deferredfeedback correct'>
<div class='info'>
<h3 class='no'>Klausimas
</h3>
<div class='state'>Teisinga</div>
<div class='questionflag editable' aria-atomic='true' aria-relevant='text' aria-live='assertive'>
<input type='hidden' name='q184179:2_:flagged' value='0'><input type='checkbox' id='q184179:2_:flaggedcheckbox' name='q184179:2_:flagged' value='1'><input type='hidden' value='qaid=2671155&amp;qubaid=184179&amp;qid=58374&amp;slot=2&amp;checksum=131e9288c1b68314a057435f15dc4ca3&amp;sesskey=W3xLDEKHcX&amp;newstate=' class='questionflagpostdata'><label id='q184179:2_:flaggedlabel' for='q184179:2_:flaggedcheckbox'><img src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/unflagged' alt='Nepažymėta vėliavėle' id='q184179:2_:flaggedimg'></label>
</div>
</div>
<div class='content'>
<div class='formulation clearfix'>
<h4 class='accesshide'>Klausimo tekstas</h4>
<input type='hidden' name='q184179:2_:sequencecheck' value='3'><div class='qtext'><p>".$klausimas."</p></div>
<div class='ablock'>
<div class='prompt'>Teisingi atsakymai:</div>
<div class='answer'>
<div class='r0 correct'>
<input type='radio' name='q184179:2_answer' disabled='' value='2' id='q184179:2_answer2' checked=''><label for='q184179:2_answer2' class='ml-1'><span class='answernumber'></span>".$atsakymas1."</label> <img class='icon ' alt='Teisinga' title='Teisinga' src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct'>
</div>";
if($atsakymas2 != null)
{
echo "<div class='r0 correct'>
<input type='radio' name='q184179:2_answer' disabled='' value='2' id='q184179:2_answer2' checked=''><label for='q184179:2_answer2' class='ml-1'><span class='answernumber'></span>".$atsakymas2."</label> <img class='icon ' alt='Teisinga' title='Teisinga' src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct'>
</div>";
}
if($atsakymas3 != null)
{
echo "<div class='r0 correct'>
<input type='radio' name='q184179:2_answer' disabled='' value='2' id='q184179:2_answer2' checked=''><label for='q184179:2_answer2' class='ml-1'><span class='answernumber'></span>".$atsakymas3."</label> <img class='icon ' alt='Teisinga' title='Teisinga' src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct'>
</div>";
}
if($atsakymas4 != null)
{
echo "<div class='r0 correct'>
<input type='radio' name='q184179:2_answer' disabled='' value='2' id='q184179:2_answer2' checked=''><label for='q184179:2_answer2' class='ml-1'><span class='answernumber'></span>".$atsakymas4."</label> <img class='icon ' alt='Teisinga' title='Teisinga' src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct'>
</div>";
}
if($atsakymas5 != null)
{
echo "<div class='r0 correct'>
<input type='radio' name='q184179:2_answer' disabled='' value='2' id='q184179:2_answer2' checked=''><label for='q184179:2_answer2' class='ml-1'><span class='answernumber'></span>".$atsakymas5."</label> <img class='icon ' alt='Teisinga' title='Teisinga' src='https://moodle.kauko.lt/theme/image.php/lambda/core/1588829529/i/grade_correct'>
</div>";
}

echo "</div>
</div>
</div>
</div>
</div>";
							
						}
					}
}

?>
