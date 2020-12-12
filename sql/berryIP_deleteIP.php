<?php
	require_once "berryIP_header.php";
?>
<script>
	$(document).ready(function(){
		var tbl=$("#checkboxTable");

		$(":checkbox:first",tbl).click(function(){
			if($(this).is(":checked")){
				$(":checkbox",tbl).attr("checked","checked");
			}else{
				$(":checkbox",tbl).removeAttr("checked");
			}
			$(":checkbox",tbl).trigger("change");
		});

		$(":checkbox:not(:first)",tbl).click(function(){
			var allCnt=$(":checkbox:not(:first)",tbl).lenght;
			var checkedCnt=$(":checkbox:not(:first)",tbl).filter(":checked").length;

			if(allCnt=checkedCnt){
				$(":checkbox:first",tbl).attr("checked","checked");
			}else{
				$(":checkbox:first",tbl).removeAttr("checked");
			}
		}).change(fucntion(){
			if($(this).is("checked")){
				$(this).parent().parent().addClass("selected");
			}else{
				$(this).parent().parent().removeClass("selected");
			}
			});
	});
</script>
<style>
	#checkboxTable {border-collapse: collapse;}
</style>
<?php
	// Function
	function addSearch($pageNo){
		global $howMany, $searchHow, $searchWhat;
		$resultStr="berryIP_viewIP.php?page=".$pageNo;
		if($howMany!=NULL && $HowMany!=10){
			$resultStr=$resultStr."&list_no=".$howMany;
		}
		if($searchHow!=NULL && $searchWhat!=NULL){
			$resultStr=$resultStr."&method=".$searchHow."&search=".$searchWhat;
		}
		return $resultStr;
	}
	// 초기설정
	if(empty($_SESSION['userID'])){
		echo "<script>location.href='./berryIP_login.php';</script>";
	}

	$pageNo=$_GET['pageNo'];
	if($pageNo==NULL){
		$pageNo=1;
	}
	$howMany=$_GET['list_no'];
	if($howMany==NULL){
		$howMany=10;
	}else if($howMany!=10 && $howMany!=30 && $howMany!=50){
		$howMany=10;
	}
	$searchHow=$_GET['method'];
	$searchWhat=$_GET['search'];
	$searchStr=NULL;
	if($searchHow!=NULL && $searchWhat!=NULL){
		switch($searchHow){
		case how1:
			$searchStr="where IP_ADDR like \"%".$searchStr."%\"";
			break;
		case how2:
			$searchStr="where MAD_ADDR like \"%".$searchStr."%\"";
			break;
		case how3:
			$searchStr="where OWNER like \"%".$searchStr."%\"";
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
	<table id="checkboxTable" class=tbNoBorder align="center">
	<tr><td class=tdRight colspan=7>
		로그인 : <?php echo $_SESSION['userID']; ?>
		<button type="button" class=butWhite onclick="location.href='berryIP_logout.php'">로그아웃</button>
	</td></tr>
	<tr><td style="text-align:center" class=tdCenter colspan=7>
		<h1> IP 삭제모드 </h1>
	</td></tr>
	<tr><td class=tdRight colspan=7>
		<form method="GET" action="berryIP_viewIP.php">
			<u>출력 라인수 : </u>
			<select onchange="this.form.submit()" id='list_no' name='list_no'>
				<option value="10">10개</option>
				<option value="30" <?php if($howMany==30) echo "selected" ?>>30개</option>
				<option value="50" <?php if($howMany==50) echo "selected" ?>>50개</option>
			</select>
		</form>
	</td></tr>
	<tr>
		<th class=thGray width="50"><input type="checkbox" id="checkAll"/></th>
		<th class=thGray width="200">IP 주소</th>
		<th class=thGray width="200">MAC 주소</th>
		<th class=thGray width="150">소유자</th>
		<th class=thGray width="100">권한</th>
		<th class=thGray width="100">보호</th>
		<th class=thGray width="300">메모</th>
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
	echo "<form action='berryIP_process_deleteIP.php' method='post'>"
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
?>
	<input type="submit" />
	</form>
	</table>
	<br>
	<div style="text-align:center">
<?php
	$pgMax=ceil($row_max/$howMany);
	$pgLimit=floor(($pgMax-1)/10);
	$pgNow=floor(($pageNo-1)/10);
	if($pgNow!=0){
		echo "<button type='button' onclick=\"location.href'".addSearch(1)."'\">처음</button> ";
		echo "<button type='button' onclick=\"location.href'".addSearch($pgNow*10-9)."'\">이전</button> ";
	}
	for($n=$pgNow*10+1; ($n<=$pgNow*10+10)&&($n<=$pgMax); $n++){
		echo "<a href=".addSearch($n).">".$n." </a>";
	}
	if($pgNow!=$pgLimit){
		echo "<button type='button' onclick=\"location.href'".addSearch($pgNow*10+11)."'\">다음</button> ";
		echo "<button type='button' onclick=\"location.href'".addSearch($pgMax)."'\">끝</button> ";
	}	
?>
	<form method="GET" action="berryIP_viewIP.php">
		<select name="method" size="0">
			<option value="how1">IP주소</option>
			<option value="how2">MAC주소</option>
			<option value="how3">소유자</option>
		</select>
		<input type="test" name=search>
<?php
		if($howMany!=10){
			echo "<input type='hidden' name=list_no value=$howMany>";
		}
?>
		<input type="submit" class="button" value="검색">
	</form>
	</div>
<?php
	mysqli_close($db_conn);
	require_once "berryIP_footer.php";
?>
