<?php
/* Required */
require('settings/core/config/constants.php');
require('settings/core/class/ServerQuery.php');	

// Define Class
$ServerQuery = new ServerQuery();

// Execute Build Page.
echo $ServerQuery->buildPage();

?>
<script>$(document).ready(function() {    
$('#servers').DataTable({
      paging: true,
      lengthChange: false,
      searching: true,
      ordering: true,
      info: false,
      autoWidth: false,
	  responsive: false,
	  scrollY: false,
	  scrollX: false,
	  iDisplayLength: 10,
    }); });</script>