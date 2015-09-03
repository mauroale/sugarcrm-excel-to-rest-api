<?php




class connectAPI {

	private $oauth2_token_arguments ;

		
	public function startSession()
	{
		session_start();

	}

	public function connect(){
	
		global $base_url , $username , $password;

			
		$this->oauth2_token_arguments	= array(
		    "grant_type" => "password",
		    //client id/secret you created in Admin > OAuth Keys
		    #"client_id" => "<CustomID>",
		    #"client_secret" => "<CustomSecret>",
		    "client_id" => "sugar",
		    "client_secret" => "",
		    "username" => $username,
		    "password" => $password,
		    "platform" => "api"
		);
			


		if( !isset( $_SESSION['access_token'] ) )
		{
			$url = $base_url . "/oauth2/token";
			$oauth2_token_response = call( $url, '', 'POST', $this->oauth2_token_arguments);
			$_SESSION['access_token']    = $oauth2_token_response->access_token;
			$_SESSION['refresh_token']   = $oauth2_token_response->refresh_token;
			$_SESSION['download_token']  = $oauth2_token_response->download_token;
			return true;

		}else{
			$url = $base_url . "/ping";
			$pong = call($url, $_SESSION['access_token'] );

			if( ! ($pong == 'pong') )
			{
				$url = $base_url . "/oauth2/token";
				$oauth2_token_response = call( $url, '', 'POST', $oauth2_token_arguments);
				$_SESSION['access_token']    = $oauth2_token_response->access_token;
				$_SESSION['refresh_token']   = $oauth2_token_response->refresh_token;
				$_SESSION['download_token']  = $oauth2_token_response->download_token;
				return true;

			}else{
				return true;

			}

		}

		return false;

	}

	public function testConnection(){

		$statusConection = call( $base_url . "/ping" , $_SESSION['access_token'] );
		
		if( $statusConection == 'pong' ){
			return true;
		}
		return false;
	}
	


}