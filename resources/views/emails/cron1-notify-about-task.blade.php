<?php 
	if( isset($emailData['body']) ){
		echo $emailData['body'];
	}
	else{
		echo 'Please check tickets';
	}
?>