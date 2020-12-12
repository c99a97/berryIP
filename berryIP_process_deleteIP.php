<!-- 삭제할건지 확인 -->	
<?php
	$db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
	if(!empty($_POST['check_list'])) {
		foreach($_POST['check_list'] as $check) {
			$sql = "DELETE FROM IP_INFO WHERE IP_ADDR = '{$check}'"; 
			$db_res=mysqli_query($db_conn,$sql);
			
			if($db_res === false){
			echo 'IP 삭제하는 과정에서 문제가 생겼습니다.';
			echo mysqli_error($db_conn);
			} 
			else {
			header('Location: berryIP_viewIP.php');
			}
		}
	}
?>
