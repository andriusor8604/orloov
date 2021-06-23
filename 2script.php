  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$klausimo_raktas = "";
$klausimo_atsakymas = "";
$atsakymas = "";

$string2 = $_POST['skaicius'].'.txt';

$page = file_get_contents($string2);
$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($page);
libxml_use_internal_errors(false);
$forms = $doc->getElementsByTagName('form');

$string = $_POST['paieska'];
$string = preg_replace('/[^A-Za-z0-9\-]/', '.', $string);

foreach($forms as $form) {
      if ($form->getAttribute('class') === 'questionflagsaveform') {
		  $divs = $form->getElementsByTagName('div');
			foreach($divs as $div) {
						if(preg_match('/\bque \b.*/',$div->getAttribute('class'))) {
							$klausimo_atsakymas = $doc->saveHTML($div);
						}
				
				$raktas = $div->getAttribute('id');
				if($div->getAttribute('class') === 'qtext') {
					if(preg_match('/.*'.$string.'.*/i',$div->nodeValue))
					{				
						$klausimas = $doc->saveHTML($div);
						$tikras_raktas = $div->getAttribute('id');
						echo $atsakymas = $klausimo_atsakymas;
						
					}
					else{
						$atsakymas = "Atsakymas nerastas.";
					}
				}
		}
	}
}
}

?>
