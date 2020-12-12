<?php
	require_once "berryIP_header.php"
?>
<form name="addIP_form" align=center method="post">
	<h2><u>IP 추가</u></h2>
	<table class=tbNoBorder align="center">
	<tr>
		<td class=tdGrayC width=100><label for="IP_ADDR">IP</label></td>
		<td class=tdGrayC ><input type="text" id="IP_ADDR" name="IP_ADDR" placeholder=" XXX.XXX.XXX.XXX" required></td>
	</tr>
	<tr>
		<td class=tdGrayC><label for="MAC_ADDR">MAC</label></td>
		<td class=tdGrayC><input type="text" id="MAC_ADDR" name="MAC_ADDR" placeholder=" XX:XX:XX:XX:XX:XX" required></td>
	</tr>
	<tr>
		<td class=tdGrayC><label for="OWNER">성함</label></td>
		<td class=tdGrayC><input type="text" id="OWNER" name="OWNER" placeholder=" 사용자 이름" required></td>
	</tr>
	<tr>
		<td class=tdGrayC>보호여부</td>
		<td class=tdGray>
		<input type="radio" name="IS_PROTECTED" id="pyes" value="0" checked="checked"><label for="pyes">미보호</label></option>
		<input type="radio" name="IS_PROTECTED" id="pno" value="1"><label for="pno">보호</label></option>
		</td>
	</tr>
	<tr>
		<td class=tdGrayC><label for="IP_REMARK">메모</label></td>
		<td class=tdGrayC><input type="text" id="IP_REMARK" name="IP_REMARK"></td>
	</tr>
	<tr>
		<td colspan=2 class=tdRight><button class=butGray onclick="ValidateIPMACaddress(document.addIP_form.IP_ADDR,document.addIP_form.MAC_ADDR )"><b>확 인</b></button>
		<button class=butGray onclick="location.href='./berryIP_viewIP.php'"><b>취 소</b></button></td>
	</tr>
	</table>
</form>
<script src="IPMAC-validation.js"></script>
<?php
	require_once "berryIP_footer.php";
?>
