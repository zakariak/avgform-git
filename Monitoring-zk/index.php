<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
<?php
require 'plugin/inc/init.php';

function telTotHonderd() {

  for($i = 0; $i < 100; $i++ ) {
    if( $i > 50 ) {
      throw new exception('Error Occured', 69);
    }
  }
}
telTotHonderd();



// try {
//   telTotHonderd();
// } catch( Exception $ex) {
//       throw new Exception($plugin->log($ex->getCode(), $ex->getMessage(), $ex->getTraceAsString()));
// }
?>
</body>
</html>
