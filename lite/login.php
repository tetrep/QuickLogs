<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, Feb 2012

	include_once '../cas/CAS.php';
	
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	
	// SSL!
	phpCAS::setCasServerCACert("../cas-auth.rpi.edu");
		
	//If not authenticated then do it
	if (!(phpCAS::isAuthenticated()))
	{
		phpCAS::forceAuthentication();
	}else{
		//We are authenticated, but we may not be in the users database		
			include '../core.php';
	
		QuickLogs::db_connect();
		
		$user_exists = mysql_query("SELECT * FROM `Users` WHERE `username`='" . phpCAS::getUser() ."'");
		
		if (mysql_num_rows($user_exists) == 0) // True is user is not in the database
		{
			$sql = mysql_query("INSERT INTO `QuickLogs`.`Users` (`ID` ,`username` ,`type`) VALUES (NULL , '" . phpCAS::getUser() . "', '0');");
			if ($sql)
			{ //Insert worked, copy default display data
				mysql_query("INSERT INTO `display` (`user`,`Box1`,`Box2`,`Box3`,`Box4`,`Box5`,`Box6`,`Box7`,`Box8`,`Box9`,`Box10`) (SELECT (SELECT `ID` FROM `Users` WHERE `username`='" . phpCAS::getUser() ."' LIMIT 1), `display`.`Box1`, `display`.`Box2`, `display`.`Box3`, `display`.`Box4`, `display`.`Box5`, `display`.`Box6`, `display`.`Box7`, `display`.`Box8`, `display`.`Box9`, `display`.`Box10` FROM `display` WHERE `user`=0)");
			}
		}
		QuickLogs::db_disconnect();
	}
	header("location: ./index.php");
?>