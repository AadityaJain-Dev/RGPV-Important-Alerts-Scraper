<?php
$executionStartTime = microtime(true);

$file = 'rgpv.html';

// Open the file to get existing content
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
$html=file_get_contents("https://www.rgpv.ac.in/",false,$context);

// Write the contents back to the file
file_put_contents($file, $html);


// setting the timezone to India
date_default_timezone_set('Asia/Kolkata');

//current date and time
$datetime = date("d-m-Y - h:i:sa");

//for dom
include("dom.php");

//for dom
include("db.php");

//file news page local copy
$html=file_get_html("rgpv.html");
$rgpvkiurl="NULL";

// for each news
foreach($html->find(".tab-content div div") as $news){
	//get inner text (it dosent include container)
	$text=$news->innertext;

// extracting url from news (if any)
    if(preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $text, $result)){
    if (!empty($result)) {
        $rgpvkiurl=$result['href'][0];
        if (filter_var($rgpvkiurl, FILTER_VALIDATE_URL)) {
        }else{
          $rgpvkiurl = str_replace('\\', '/', $rgpvkiurl);
          $rgpvkiurl="https://www.rgpv.ac.in$rgpvkiurl";
        }}}
// strip all html tags
$text=strip_tags("$text","");
//removing some unwanted sting
$text=str_replace("&nbsp;Click Here to View&nbsp;","","$text");
$text=str_replace("&nbsp;","","$text");
//For preventing SQL errors
$text=htmlspecialchars($text, ENT_QUOTES);
// TODO: Find a better way to do this
$sql = "INSERT INTO alerts (alert_text, alert_url, date) VALUES ('$text', '$rgpvkiurl', '$datetime')";

if ($conn->query($sql) === TRUE) {
    echo "Added - $text $rgpvkiurl";
  } else {
  echo "Error: " . $conn->error;
  }
  echo "<br>\n";
  }

//close the connection
$conn->close();

$executionEndTime = microtime(true);
$seconds = $executionEndTime - $executionStartTime;

//Print time taken
echo "<script>console.log( 'The script took $seconds to execute.' );</script>";
?>
