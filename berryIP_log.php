<?php
	require_once "berryIP_header.php";
?>
	<button type="button" class=butGray onclick="location.href='berryIP_viewIP.php'">뒤로 돌아가기</button>
<?php
	file_viewer("berryIP 관련","./log/berryIP");
	file_viewer("관리자 관련","./log/login_admin");
	file_viewer("사용자 관련","./log/login_user");
?>
<!-- :: Function PART :: -->
<?php
	
	function file_viewer($dir_name,$dir)
	{
		$dir_handle=opendir($dir);
		$entrys=array();
		echo "<details>";
		echo "<summary><b><font size='5'>".$dir_name."</font></b></summary>";
		while(($filename=readdir($dir_handle))!=false)
		{
			if(is_dir($dir.'/'.$filename))
				continue;
			else
				$entries[]=$filename;
		}
		// entries 목록 정렬
		sort($entries);
		echo "<table>";
		foreach($entries as $entry){
			echo "<tr>";
			echo "<td width='150' style='padding-left:10px'><a href=$dir/$entry>"."$entry"."</a></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</details>";
		closedir($dir_handle);
	}
?>
<?php
	require_once "berryIP_footer.php";
?>
