<?php
  $db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
  $filtered = array(
  'IP_ADDR'=>mysqli_real_escape_string($db_conn, $_POST['IP_ADDR']),
  'MAC_ADDR'=>mysqli_real_escape_string($db_conn, $_POST['MAC_ADDR']),
  'OWNER'=>mysqli_real_escape_string($db_conn, $_POST['OWNER']),
  'IS_PROTECTED'=>mysqli_real_escape_string($db_conn,$_POST['IS_PROTECTED']),
  'IP_REMARK'=>mysqli_real_escape_string($db_conn,$_POST['IP_REMARK'])
	);
  $sql = "INSERT INTO IP_INFO
	  (IP_ADDR, MAC_ADDR, OWNER, IS_PROTECTED, IP_REMARK) VALUES(    
	  '{$filtered['IP_ADDR']}',
	 '{$filtered['MAC_ADDR']}',
	 '{$filtered['OWNER']}',
	 '{$filtered['IS_PROTECTED']}',
	 '{$filtered['IP_REMARK']}'
    );
  ";
  $db_res=mysqli_query($db_conn,$sql);
  if($db_res === false){
  echo 'IP 추가하는 과정에서 문제가 생겼습니다.';
  echo mysqli_error($db_conn);
} else {
	header('Location: berryIP_viewIP.php');
}
?>
