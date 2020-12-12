<?php
	require_once "berryIP_header.php";
?>
<!-- 로그인 확인 페이지-->
<?php
	if(isset($_SESSION[userID])){
		echo "<script>location.href='./berryIP_viewIP.php';</script>";
	}else if(empty($_POST[userID])){
		echo "<script>location.href='./berryIP_login.php';</script>";
	}

	$db_conn = mysqli_connect("localhost","cswin","cswin","berryIP");
	$userID = mysqli_real_escape_string($db_conn,$_POST[userID]);
	$userPW = mysqli_real_escape_string($db_conn,$_POST[userPW]);
	$fname = "./log/login_admin/login_".date("ymd").".log";
	$fd = fopen($fname,"a+");
	$time_now = date("Y-m-d H:i:s");
	$fstr = $time_now." # ".$userID." # ";
	$db_res = mysqli_query($db_conn,"select LATEST_LOCK_DATE from USER_INFO where USER_ID='$userID'");
	$db_row = mysqli_fetch_row($db_res);
	if($db_row[0]!=NULL){
		$time_diff = strtotime($time_now)-strtotime($db_row[0]);
		if($time_diff < 300){
			$time_diff = 300-$time_diff;
			$fstr = $fstr."Locked ID\n";
			fwrite($fd,$fstr);
			echo "<script>alert('잠긴 계정입니다. $time_diff 초 남았습니다.')</script>";
			echo "<script>location.href='./berryIP_login.php';</script>";
			exit();
		}
	}

	$db_res = mysqli_query($db_conn,"select USER_ID from USER_INFO where USER_ID='$userID' and USER_PW=password('$userPW')");
	$db_row = mysqli_fetch_row($db_res);
	if(isset($db_row[0])){
		$_SESSION[userID]=$userID;
		$fstr = $fstr."Login Success";
		$db_res = mysqli_query($db_conn,"update USER_INFO set LOGIN_FAIL_COUNT=0, IS_LOCK=0, LATEST_LOCK_DATE=NULL where USER_ID='$userID'");
	}else{
		$fstr = $fstr."Login Failed";
		# 계정은 존재하는데 비밀번호를 틀린 건가?
		$db_res = mysqli_query($db_conn,"select USER_ID,LOGIN_FAIL_COUNT from USER_INFO where USER_ID='$userID'");
		$db_row = mysqli_fetch_row($db_res);
		if(isset($db_row[0])){
			if($db_row[1]==4){
				$db_res = mysqli_query($db_conn,"update USER_INFO set LOGIN_FAIL_COUNT=0, IS_LOCK=1, LATEST_LOCK_DATE=now() where USER_ID='$userID'");
				$fstr = $fstr." LOCK";
			}else{
				$db_res = mysqli_query($db_conn,"update USER_INFO set LOGIN_FAIL_COUNT=LOGIN_FAIL_COUNT+1 where USER_ID='$userID'");
			}
		}
	}
	$db_row = mysqli_fetch_row($db_res);

	$fstr = $fstr."\n";
	fwrite($fd,$fstr);
	fclose($fd);
	mysqli_close($db_conn);
	if(isset($_SESSION[userID])){
		echo "<script>location.href='./berryIP_viewIP.php';</script>";
	}else{
		echo "<script>alert('아이디나 비밀번호가 틀렸습니다.');</script>";
		echo "<script>location.href='./berryIP_login.php';</script>";
	}
?>
<?php
	require_once "berryIP_footer.php";
?>
