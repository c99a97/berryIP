<?php
  $div = $_POST['div'];
  $db_conn=mysqli_connect("localhost","cswin","cswin","berryIP");
  $filtered = array(
  'Origin_IP_ADDR' =>mysqli_real_escape_string($db_conn, $_POST['Origin_IP_ADDR'.$div]),
  'IP_ADDR'=>mysqli_real_escape_string($db_conn, $_POST['IP_ADDR'.$div]),
  'MAC_ADDR'=>mysqli_real_escape_string($db_conn, $_POST['MAC_ADDR'.$div]),
  'OWNER'=>mysqli_real_escape_string($db_conn, $_POST['OWNER'.$div]),
  'IS_OK'=>mysqli_real_escape_string($db_conn,$_POST['IS_OK'.$div]),
  'IS_PROTECTED'=>mysqli_real_escape_string($db_conn,$_POST['IS_PROTECTED'.$div]),
  'IP_REMARK'=>mysqli_real_escape_string($db_conn,$_POST['IP_REMARK'.$div])
	);
  $sql = "UPDATE IP_INFO SET
	IP_ADDR = '{$filtered['IP_ADDR']}' ,
	MAC_ADDR = '{$filtered['MAC_ADDR']}',
	OWNER = '{$filtered['OWNER']}',
	IS_OK = '{$filterd['IS_OK']}',
	IS_PROTECTED = '{$filtered['IS_PROTECTED']}',
	IP_REMARK = '{$filtered['IP_REMARK']}'
	WHERE IP_ADDR = '{$filtered['Origin_IP_ADDR']}';
  ";
  $db_res=mysqli_query($db_conn,$sql);
  if($db_res === false){	
  echo 'IP 수정하는 과정에서 문제가 생겼습니다.';
  echo mysqli_error($db_conn);
  } else {
	  header('Location: berryIP_viewIP.php');
}
?>
