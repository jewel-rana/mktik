<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Apperror {
	protected $error;
	protected $code;
	public function __construct() {
		$this->error = array (
			601 => 'Sorry! Cannot validate the licence key. Please contact the provider ( <a href="http://rajtika.com" target="ext">here</a> ).',
			602 => 'Sorry! Your license is not valid.',
			603 => 'Sorry! Your license has expired.',
			604 => 'Sorry! Your license expired the demo period.',
			605 => 'Sorry! Your license is not valid.',
			606 => 'Sorry! Your license is not valid.',
			607 => 'Your licence of this product is not passed, please contact the provider ( <a href="http://rajtika.com" target="ext">here</a> ).',
			608 => 'Your trial period has end. Please purchase the licence <a href="http://rajtika.com" target="ext">here</a>'
		);
	}

	protected function err( $code = 601 ) {
		$msg = $this->error[$code];
		if( array_key_exists( $code, $this->error ) )
			die( $msg );
	}
}