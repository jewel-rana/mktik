<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
      /*******
       * Author: Jewel Rana
       * Date: July 8 2016
       * What it does: It is a class that enables you to easily check purchase of Netbill Software,
       * and Customer support manager
      *******/

class Rtlicence extends Apperror
{
	protected $CI;
   protected $domain; //this domain name (where softwer installed)
   protected $filename;
   protected $appcurl;
   protected $key;
   protected $type;
   protected $activation_date;
   protected $expire_date;
   protected $last_check;
   protected $mktik_access;
   protected $access_level;

   	/**
    * This is the function that loads everything. You don't really need to do anything in here,
    * however it does check to see if you are online or not
    **/

  	function __construct()
  	{
  		parent::__construct();
	  	//set essential variables
	   $this->CI =& get_instance();
	   //set filename to private variable
	   $this->filename = LCPATH . 'core' . DIRECTORY_SEPARATOR . 'read_me.txt';
	   //init appcurl
	   if( class_exists( 'appcurl' ) )
	   	$this->appcurl = new Appcurl();
		//set domain name to the variables
		$this->domain = $this->_domainName();
	   // $this->_read(); // read the licence file
	}

	private function _read() {
		$array = array();
		if( ! file_exists( $this->filename ) )
			parent::err( 601 ); //licence key is not exists
		$lines = file( $this->filename ); //, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES 
		if( is_array( $lines ) && count( $lines ) ) :
			$this->key = $this->removeBOM( $lines[0] ); //line number 1
			$this->type = $this->removeBOM( $lines[1] ); //line number 2
			$this->activation_date = ( int ) $this->removeBOM( $lines[2] ); //line number 3
			$this->expire_date = ( int ) $this->removeBOM( $lines[3] ); //line number 4
			$this->last_check = ( int ) $this->removeBOM( $lines[4] ); //line number 5
			$this->access_level = ( int ) $this->removeBOM( $lines[5] ); //line number 6
			$this->mktik_access = ( bool ) $this->removeBOM( $lines[6] ); //line number 6
		endif;
		$this->init();
	}

	private function init() {
		//validate licence again if last_check is over
		if( date( 'm-Y', $this->last_check) < date( 'm-Y', strtotime( '-1 month' ) ) )
			$this->appcurl->check( $this->domain );
	}

	public function mikrotik_access() {
		// return ( $this->mktik_access == true ) ? true : false;
		return true;
	}

	private function removeBOM( $string ) {
   	if( substr( $string, 0, 3 ) == pack( 'CCC', 0xef, 0xbb, 0xbf ) ) :
      	$string= substr( $string, 3 );
   	endif;
   	return $string;
	}

	public function get( $key = '' ) {
		$array = array (
			'key' => $this->key, //line number 1
			'type' => $this->type, //line number 2
			'expire_date' => $this->expire_date, //line number 3
			'last_check' => $this->last_check, //line number 4
		);
		return ( $key != '' && array_key_exists( $key, $array ) ) ? $array[$key] : $array;
	}

	public function _filename() {
		return $this->filename;
	}
	protected function _domainName() {
	    //get domain name from the url
		$this->CI->load->helper('url');
	   $url = site_url();
	   //$parse the url
		$host = parse_url( $url, PHP_URL_HOST );
		//reverse array
		$host = array_reverse( explode( '.', $host ) );
		//check in_array for .bd domain name
		if( in_array( 'bd', $host ) == True ) :
			$host = $host[2] . '.' . $host[1] . '.' . $host[0];
		elseif( in_array( 'localhost', $host ) == true ) :
			$host = $host[0];
		else :
			$host = $host[1] . '.' . $host[0];
		endif;
		return $host;
	}

	public function getDomain() {
		return $this->_domainName();
	}
}
new Rtlicence();