<head>

<title>Isilon Platform API basic demo</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="/js/fp_js.js"></script>
</head>
<body>
<h1>Isilon Platform API basic demo</h1>
</p>
<p>
This is a simple demo of the <a href="http://www.emc.com/storage/isilon/isilon.htm" target='_blank'>EMC Isilon storage system</a> Platform API's functionality.

The command being issued (on the end of this URL) is listed under Commands and the result given underneath.
note that this result has been passed to get a particular value.

Note credentials are not stored in anyway, but not protected, test accounts should be used.  (ideally with audit RBAC capabilities only).  <br><br><b>If you would like to query your own live/test/virtual Isilon cluster you will also have to accept traffic over port <i>443</i> from
<i>www.papidemo.info</i> or <i>87.81.222.89</i> and type in your systems Internet facing IP address below.</b>  You may also leave the filled in credentials to query a virtual cluster already configured on my server.
<br>
For a brief description of what is going on<a href="description.html"> see here</a>.
</p>
<p>
This demo is written with information from: <a href="./docu50224_OneFS-7.1.0-Platform-API-Reference.pdf">This document</a>.  The sourcecode looks like this: <a href="./source_code.php">source code</a> written by <a href='mailto:adam.weeks@emc.com?subject=PAPI demo'>Adam Weeks</a>.<br>
To download the demo, or the source code<a href="/downloads/downloads.html"> download them here</a>.
<br><small>This demo is in no way officially supported by EMC or any other organisation and is only designed as a simple example of what PAPI can do.</small><br><br>
</p>
<p>
<?php

//display quick summary of cluster:

$cluster=$_POST['cluster'];
$user=$_POST['user'];
$password=$_POST['password'];

if(!$cluster){$cluster='192.168.2.151';}
if(!$user){$user='papi_test';}
if(!$password){$password='password';}

?>

<form action='index.php' name='configform' id='configform' name='falsey' class="js-ajax-php-json" method='post'>
Cluster IP:<input type='text' name='cluster' id='cluster' value='<?=  $cluster ?>'>(you may change to your cluster's internet IP address & user credentials, this is a virtual cluster)<br>
Username:<input type='text' name='user' id='user' value='<?= $user ?>'><br>
Password: <input type='password' name='password' id='password' value='<?= $password ?>'><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<select id='cmd_type'>
	<option value="count">Count commands</option>
	<option value="user">User info</option>
	<option value="jobs">Jobs</option>
</select>
<input type='button' name='button' value='Run' id='button'></input>
</form>
<strong>RESULTS WILL NOT SHOW UNTIL QUERY IS RUN (by clicking 'run' button).</strong>
<h2>Results:</h2>
<table id='commands' name='commands' border=1 cellspacing=2>
<tr><th>Description</th><th>Command</th><th>Result</th><th>Details</th></tr>
</table>
<h2>Details:</h2>
<iframe width='600' id='detail' name='detail'  border=0></iframe>
</body>
</html>