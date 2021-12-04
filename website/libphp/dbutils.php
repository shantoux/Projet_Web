<?php
	
	// Variable globale de connexion base de données pour simplifier
	$db_conn = null;

	// Fonction de connexion à la base de données postgres
	function connect_db ()
	{
		// on utilisera la variable globale $db_conn
		global $db_conn;
		// Parsing du fichier ini qui intègre les infos de connexion
		$db_info = parse_ini_file(".projetWEB.ini");
		// Connexion à la base de données 
		$db_conn = pg_connect("host=" . $db_info['host'] 
							. " user=" . $db_info['user'] 
							. " password=". $db_info['password']);
	}

	// Fonction de déconnexion de la base de données postgres	
	function disconnect_db ()
	{
		global $db_conn;
		// Ferme la connexion
		pg_close($db_conn);
	}
	
?>