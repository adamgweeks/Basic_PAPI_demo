<?php

//run a PAPI command

$cluster=$_GET['cluster']; //get cluser url/IP address
$isilon_url="https://{$cluster}:8080";
$username = $_GET['user'];//get user credentials (to log into array)
$password = $_GET['password'];
$type = $_GET['type']; //get the command type (category of commands)

//open db connection to mysql (note simple login credentials):
$con = mysql_connect('localhost', 'papi', 'password');

// Check connection
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
// make PAPI_db the current db
$db_selected = mysql_select_db('PAPI_db', $con);
if (!$db_selected) {
    die ('Can\'t open database : ' . mysql_error());
}



//run sql command to get command list
//$sql = "SELECT * FROM commands WHERE category='{$type}'";//find all commands in DB of this type. 

//original SQL above, using md5 hashing to avoid SQL code injection only (functionally no different).

$hashedval=md5(trim($type));
$sql = "SELECT * FROM commands WHERE md5(category)='{$hashedval}'";//find all commands in DB of this type. 

$result = mysql_query($sql) or die(mysql_error());

 $nullc=0;//create a 'null result' counter variable set at 0

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { //go through each command from the database.

 //get ready for http login:
$context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
    ),
    "ssl"=>array(
        "allow_self_signed"=>true,
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
));

$service_url= $isilon_url . $row['command']; //place the isilon url and then the desired command into the file_get command
//echo "service URL:{$service_url}";exit;

if($cluster){//if we have a address/name for the isilon cluster, let's get the info
//The most important 2 lines of code! 
$encodedcontents = @file_get_contents($service_url,false,$context); //go and get the 'file' from the isilon array
//var_dump($encodedcontents);exit;//used for debugging (to see the absolute raw output from the Isilon cluster).
$decodedcontents = @json_decode($encodedcontents,true);//decode the json response into a PHP array. (so that we can process it)
}
else//if we didn't get an address/url for the cluster let's get the simulated response from the db instead
{
$encodedcontents = $row['sim_response']; //go and get the simulated response from the db
$decodedcontents = @json_decode($encodedcontents,true);//decode the json response into a PHP array. (so that we can process it)
$service_url="https://sim_cluster:8080" . $row['command'];
}

//use different parts of JSON array depending on the command (structure differs between commands)
if($row['array_part']=='total'){
$inner=$decodedcontents['total'];//get the count response from the variable
if(!$inner && ($inner!==0)){$nullc++;}//if we get a null result (we get nothing back) increase the null counter
}
elseif($row['array_part']=='summary.count'){
$inner=$decodedcontents['summary']['count'];//get the count response from the variable
if(!$inner && ($inner!==0)){$nullc++;}//if we get a null result (we get nothing back) increase the null counter
}
elseif($row['array_part']=='privilege'){
$tinner=$decodedcontents['ntoken']['privilege'];//get the count response from the variable
foreach($tinner as $priv){$apriv=$priv['id'];$inner= $inner . "$apriv<br>";}
//var_dump($inner);
if(!$inner && ($inner!==0)){$nullc++;}//if we get a null result (we get nothing back) increase the null counter
}
elseif($row['array_part']=='types'){
$tinner=$decodedcontents['types'];//get the count response from the variable
foreach($tinner as $job){$inner= $inner . "<b>job=</b>{$job['id']} - <b>ex_set=</b>{$job['exclusion_set']} <b>pri=</b>{$job['priority']} <b>policy=</b>{$job['policy']}<br>";}
}
else{$inner=null;$nullc++;}

$output[]=array('desc'=> $row['description'],'command'=>$row['command'],'out'=>$inner,'fullcmd' => $service_url); //place the output into a different array (that we will JSON encode for Jquery).

}

if($nullc>=6){$output[]=array('desc'=> $row['description'],'command'=>$row['command'],'out'=>'ERROR! CHECK SETTINGS!');}//if we have had 6 or more errors, place an error message into last response.

//print_r ($output);exit;//used for debugging, to see the raw array that would be sent back to the application

echo(json_encode($output));  //JSON encode the response, which is passed back into the Jquery AJAX function.

//mysqli_close($conn);
?>