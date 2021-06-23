    <?php
	session_start();
	chmod(dirname(__FILE__). '/upload', 0777);
	chmod(dirname(__FILE__). '/temp', 0777);
	
	?>
<!-- RIP kolegija -->
	<link rel="shortcut icon" href="https://moodle.kauko.lt/theme/image.php/lambda/theme/1588829529/favicon" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="moodle, LSKT: KT9(2020) Egzaminas" />
<link rel="stylesheet" type="text/css" href="https://moodle.kauko.lt/theme/yui_combo.php?rollup/3.17.2/yui-moodlesimple-min.css" /><script id="firstthemesheet" type="text/css">/** Required in order to fix style inclusion problems in IE with YUI **/</script><link rel="stylesheet" type="text/css" href="https://moodle.kauko.lt/theme/styles.php/lambda/1588829529_1/all" />
<!-- CSS only -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
<link href="./styles/css/all.css" rel="stylesheet">
<!-- JS, Popper.js, and jQuery -->
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
  
	<script>
	   var value = "";
   function getCombo(selectObject) {
    value = selectObject.value;  
   }
   
	</script>
	
	<style>
	.btn-primary:hover,.btn-primary:focus,.btn-primary:active, 
.btn-primary:active:focus:not(:disabled):not(.disabled),
.btn:focus, .btn:active, .btn:hover{
    box-shadow: none!important;
    outline: 0;
}
	</style>
 <?php
  
  
///ini_set('display_errors', 1);
///ini_set('display_startup_errors', 1);
///error_reporting(E_ALL);

//Include GP config file && User class
include_once 'gpConfig.php';
include_once 'User.php';

if(isset($_GET['code'])){
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
	//Get user profile data from google
	$gpUserProfile = $google_oauthV2->userinfo->get();
	
	//Initialize User class
	$user = new User();
	
	//Insert or update user data to the database
    $gpUserData = array(
        'oauth_provider'=> 'google',
        'oauth_uid'     => $gpUserProfile['id'],
        'first_name'    => $gpUserProfile['given_name'],
        'last_name'     => $gpUserProfile['family_name'],
        'email'         => $gpUserProfile['email'],
        'gender'        => $gpUserProfile['gender'],
        'locale'        => $gpUserProfile['locale'],
        'picture'       => $gpUserProfile['picture'],
        'link'          => $gpUserProfile['link']
    );
    $userData = $user->checkUser($gpUserData);
	
	//Storing user data into session
	$_SESSION['userData'] = $userData;
	
	//Render facebook profile data
    if(!empty($userData)){
		
	$file = fopen("whitelist.txt", "r");
	$members = array();

	while (!feof($file)) {
		$members[] = fgets($file);
	}
	fclose($file);
	$yra = false;
	$visi_donate[24];
	foreach($members as $member)
	{	
	$userData['email'] = preg_replace('/\s+/', '', $userData['email']);
	$member_splited = explode(" ", $member);
	$member_splited[0] = preg_replace('/\s+/', '', $member_splited[0]);
	$visi_donate[] = $member_splited[1];
		if(strcmp($member_splited[0], $userData['email']) == 0)	
		{
			$donate = $member_splited[1];
			$yra = true;
			
		}
	}
	rsort($visi_donate,SORT_NUMERIC);

	$k = array_search($donate,$visi_donate, true);
	$k++;
	
	if($yra == false)
	{
		header("Location: https://orloov.com/logout.php");
		exit();
	}else{
		
		
		// Change this to your connection info.
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
		
		
		if(isset($_POST['pridetiklausima_isnuotraukos'])){
	
		$file = dirname(__FILE__). '/upload/' . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], $file);
		
		$url = 'https://api.ocr.space/parse/imageurl?apikey=RAKTAS&url=https://orloov.com/upload/'. $_FILES['image']['name'];
		$json = file_get_contents($url);
		$json = json_decode($json);
		$text = $json->ParsedResults[0]->ParsedText;
		$text = trim(preg_replace('/\s+/', ' ', $text));
		
		$sql = "INSERT INTO klausimai_nuotraukos (klausimas, atsakymas1, atsakymas2, atsakymas3, atsakymas4, atsakymas5) VALUES ('".$text."', '".$_POST['atsakymas1']."', '".$_POST['atsakymas2']."', '".$_POST['atsakymas3']."', '".$_POST['atsakymas4']."', '".$_POST['atsakymas5']."')";
		mysqli_query($con,$sql);
		
		echo '<script>alert("Klausimas iš nuotraukos sėkmingai pridėtas.")</script>';
		}
		
		
		if($donate > 0.00) {
        $output .= '<br/><center><div style="width: 120px;height: 120px;position:relative;">
		<div style=" float: left;position: absolute;left: -12.5;top: -12.5px;color: green;"><img src="./images/donate-solid.svg" width="25px" height="25px"></div>
		<img src="'.$userData['picture'].'" width="120" height="120"></div>';
		}
		else
		{
		$output .= '<br/><center><div style="width: 120px;height: 120px;position:relative;">
		<div style=" float: left;position: absolute;left: -12.5;top: -12.5px;color: green;"></div>
		<img src="'.$userData['picture'].'" width="120" height="120"></div>';
		}
        ///$output .= '<br/>Google ID : ' . $userData['oauth_uid'];       ///////SEKTI PAGAL SITA
        $output .= '<br/>Esate prisijungęs kaip<br/> <b>' . $userData['first_name'].' '.$userData['last_name'].'</b><br/>';
		$output .= '<br/>Dosnumo sąraše esate: #<b>' . $k . '</b><br/>';
        $output .= 'paukoję <b>&euro; ' . $donate . '</b><br/><br><br>';
		$output .= '<button type="button" name="donatelist" id="donatelist" class="btn btn-success" style="width:150px;">Dosnumo sąrašas</button><hr>';
		$output .= '<button type="button" name="pridetisource" id="pridetisource" class="btn btn-success" style="width:150px;">Pridėti SOURCE kodą</button><br><br>';
		$output .= '<button type="button" name="pridetiklausima" id="pridetiklausima" class="btn btn-success" style="width:150px;">Pridėti klausimą iš nuotraukos</button><br><hr>';
        $output .= '<a href="logout.php">Atsijungti</a></center>'; 
		/////// VISAS SAITAS
		?>
		<div class="container">
		<div class="row justify-content-start">
		<div class="col-3">
		<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title"><center>Vartotojo informacija</center></h5>
    <div class="card-text"><?php echo $output; ?></div>
  </div>
</div>
</div>

<div id="donate_sarasas" hidden>
<?php
foreach($members as $member)
	{
		$member_splited = explode(" ", $member);
		$member_splited[0] = preg_replace('/\s+/', '', $member_splited[0]);
		$member_splited[0] = preg_replace('/@gmail.com/', '', $member_splited[0]);
		///$member = "'".$member_splited[0]."' => ".$member_splited[1].",";
		///$members2[] .= $member;
		///$member_splited[1] = str_replace('.','',$member_splited[1]);
		$vardai[] = $member_splited[0];
		$skaiciai[] = $member_splited[1];
		///$bandomasis[$i][0] = $member_splited[0];
		///$bandomasis[$i][1] = $member_splited[1];
	}
	array_multisort($skaiciai,SORT_NUMERIC,SORT_DESC, $vardai);
	?>
	<table class="table table-striped table-sm" style="margin-bottom: 0px;">
  <thead>
    <tr>
	  <th scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
      <th scope="col">Vieta</th>
      <th scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Slapyvardis</th>
      <th scope="col">Parama</th>
    </tr>
  </thead>
  <tbody>
	<?php
	for($i = 0;$i<sizeof($members);$i++)
	{
		if($skaiciai[$i] > 0)
		{
			?>
			<tr>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			<th scope="row"><?=$i + 1;?></th>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$vardai[$i]?></td>
			<td>€ <?=$skaiciai[$i]?></td>
			</tr>
			<?php
		}
	}
	?>
	<tr>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			<th scope="row">15</th>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visi kiti kurie neprisidėjo prie išlaikymo</td>
			<td>€ 0.00</td>
			</tr>
	  </tbody>
</table>
</div>
		
		
		<div class="col-9">
		<div class="container">
  <div class="card-body" style="padding-top: 0rem; padding-bottom:0rem;">
  <form name="myForm" method="POST" class="row">
  
  <div class="input-group">
  <a href="https://orloov.com" style="padding-top: 3px;"><img src="./images/home.png" style="color:green;" /></a>&nbsp;&nbsp;
   <div class="form-group">
    <select class="form-control" name="paskaitos" id="paskaitos" style="height: 30px; width:200px;">
	<option disabled selected value="0" name="0">Pasirinkti kryptį..</option>
	  <optgroup label="2 kurso paskaitos">
	  <option value="10" name="10">Tarptautinė ekonomika</option>
      <option value="1" name="1">Elektronika</option>
      <option value="2" name="2">Kompiuterių tinklai</option>
      <option value="3" name="3">Kompiuterių tinklo sauga</option>
      <option value="4" name="4">Duomenų saugumas ir kriptografija</option>
      <option value="5" name="5">Teisinis duomenų apsaugos reglamentavimas</option>
	  <option value="6" name="6">Programavimo technologijos</option>
	  </optgroup>
	  <optgroup label="Laisvai pasirenkami dalykai">
	  <option value="7" name="7" id="7">Turinio valdymo sistemos</option>
	  <option value="8" name="8" id="8">Tinklapių kūrimas</option>
	  </optgroup>
	  <optgroup label="1 kurso paskaitos">
	  <option value="9" name="9">UNIX/Linux</option>
	  </optgroup>
    </select>
  </div>
  
  <div id="paskaitu_sarasas" class="form-group" style="display: none;">
    <select class="form-control" name="paskaitos_prideti" id="paskaitos_prideti" style="height: 30px; width:200px;" onchange="getCombo(this)">
	<option disabled selected value="0" name="0">Pasirinkti kryptį..</option>
	  <optgroup label="2 kurso paskaitos">
	  <option value="20" name="20">Tarptautinė ekonomika</option>
      <option value="11" name="11" id="11">Elektronika</option>
      <option value="12" name="12" id="12">Kompiuterių tinklai</option>
      <option value="13" name="13" id="13">Kompiuterių tinklo sauga</option>
      <option value="14" name="14" id="14">Duomenų saugumas ir kriptografija</option>
      <option value="15" name="15" id="15">Teisinis duomenų apsaugos reglamentavimas</option>
	  <option value="16" name="16" id="16">Programavimo technologijos</option>
	  </optgroup>
	  <optgroup label="Laisvai pasirenkami dalykai">
	  <option value="17" name="17" id="17">Turinio valdymo sistemos</option>
	  <option value="18" name="18" id="18">Tinklapių kūrimas</option>
	  </optgroup>
	  <optgroup label="1 kurso paskaitos">
	  <option value="19" name="19" id="19">UNIX/Linux</option>
	  </optgroup>
    </select>
  </div>
  
    <input type="text" class="form-control rounded" placeholder="Klausimas ar dalis klausimo teksto" style="margin-left: 4px; height: 30px;" name="paieska" id="paieska">
    <div class="input-group-btn">
      <button type="button" name="ieskoti" id="ieskoti" class="btn btn-success" style="margin-left: 4px; height: 30px;">Ieškoti atsakymų</button>
    </div>
  </div>
</form>
</div>
<div class="card-body" style="padding-top: 0rem;">
<div class="input-group">
<form method="post" enctype="multipart/form-data" class='row' style="background-color:white;" id='forma_siusti' name='forma_siusti'>
			<span style="margin-top:14px; margin-left:10px;">Ieškomo atsakymo klausimo nuotrauka:</span><input style="height:40px !important; margin-top:4px; width: 276px;" type="file" name="file1" id="file1" /> <br /><br />
			<div class="input-group-btn">
      <button type="button" name="ieskoti_nuotraukos" id="ieskoti_nuotraukos" class="btn btn-success" style="margin-left: 79.7px; height: 44px; ">Ieškoti atsakymų iš nuotraukos</button>
    </div>
		</form>
			</div>
				</div>
				
				

<div class="card">
<div id="content" >
<div class="itemFullText" style="padding-left: 20px;padding-right: 20px;">
<br>
<h3><center>Paskaitų įrašai<center></h3>
<div class="container">



<style>
.collapsible {
  background-color: #777;
  color: white;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  box-shadow: none!important;
  outline: 0;
}

.active, .collapsible:hover {
  background-color: #555;
      box-shadow: none!important;
    outline: 0;
}

.collapsible:focus {
	    box-shadow: none!important;
    outline: 0;
}

.collapsible:after {
  content: '\002B';
  color: white;
  font-weight: bold;
  float: right;
  margin-left: 5px;
  box-shadow: none!important;
  outline: 0;
}

.active:after {
  content: "\2212";
      box-shadow: none!important;
    outline: 0;
}

.content {
  padding: 0 18px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
  background-color: #f1f1f1;
      box-shadow: none!important;
    outline: 0;
}


</style>

<button class="collapsible btn-success">CCNA3 2021-05-05 Paskaita</button>
<div class="content">
  <p><video class="row d-flex mx-auto" style="padding-top:18.5px;" width="640" height="480" preload="none" controls>
		  <source src="paskaita.mp4" type="video/mp4">
		Your browser does not support the video tag.
		</video></p>
</div>

<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight){
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    } 
  });
}
</script>

<hr>
<h3><center>Informacijos saugos technologijos<center></h3>
<a href='/saugos_atsakymai.docx' target='_blank'>Egzamino atsakymai</a><br>
<hr>
<h3><center>CCNA3 LPD<center></h3>
<h5>Netacad.com</h5>
<a href='https://itexamanswers.net/ccna-3-v7-modules-1-2-ospf-concepts-and-configuration-exam-answers.html' target='_blank'>Modules 1 – 2: OSPF Concepts and Configuration Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-v7-modules-3-5-network-security-exam-answers.html' target='_blank'>Modules 3 – 5: Network Security Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-v7-modules-6-8-wan-concepts-exam-answers.html' target='_blank'>Modules 6 – 8: WAN Concepts Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-v7-modules-9-12-optimize-monitor-and-troubleshoot-networks-exam-answers.html' target='_blank'>Modules 9 – 12: Optimize, Monitor, and Troubleshoot Networks Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-v7-modules-13-14-emerging-network-technologies-exam-answers.html' target='_blank'>Modules 13 – 14: Emerging Network Technologies Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-ensa-practice-pt-skills-assessment-ptsa-answers.html' target='_blank'>ENSA Practice PT Skills Assessment (PTSA)</a><br>
<a href='https://itexamanswers.net/ensa-version-7-00-final-pt-skills-assessment-exam-ptsa-answers.html' target='_blank'>Final PT Skills Assessment Exam (PTSA)</a><br>
<a href='https://itexamanswers.net/enterprise-networking-security-and-automation-v7-0-ensav7-practice-final-exam-answers.html' target='_blank'>ENSAv7 Practice Final Exam</a><br>
<a href='https://itexamanswers.net/ccna-3-v7-0-final-exam-answers-full-enterprise-networking-security-and-automation.html' target='_blank'>CCNA 3 v7.0 Final Exam</a><br>
<a href='https://itexamanswers.net/ccna-200-301-certification-practice-exam-answers-ensa-v7-0.html' target='_blank'>CCNA (200-301) Certification Practice Exam</a><br>
<hr>
<h3><center>Kompiuterių tinklai 2 dalis<center></h3>
<br>
<h5>Packet Tracer</h5>
<?php
    $files = scandir('./cisco2/');
    foreach($files as $file) {
        if($file !== "." && $file !== "..") {
            echo "<a href='/cisco2/$file' download target='_blank' type='application/octet-stream'>$file</a><br>";
        }
    }
?>
<br>
<h5>Netacad.com</h5>
<a href='https://itexamanswers.net/ccna-2-v7-modules-1-4-switching-concepts-vlans-and-intervlan-routing-exam-answers.html' target='_blank'>Modules 1 – 4: Switching Concepts, VLANs, and InterVLAN Routing Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-v7-modules-5-6-redundant-networks-exam-answers.html' target='_blank'>Modules 5 – 6: Redundant Networks Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-v7-modules-7-9-available-and-reliable-networks-exam-answers.html' target='_blank'>Modules 7 – 9: Available and Reliable Networks Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-v7-modules-10-13-l2-security-and-wlans-exam-answers.html' target='_blank'>Modules 10 – 13: L2 Security and WLANs Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-v7-modules-14-16-routing-concepts-and-configuration-exam-answers.html' target='_blank'>Modules 14 – 16: Routing Concepts and Configuration Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-srwe-practice-pt-skills-assessment-ptsa-part-1-answers.html' target='_blank'>SRWE Practice PT Skills Assessment (PTSA) – Part 1</a><br>
<a href='https://itexamanswers.net/ccna-2-srwe-practice-pt-skills-assessment-ptsa-part-2-answers.html' target='_blank'>SRWE Practice PT Skills Assessment (PTSA) – Part 2</a><br>
<a href='https://itexamanswers.net/hands-on-skills-exam-ccnav7-srwe-skills-assessment-answers.html' target='_blank'>SRWE Skills Assessment</a><br>
<a href='https://itexamanswers.net/switching-routing-and-wireless-essentials-v7-0-srwev7-practice-final-exam-answers.html' target='_blank'>SRWEv7 Practice Final Exam</a><br>
<a href='https://itexamanswers.net/ccna-2-v7-0-final-exam-answers-full-switching-routing-and-wireless-essentials.html' target='_blank'>CCNA 2 v7.0 Final Exam</a><br>
<hr>
<h3><center>Kompiuterių tinklai 1 dalis<center></h3>
<br>
<h5>Packet Tracer</h5>
<?php
    $files = scandir('./cisco/');
    foreach($files as $file) {
        if($file !== "." && $file !== "..") {
            echo "<a href='/cisco/$file' download target='_blank' type='application/octet-stream'>$file</a><br>";
        }
    }
?>
<br>
<h5>Netacad.com</h5>
<a href='https://www.itciscov7.com/2020/02/introduction-to-networks-version-7.html' target='_blank'>Modules 1 - 3: Basic Network Connectivity and Communications Exam</a><br>
<a href='https://www.itciscov7.com/2020/02/ccna-1-v7-modules-4-7-ethernet-concepts.html' target='_blank'>Modules 4 - 7: Ethernet Concepts Exam</a><br>
<a href='https://www.itciscov7.com/2020/02/ccna-1-v7-modules-8-10-communicating.html' target='_blank'>Modules 8 - 10: Communicating Between Networks Exam</a><br>
<a href='https://www.itciscov7.com/2020/02/ccna-1-v7-modules-11-13-ip-addressing.html' target='_blank'>Modules 11 – 13: IP Addressing Exam</a><br>
<a href='https://www.itciscov7.com/2020/02/ccna-1-v7-modules-14-15-network.html' target='_blank'>Modules 14 – 15: Network Application Communications Exam</a><br>
<a href='https://www.itciscov7.com/2020/02/ccna-1-v7-modules-16-17-building-and.html' target='_blank'>Modules 16 – 17: Building and Securing a Small Network Exam</a><br>
<a href='https://itexamanswers.net/ccna-1-v7-0-final-exam-answers-full-introduction-to-networks.html' target='_blank'>CCNA 1 v7.0 Final Exam Answers</a><br><hr>
<h3><center>Duomenų saugumas ir kriptografija<center></h3>
<br>
<?php
    $files = scandir('./kripto/');
    foreach($files as $file) {
        if($file !== "." && $file !== "..") {
            echo "<a href='/kripto/$file' download target='_blank' type='application/octet-stream'>$file</a><br>";
        }
    }
	
?>
<hr>
<h3><center>Elektronika<center></h3>
<br>

<?php
    $files = scandir('./elektra/');
    foreach($files as $file) {
        if($file !== "." && $file !== "..") {
            echo "<a href='/elektra/$file' download target='_blank' type='application/octet-stream'>$file</a><br>";
        }
    }
	
?>

<hr>
<br>
	  </div>
	  </div>

<script type="text/javascript">

    $(document).ready(function(){
			
        $("#ieskoti").click(function(){
			if(document.getElementById("paskaitos").value > 0)
			{
            $.ajax({
                type: 'POST',
                url: '2script.php',
				data: { paieska: $("#paieska").val(),
						skaicius: $("#paskaitos").val()},
				dataType : 'html',
				timeout: 500, 
                success: function(data) {
						$("#content").html(data);	
						document.getElementById("content").innerHTML = data;
                },
				error: function(xhr, status, error) {
				var err = eval("(" + xhr.responseText + ")");
				alert(err.Message);
				}	
            });
			} else
			{
				document.getElementById("content").innerHTML = "<center><br><h3>Privaloma pasirinkti kryptį!</h3></center><br>";
			}
   });
   
   
   $("#ieskoti_nuotraukos").click(function(){
	   
        var file_data = $('#file1').prop('files')[0];
        var form_data = new FormData();  // Create a FormData object
        form_data.append('file', file_data);  // Append all element in FormData  object

        $.ajax({
                url         : 'upload.php',     // point to server-side PHP script 
                dataType    : 'text',           // what to expect back from the PHP script, if anything
                cache       : false,
                contentType : false,
                processData : false,
                data        : form_data,                         
                type        : 'post',
                success     : function(output){
					
				$.ajax({
                type: 'POST',
                url: '3script.php',
				data: {paieska_nuotraukos: output},
				dataType : 'html',
                success: function(data) {
				$("#content").html(data);	
						document.getElementById("content").innerHTML = data;
                }
				
            });
					
                }
         });
	   
   });
   
   
   $("#donatelist").click(function(){
	   ///var visidonate = '<?php echo json_encode($members, JSON_FORCE_OBJECT)?>';
	   ///var visidonate2 = JSON.parse(visidonate);
	   var donatesarasas = document.getElementById("donate_sarasas").innerHTML;
				document.getElementById("content").innerHTML = '<br><center><h3>Dosnumo sąrašas</h3><br><br>'+ donatesarasas +'</center>';
   });
   
   
   $("#pridetisource").click(function(){
				var sarasas = document.getElementById("paskaitu_sarasas").innerHTML;
				document.getElementById("content").innerHTML = "<br><center><h3>SOURCE kodo pridėjimas</h3><br>" + sarasas + "<br><div class='form-group'></center>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Įkopijuokite SOURCE kodą čia:<center><textarea id='source_kodas' name='source_kodas' style='width:90%' class='form-control' rows='6' id='comment'></textarea></div><br><button type='button' name='pridetisource_ifaila' id='pridetisource_ifaila' class='btn btn-success'>Pridėti klausimus į duomenų bazę</button><br></center><br><div id='content_ikelimas'></div><br>";
				///console.log('gay1');
	
	
	$("#pridetisource_ifaila").click(function(){
				var pridedamas_source = document.getElementById("source_kodas").value; //////////TEKSTAS
				pridedamas_source_list_id = value - 10;
				var failas = pridedamas_source_list_id + ".txt"; /////////// failo pavadinimas

				$.ajax({
                type: 'POST',
                url: '2script_ikelimas.php',
				data: { tekstas: $("#source_kodas").val(),
						pavadinimas: failas},
				dataType : 'html',
				timeout: 500, 
                success: function(data) {
						$("#content_ikelimas").html(data);	
						document.getElementById("content_ikelimas").innerHTML = data;
                },
				error: function(xhr, status, error) {
				var err = eval("(" + xhr.responseText + ")");
				alert(err.Message);
				}	
            });
				
				
				/// REIKIA JOG PERDUOTU 'failas' ir 'pridedamas_source' i php value ir tada pridet i failas
				/////
				///
				///
				//
				///
				
   });
   });
   
   $("#pridetiklausima").click(function(){
				document.getElementById("content").innerHTML = "<br><center><h3>Klausimo iš nuotraukos pridėjimas</h3><br><br><form method='post' enctype='multipart/form-data' >Pridėti klausimo nuotrauką (be atsakymų): <input style='height:40px !important;' type='file' name='image' /><br><br><input type='text' class='form-control rounded' placeholder='Teisingas atsakymas' style='margin-left: 4px; height: 30px;width:600px;' name='atsakymas1' id='atsakymas1'><br><input type='text' class='form-control rounded' placeholder='Teisingas atsakymas (nebūtina)' style='margin-left: 4px; height: 30px;width:600px;' name='atsakymas2' id='atsakymas2'><br><input type='text' class='form-control rounded' placeholder='Teisingas atsakymas (nebūtina)' style='margin-left: 4px; height: 30px;width:600px;' name='atsakymas3' id='atsakymas3'><br><input type='text' class='form-control rounded' placeholder='Teisingas atsakymas (nebūtina)' style='margin-left: 4px; height: 30px;width:600px;' name='atsakymas4' id='atsakymas4'><br><input type='text' class='form-control rounded' placeholder='Teisingas atsakymas (nebūtina)' style='margin-left: 4px; height: 30px;width:600px;' name='atsakymas5' id='atsakymas5'><br /><br /><button type='submit' name='pridetiklausima_isnuotraukos' id='pridetiklausima_isnuotraukos' class='btn btn-success'>Pridėti klausimą į duomenų bazę</button></form><br></center><br><div id='klausimonuotraukos_ikelimas_atsakymas'></div><br>";

   });
   
   
});

</script>

</div>
</div>
</div>
</div>
</div>
		<?php
	
		
	}	//// VISO SAITO PABAIGA
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} else {
	$authUrl = $gClient->createAuthUrl();
	$output = '<a class="btn btn-dark btn-lg" href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'">
    <img src="https://img.icons8.com/color/16/000000/google-logo.png"> Sign in with Google</a>';
	?>
	<br><br><br><br><br><br><br><br>
<center>
    <h1 class="display-1">
    
        <span class="text-primary">O</span><span class="text-danger">r</span><span class="text-warning">l</span><span class="text-primary">o</span><span class="text-success">o</span><span class="text-warning">v</span>
    </h1>
	<h6>A secret organization whose activities, events, inner functioning, or membership are concealed from non-members.</h6>
<div><br><br><br><br><br><br><?php echo $output; ?></div>
</center>
	
	<?php
}
?>
