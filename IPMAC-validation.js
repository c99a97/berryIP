function ValidateIPMACaddress(IP,MAC)
 {
 // 각각 ip와 mac 주소의 정규표현식
 var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
 var macformat = /^([0-9A-F]{2}[:-]?){5}([0-9A-F]{2})$/;
 
 formName=document.addIP_form;  // input 혹은 button이 들어있는 form의 이름
 
 if(!IP.value.match(ipformat))
 {
	alert("IP주소가 유효하지 않습니다!");
	return formName.IP_ADDR.focus();  // IP_ADDR 은 IP입력받는 input의 이름
 }
 if(!MAC.value.match(macformat))
 {
	alert("MAC주소가 유효하지 않습니다!");
	return formName.MAC_ADDR.focus(); // MAC_ADDR 은 MAC입력받는 input의 이름
 }
 
 formName.action="./berryIP_process_addIP.php" // form action을 넘겨줄 곳
 formName.submit();
 return true;
 
 }

 
 