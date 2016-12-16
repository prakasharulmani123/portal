<?php
class AlertsShell extends Shell {
 
  function main() {
    ClassRegistry::init('Comment')->reminders();
  }
 
}
?>