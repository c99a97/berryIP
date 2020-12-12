<?php
	require_once("berryIP_header.php");
?>
<?php
	if(isset($_SESSION[userID])){
		echo "<script>location.href='./berryIP_viewIP.php';</script>";
	}
?>
	<br>
	<form method="POST" action="berryIP_loginCheck.php">
	<table class=tbNoBorder align=center>
	<tr><th class=thGray>[ 로 그 인 ]</th></tr>
	<tr><td class=tdGrayC style="padding-top:3"><input type='text' name='userID' placeholder='ID' required></td></tr>
	<tr><td class=tdGrayC style="padding-top:3"><input type='password' name='userPW' placeholder='Password' required></td></tr>
	<tr><td class=tdC><input type='submit' class='butGray' style='width:50px;' value='확 인'></tr></td>
	</table>
	</form>
<?php
	require_once ("berryIP_footer.php");
?>
