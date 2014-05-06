<?php
/* file operations - BE CAREFUL! - people will try to access sensitive data through file operations or manipulate your files (.htaccess etc.) */
/* write the error to a log file */
function log2file($file,$message)
{
	if(file_put_contents($file, time().": ".$message."\n", FILE_APPEND))
	{
		// worked
	}
	else
	{
		// outputting logs failed
	}
}