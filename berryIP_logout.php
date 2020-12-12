<?php
	require_once "berryIP_header.php";
?>
<?php
	session_destroy();
	echo "<script>location.href='./berryIP_login.php';</script>";
?>
<?php
	require_once "berryIP_footer.php";
?>
