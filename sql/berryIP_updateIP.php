

<table id="updateTable" class=tbNoBorder align="center">
<tr>
	<th class=thGray> 컬럼명 </th>
	<th class=thGray> 값 </th>
</tr>
<?php
	$db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
	if(!empty($_POST['check_list'])) {
		foreach($_POST['check_list'] as $check) {
			$sql = "SELECT * FROM IP_INFO WHERE IP_ADDR = '{$check}'"; 
			$db_res=mysqli_query($db_conn,$sql);
			while($db_row=mysqli_fetch_row($db_res)){
				for ($n=0;$n<6;$n++){
					echo "<tr><td class=tdGrayC>";
					switch ($n) {
						case 0: echo "IP주소";
								   break;
						case 1: echo "MAC주소";
								   break;
						case 2: echo "소유자";
								   break;
						case 3: echo "권한";
								   break;		   
						case 4: echo "보호";
								   break;
						case 5: echo "메모";
								   break;
						default: print "";
								   break;
						echo "</td>"
					}
					echo "<td class=tdGrayC>$db_row[$n]</td></tr></table>";
					if($db_res === false){
					echo 'IP 삭제하는 과정에서 문제가 생겼습니다.';
					echo mysqli_error($db_conn);
					} 
					else {
					header('Location: berryIP_deleteIP.php');
					}
				}
			}
		}
	}
?>


