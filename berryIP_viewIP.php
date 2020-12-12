<?php
	require_once "berryIP_header.php";
?>
<script src="/jQuery/jquery-3.4.1.min.js"></script>
<script type="text/javascript" >
	$("#checkAll").click(function(){
		if($("#checkAll").prop("checked")){
			$("input[name=chk]").prop("checked",true);
		}else{
			$("input[name=chk]").prop("checked",false);
		}
	});

	$(".chk").click(function(){
		if($("input[name='chk']:checked").length==8){
			$("#checkAll").prop("checked",true);
		}else{
			$("#checkAll").prop("checked",false);
		}
	});
</script>
<?php
	function addSearch($pageNo){
		global $howMany, $searchHow, $searchWhat;
		$resultStr="berryIP_viewIP.php?page=".$pageNo;
		if($searchHow!=NULL && $searchWhat!=NULL){
			$resultStr=$resultStr."&method=".$searchHow."&search=".$searchWhat;
		}
		return $resultStr;
	}

	if(empty($_SESSION['userID'])){
		echo "<script>location.href='./berryIP_login.php';</script>";
	}

	$pageNo=$_GET['pageNo'];
	if($pageNo==NULL){
		$pageNo=1;
	}
	$howMany=20;
	$searchHow=$_GET['method'];
	$searchWhat=$_GET['search'];
	$searchStr=NULL;
	if($searchHow!=NULL && $searchWhat!=NULL){
		switch($searchHow){
		case how_ip:
			$searchStr="where IP_ADDR like \"%".$searchWhat."%\"";
			break;
		case how_mac:
			$searchStr="where MAC_ADDR like \"%".$searchWhat."%\"";
			break;
		case how_owner:
			$searchStr="where OWNER like \"%".$searchWhat."%\"";
			break;
		case how_remark:
			$searchStr="where IP_REMARK like \"%".$searchWhat."%\"";
			break;
		default:
			$searchHow=NULL;
			$searchWhat=NULL;
		}
	}else{
		$searchHow=NULL;
		$searchWhat=NULL;
	}
?>
	<form name='ipform' method='post'>
	<table id="checkboxTable" class=tbNoBorder align="center">
	<tr><td class=tdRight colspan=7 style=padding-bottom:5px;">
		로그인 : <?php echo $_SESSION['userID']; ?>
		<button type="button" class=butWhite onclick="location.href='berryIP_logout.php'">로그아웃</button>
	</td></tr>
	<tr><td class=tdRight colspan=7 style="padding-bottom:2px;">
		<button type="button" class=butGray onclick="location.href='berryIP_log.php'">로그</button>
		<button type="button" class=butGray onclick="location.href='berryIP_viewUser.php'">사용자</button>
	</td></tr>
	<tr><td class=tdRight colspan=7 style="padding-bottom:2px;">
		<button type="button" class=butGray onclick="location.href='berryIP_addIP.php'">IP 추가</button>
		<input type="submit" value ="선택 IP 삭제" class=butGray onclick=func_update() formaction="berryIP_process_deleteIP.php" ></input>
		<input type="submit" value ="선택 IP 수정" class=butGray onclick=func_update() formaction="berryIP_updateIP2.php"></input>
	</td></tr>
	<tr>
		<th class=thGray width="50"><input type="checkbox" class="chk" id="checkAll"/></th>
		<th class=thGray width="200">IP 주소</th>
		<th class=thGray width="200">MAC 주소</th>
		<th class=thGray width="150">소유자</th>
		<th class=thGray width="100">승인</th>
		<th class=thGray width="100">보호</th>
		<th class=thGray width="300">비고</th>
	</tr>
<?php
	$db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
	$db_res=mysqli_query($db_conn,"select count(*) from IP_INFO $searchStr");
	$db_row=mysqli_fetch_row($db_res);
	$row_max=$db_row[0];
	$pgFrom=($pageNo-1)*$howMany;
	if($pgFrom+$howMany>$row_max){
		$pgTo=$row_max%$howMany;
	}else{
		$pgTo=$howMany;
	}
	$db_res=mysqli_query($db_conn,"select * from IP_INFO $searchStr order by IP_ADDR ASC LIMIT $pgFrom, $pgTo");

	while($db_row=mysqli_fetch_row($db_res)){
		echo "<tr>";
		echo "<td class=tdGrayC><input type='checkbox' name='check_list[]' value = $db_row[0] id='chk$n'/></td>";
		for($n=0; $n<6; $n++){
			echo "<td class=tdGrayC>";
			if($n==3 || $n==4){
				if($db_row[$n]=='0'){
					echo "-";
				}else{
					echo "O";
				}
			}else{
				echo $db_row[$n];
			}
			echo "</td>";
		}
		echo "</tr>";
	}
	mysqli_close($db_conn);
?>
	</table>
	<script>
	function func_update(){
		var update_confirm = confirm("선택항목들을 수정 혹은 삭제하시겠습니까?");
		if(update_confirm == true){
			document.ipform.submit();	
		}
	}
	</script>
	</form>
	<br>
	<div style="text-align:center">
<?php
	$pgMax=ceil($row_max/$howMany);
	$pgLimit=floor(($pgMax-1)/10);
	$pgNow=floor(($pageNo-1)/10);
	if($pgNow!=0){
		echo "<button type='button' class=butGray onclick=\"location.href='".addSearch(1)."'\">처음</button> ";
		echo "<button type='button' class=butGray onclick=\"location.href='".addSearch($pgNow*10-9)."'\">이전</button> ";
	}
	for($n=$pgNow*10+1; ($n<=$pgNow*10+10)&&($n<=$pgMax); $n++){
		echo "<a href=".addSearch($n).">".$n." </a>";
	}
	if($pgNow!=$pgLimit){
		echo "<button type='button' class=butGray onclick=\"location.href='".addSearch($pgNow*10+11)."'\">다음</button> ";
		echo "<button type='button' class=butGray onclick=\"location.href='".addSearch($pgMax)."'\">끝</button> ";
	}	
?>
	<form method="GET" action="berryIP_viewIP.php">
		<select name="method" size="0">
			<option value="how_ip">IP주소</option>
			<option value="how_mac">MAC주소</option>
			<option value="how_owner">소유자</option>
			<option value="how_remark">비고</option>
		</select>
		<input type="test" name=search>
		<input type="submit" class="butGray" value="검색">
	</form>
	</div>
<?php
	require_once "berryIP_footer.php";
?>
