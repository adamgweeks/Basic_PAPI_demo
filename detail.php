<?php

$command=$_GET['command'];
$username = $_GET['user'];
$password = $_GET['password']; //take in the user credentials & the command used from the main page


if(strpos($command,'https://sim_cluster:8080/')!==0){ echo "<br><b><i>Responses NOT simulated</i></b><br><br>";
//get details for http authentication
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

$raw = @file_get_contents($command,false,$context); //get the 'file' details from this command url
$decoded = @json_decode($encodedcontents,true); //json decode the output (turn into a PHP array)

$hcommand=$command . '?describe&json';//format the url for getting help from the array
$help = @file_get_contents($hcommand,false,$context);//retrieve the 'file' from the help url

} else {//if we didn't get a response from the cluster, let's get the simulated response from the db instead
echo "<br><strong>All responses are simulated:</strong><br>";
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

$stripped_command=str_replace('https://sim_cluster:8080','',$command);//take out the fake url from the command, so that we can look it up in the db
//run sql command to get command list
//$sql = "SELECT sim_response,sim_help_response FROM commands WHERE command='{$stripped_command}'";//find all commands in DB of this type.

//original SQL above, using md5 hashing to avoid SQL code injection only (functionally no different).

$hashedval=md5($stripped_command);
 
$sql = "SELECT sim_response,sim_help_response FROM commands WHERE md5(command)='{$hashedval}'";
$result = mysql_query($sql) or die(mysql_error());

$raw=mysql_result($result,0,'sim_response');//get data from db lookup (sim response)
$help=mysql_result($result,0,'sim_help_response');//get data from db lookup (sim help response)

}

//create a nice table to show the output in the iframe
?>
<a href='#command'>Command</a> - <a href='#raw'>Raw response</a> - <a href='#help'>Help (raw) response</a>
<br><br>

<table id='details' name='details' border=0 cellspacing=4>

<tr><td><strong><a name='command'></a>Command:</strong></td></tr>
<tr><td><?= $command ?></td></tr>
<tr><td><strong><a name='raw'></a>Raw response:</strong></td></tr>
<tr><td><?= $raw ?></td></tr>
<tr><td><strong><a name='help'></a>Help (raw) response:</strong></td></tr>
<tr><td><?= $help ?></td></tr>
</table>