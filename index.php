<?php
/*
**   Script feito com a finalidade de importar dados para o SugarCRM ultizando um arquivo Excel (.xls) como origem
**   e a  API Rest v10 do SugarCRM.
**
**   1) Defina as informações básicas de acesso ao Sugar no arquivo sugarConfig.php
**   2) 
**
*/



require_once("sugarConfig.php");
require_once('connect.php');
require_once('excel/Excel/reader.php');
require_once('connectAPI.php');

$ID_USER 					=  1;
$phone_mobile 				=  2;
$date_entered 				=  3;
$cd_operador_c 				=  4;
$cpf_c						=  5;
$lead_source				=  6;
$midia_c					=  7;
$creditcard_bandeira_c 		=  8;
$creditcard_numero_c  		=  9;
$creditcard_validade_mes_c 	= 10;
$creditcard_validade_ano_c 	= 11;
$first_name					= 12;
$last_name					= 13;
$email1						= 14;
$primary_address_postalcode = 15;
$primary_address_bairro_c   = 16;
$primary_address_street		= 17;
$primary_address_num_c		= 18;
$primary_address_compl_c	= 19;
$date_modified				= 20;
$status_c					= 21;
$primary_address_city		= 22;
$primary_address_state		= 23;


	$connectSugar = new connectAPI() ;
	$connectSugar->startSession();
	$statusConection = $connectSugar->connect();

	$url = $base_url . "/Contacts";


	$excel = new Spreadsheet_Excel_Reader();
	$excel->setOutputEncoding('CP1251');
	//$excel->read('amostra_clientes_001.xls');
	$excel->read('Clientes_001_modificada.xls');



	$i=0;
	$errors = 0;
	$imported = 0;

	foreach ($excel->sheets[0]['cells'] as $row ) {
		
		$statusConection = call( $base_url . "/ping" , $_SESSION['access_token'] );
		
		if( $statusConection != 'pong' ){
			$statusConection = $connectSugar->connect();
			echo 'aqui';		
		}


		if ( !$i == 0 )
		{

			$APIarguments = array(
				"phone_mobile"				=>	$row[$phone_mobile],
				"date_entered"				=>	$row[$date_entered],
				"cd_operador_c"				=>	$row[$cd_operador_c],
				"cpf_c"						=>	$row[$cpf_c],
				"lead_source"				=>	$row[$lead_source],
				"midia_c"					=>	$row[$midia_c],
				"creditcard_bandeira_c"		=>	$row[$creditcard_bandeira_c],
				"creditcard_numero_c"		=>	$row[$creditcard_numero_c],
				"creditcard_validade_mes_c"	=>	$row[$creditcard_validade_mes_c],
				"creditcard_validade_ano_c"	=>	$row[$creditcard_validade_ano_c],				
				"first_name"				=>	$row[$first_name],
				"last_name"					=>	$row[$last_name],
				"email1"					=>	$row[$email1],
				"primary_address_postalcode"=>	$row[$primary_address_postalcode],
				"primary_address_bairro_c"	=>	$row[$primary_address_bairro_c],
				"primary_address_street"	=>	$row[$primary_address_street],
				"primary_address_num_c"		=>	$row[$primary_address_num_c],
				"primary_address_compl_c"	=>	$row[$primary_address_compl_c],
				"date_modified"				=>	$row[$date_modified],
				"status_c"					=>	$row[$status_c],
				"primary_address_city"		=>	$row[$primary_address_city],
				"primary_address_state"		=>	$row[$primary_address_state],
			);



			$oauth2_token_response = call($url, $_SESSION['access_token'] , 'POST', $APIarguments , true , false);

			if( $oauth2_token_response->error )
			{
				$errors++;
				$msgErrorLog =  "\nHora:" .date('H:i') . "  ID_USER: " . $row[$ID_USER] . ' Error Message: ' .  $oauth2_token_response->error_message;
				file_put_contents( date('Y-m-d').'_api_send_error' , $msgErrorLog    , FILE_APPEND );

			}else{
				$msgImported =  "\nHora:" .date('H:i') . "  ID_USER: " . $row[$ID_USER] . ' importado com sucesso';
				file_put_contents( date('Y-m-d').'_imported_ok' , $msgImported    , FILE_APPEND );
				$imported++;
			}
		}
		$i++;

	}

 /*
	die();

	var_dump($data);


	die();




	$oauth2_token_arguments = array(
	    "first_nameee" => "teste4",
	    //client id/secret you created in Admin > OAuth Keys
	    #"client_id" => "<CustomID>",
	    #"client_secret" => "<CustomSecret>",
	    #"client_id" => "sugar",
	    #"client_secret" => "",
	    #"username" => $username,
	    #"password" => $password,
	    #"platform" => "api"
	);


	$oauth2_token_response = call($url, $_SESSION['access_token'] , 'POST', $oauth2_token_arguments);

	if( $oauth2_token_response->error )
	{
		echo 'ERRO';
	}else{
		var_dump($oauth2_token_response);
	}
*/

	echo "<h3 style='color: red'> Importacao finalizada! </h3><br />" . " Importados com sucesso: " . $imported . "<br /> Erros: " .$errors;