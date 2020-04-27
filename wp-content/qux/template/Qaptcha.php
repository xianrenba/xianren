<?php
session_start();
$aResponse['error'] = false;
if(isset($_POST['action']) && isset($_POST['myQaptcha']))
{
	$_SESSION['30corg'] = false;	
	
	if(htmlentities($_POST['action'], ENT_QUOTES, 'UTF-8') == '30corg')
	{
		$_SESSION['30corg'] = $_POST['myQaptcha'];
		echo json_encode($aResponse);
	}
	else
	{
		$aResponse['error'] = true;
		echo json_encode($aResponse);
	}
}
else
{
	$aResponse['error'] = true;
	echo json_encode($aResponse);
}