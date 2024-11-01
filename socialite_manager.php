<link href="../wp-content/plugins/socialite/style.css" rel="stylesheet" type="text/css" />

<!--[if IE 6]>
<link href="../wp-content/plugins/socialite/style-ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->


<div class="wrap">
<h2 id="sl_header">Socialite Configuration</h2>

<?php
if(version_compare(PHP_VERSION, '5.0.0', '<')) :
	include(dirname(__FILE__)."/php_version_error.php");
else :
	include(dirname(__FILE__)."/socialite_interface.php");
endif;
?>

</div>