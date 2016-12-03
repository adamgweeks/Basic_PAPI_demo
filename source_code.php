

<?php

//endswith function (to check for php files later!)_
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


//read in source code files and display them...
$dir    = './';
$files = preg_grep('/^([^.])/', scandir($dir));//avoid hidden files and dirs

echo "<a href='index.php'>Back to main page</a>";

echo "<h1>Files:</h1>";
//print_r($files);exit;
foreach($files as $file){
if(endsWith($file,'.php')){
echo "<a href='#{$file}'>$file</a><br><br>";
}}

echo "<br>";
echo "<a href=\"./commands.pdf\">See database structure. (not used in web version)</a>";


foreach($files as $file){
if(endsWith($file,'.php')){
echo "<hr>";
//read file
echo "<a name=\"{$file}\"></a>";
echo "<h2>File: <i>$file</i></h2>";

$sourcefile = fopen($file, "r") or die("Unable to open file!");
//format for display and print!
echo "<pre>";
echo highlight_string(fread($sourcefile,filesize("$file")));
fclose($sourcefile);
echo "</pre>";
}}

echo "<a href='index.php'>Back to main page</a>";

?>
