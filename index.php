<?php
	require_once "berryIP_header.php";
?>
<?php
	if(isset($_SESSION[userID])){
		echo "<script>location.href='./berryIP_viewIP.php';</script>";
	}else{
		echo "<script>location.href='./berryIP_login.php';</script>";
	}
?>
<?php
	require_once "berryIP_footer.php";
?>
