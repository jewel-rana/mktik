<?php
namespace Rajtika\Mikrotik\Services;
use Illuminate\Support\Facades\Config;
use RouterosAPI;

class Routeros extends RouterosAPI
{
    public $host;
    public $port;
    public $user;
    public $password;
    private $service;

    public function __construct()
    {
        $this->host = Config::get('mikrotik.host');
        $this->port = Config::get('mikrotik.port');
        $this->user = Config::get('mikrotik.user');
        $this->password = Config::get('mikrotik.password');
        $this->service = ( Config::has('mikrotik.service') ) ? Config::get('mikrotik.service') : 'pppoe';
    }

    public function _connect()
    {
        return $this->connect();
    }

    public function get( $id = null )
    {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router.';
            return $response;
        }
        if( $id != null ) {
            $info = $this->comm("/ppp/secret/getall", array("?.id" => $id));
            if (!empty($info) && is_array($info)) {
                $response['data'] = $info;
                $response['status'] = true;
            }
        } else {
            $response['msg'] = 'Mikrotik ID not provided!';
        }

        return $response;
    }

    public function getAll()
    {
        $response = array('status' => true, 'msg' => '');
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router.';
            $response['status'] = false;
            return $response;
        }
        $this->write( '/ppp/secret/getall' );
        $read = $this->read( false );
        $response['data'] = $this->parseResponse( $read );
        return $response;
    }

    public function getByName( $name = '' ) {
        $response = array('status' => false, 'msg' => '');
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router.';
            return $response;
        }
        if( $name ) :
            $info = $this->comm( "/ppp/secret/getall", array( "?name" => $name ) );
            if ( !empty( $info[0] ) ) :
                $response['status'] = true;
                $response['data'] = $info[0];
            endif;
        else :
            $response['msg'] = 'Customer username is empty.';
        endif;

        return $response;
    }

    public function create( $params = array() ) {
        $params['service'] =$this->service;
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Router not connected';
            return $response;
        }
        if( $this->_client_exist( $params ) == False ) :
            $res = $this->comm( "/ppp/secret/add", $params );
            $response['mktikId'] = $res;
            $response['status'] = true;
        else :
            $response['msg'] = 'Customer already exist in router';
        endif;

        return $response;
    }

    public function enable( $mktikId = '' ) {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        if( $mktikId !== '' ) :
            $params = array( ".id" => $mktikId, "disabled"  => "no" );
            $this->comm( "/ppp/secret/set", $params );
            $res = $this->comm( "/ppp/secret/getall", array( "?.id" => $mktikId ) );
            if ( !empty( $res ) && $res[0]['disabled'] == 'false' ) {
                $response['status'] = true;
                $response['data'] = $res[0];
            } else {
                $response['msg'] = 'Sorry! cannot enable customer';
            }
        else :
            $response['msg'] = 'Mikrotik ID not found.';
        endif;

        return $response;
    }

    public function disable( $mktikId = '' ) {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        if( $mktikId !== '' ) :
            $params = array( ".id" => $mktikId, "disabled"  => "yes" );
            $this->comm( "/ppp/secret/set", $params );
            $res = $this->comm( "/ppp/secret/getall", array( ".proplist"=> ".id,name,profile,disabled", "?.id" => $mktikId ) );
            if ( !empty( $res ) && $res[0]['disabled'] == 'true' ) {
                $response['status'] = true;
                $response['data'] = $res[0];
            } else {
                $response['msg'] = 'Sorry! cannot disable customer';
            }
        else :
            $response['msg'] = 'Mikrotik ID not found.';
        endif;

        return $response;
    }

    public function changeName( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        if( $params['name'] != '' ) {
            $this->comm("/ppp/secret/set", $params);
            $res = $this->comm("/ppp/secret/getall", array(".proplist" => ".id,name,profile", "?.id" => $params['.id']));
            if ( $res[0]['name'] == $params['name'] ) {
                $response['status'] = true;
                $response['data'] = $res[0];
            } else {
                $response['msg'] = 'Sorry! cannot disable customer';
            }
        } else {
            $response['msg'] = 'Mikrotik ID not found.';
        }

        return $response;
    }

    public function changePassword( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        if( $params['password'] != '' ) {
            $this->comm("/ppp/secret/set", $params);
            $res = $this->comm("/ppp/secret/getall", array(".proplist" => ".id,name,profile,password", "?.id" => $params['.id']));
            if ( $res[0]['password'] == $params['password'] ) {
                $response['status'] = true;
                $response['data'] = $res[0];
            } else {
                $response['msg'] = 'Sorry! cannot change password';
            }
        } else {
            $response['msg'] = 'Your password is empty';
        }
        return $response;
    }

    /**
     * Change Profile means Change the Packege
     **/
    public function changeProfile( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        if( $params['password'] != '' ) {
            $this->comm("/ppp/secret/set", $params);
            $res = $this->comm("/ppp/secret/getall", array(".proplist" => "id,name,profile", "?.id" => $params['.id']));
            if ( $res[0]['profile'] == $params['profile'] ) {
                $response['status'] = true;
                $response['data'] = $res[0];
            } else {
                $response['msg'] = 'Sorry! cannot change package';
            }
        } else {
            $response['msg'] = 'Customer package not selected.';
        }
    }

    public function resource()
    {
        $response = ['status' => false, 'msg' => ''];
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router';
            return $response;
        }
        $query = $this->comm("/system/resource/print"); ///system/resource/print
        if(!empty($query) && is_array($query)) {
            $response['data'] = $query[0];
        } else {
            $response['msg'] = 'Could not fetch resources';
        }
        return $response;
    }

    private function _client_exist( $params ) {
        $this->connect();
        if( $this->connected == false ) return false;
        if( $params['name'] ) :
            $info = $this->comm( "/ppp/secret/getall", array( ".proplist"=> ".id", "?name" => $params['name'] ) );
            return ( !empty( $info ) && is_array( $info ) ) ? true : false;
        else :
            return false;
        endif;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
