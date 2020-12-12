<?php
	require_once "berryIP_header.php";
?>
<h1 align='center'> IP 수정 </h1>
<br></br>
<div style="text-align:center">
<?php
	$db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
	if(!empty($_POST['check_list'])) {
		$div = 0;  // 테이블 구분해주는 변수, input name 용
		foreach($_POST['check_list'] as $check) {
			$sql = "SELECT * FROM IP_INFO WHERE IP_ADDR = '{$check}'"; 
			$db_res = mysqli_query($db_conn,$sql);
			while($db_row=mysqli_fetch_row($db_res)){
				echo "<form name='applyupdate' action='berryIP_process_updateIP.php' method='post'>";
				echo "<table class=tbNoBorder align='center'>";
				echo "<tr>
						<th class=thGray> 컬럼명 </th>
						<th class=thGray> 값 </th>
					  </tr>";
				for ($n=0;$n<6;$n++){
					echo "<tr><td class=tdGrayC>";
					switch ($n) {
						case 0: echo "IP주소";
							$name = "IP_ADDR";
							break;
						case 1: echo "MAC주소";
								$name = "MAC_ADDR";
									break;
						case 2: echo "소유자";
								$name = "OWNER"   ;
								   break;
						case 3: echo "승인";
								$name = "IS_OK" ;
								   break;		   
						case 4: echo "보호";
								$name = "IS_PROTECTED"  ; 
								   break;
						case 5: echo "메모";
								$name = "IP_REMARK" ;  
								   break;
						default: print "";
								   break;
						echo "</td>";
					}
					echo "<td class=tdGrayC>";
					if($n!=3 && $n!=4){
						echo "<input type='text' name=".$name.$div." value='$db_row[$n]'></input>";
					}else{
						echo "<input type='radio' id=a".$name.$div." name=".$name.$div." value='0'";
						if($db_row[$n]==0) echo "checked='checked'";
						echo "><label for=a".$name.$div.">무</label></input>";
						echo "<input type='radio' id=b".$name.$div." name=".$name.$div." value='1'";
						if($db_row[$n]==1 or $db_row[$n]==null) echo "checked='checked'";
						echo "><label for=b".$name.$div.">유</laebl></input>";
					}
					echo "</td></tr>";
					echo "<input type=hidden name=Origin_IP_ADDR".$div." value='$db_row[0]'>";
					echo "<input type=hidden name=div value='$div'>";
				}
				$div = $div + 1;
				echo "</table> <input type='submit' class='button' value='수정' align='center' ></form><br></br>";
			}
		}
	}
	echo "<button onclick=\"location.href='./berryIP_viewIP.php'\">뒤로 돌아가기</button>";
?>
</div>
<?php
	mysqli_close($db_conn);
	require_once "berryIP_footer.php";
?>
