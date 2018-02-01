<?php
 
$file = "ip.txt";
 
if(isset($_GET['newip']) == true && isset($_GET['pass']) == true)
{
   $ip = $_GET['newip'];
   $pass = $_GET['pass'];
 
   if($pass == "123456")
   {
      // update IP address
      file_put_contents($file, $ip);
   }
   else
   {
      echo "wrong password";
   }
}
else if(isset($_GET['pass']) == true)
{
   $pass = $_GET['pass'];
 
   if($pass == "123456")
   {
      // get IP address
      $ip = file_get_contents($file);
      echo '<html>';
      echo '<head>';
      echo '<meta http-equiv="refresh" content="0; url=http://'.$ip.':8080" />';
      echo '</head>';
      echo '<body>';
      echo '</html>';
   }
   else
   {
      echo "wrong password";
   }
}
 
?>
