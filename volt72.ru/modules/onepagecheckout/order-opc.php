<?php
  require(dirname(__FILE__).'/../../config/config.inc.php');
  require("OrderOpcktController.php");
  $opckt_controller = new OrderOpcktController();
  $opckt_controller->run();
?>
