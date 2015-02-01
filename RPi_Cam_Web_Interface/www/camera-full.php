<!DOCTYPE html>
<?php
  define('BASE_DIR', dirname(__FILE__));
  require_once(BASE_DIR.'/config.php');
?>
<html>
  <head>
    <style>
       html, body {
         height: 100%;
         margin:0; 
         overflow: hidden;
       }
    </style>
    <title><?php echo CAM_STRING; ?></title>
    <script src="script.js"></script>
  </head>
  <body onload="setTimeout('init();', 100);">
      <img id="mjpeg_dest" width="100%" height="100%"/>
  </body>
</html>
