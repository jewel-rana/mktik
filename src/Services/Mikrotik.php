<?php
namespace Rajtika\Mikrotik\Services;
use Illuminate\Support\Facades\Config;
use PEAR2\Console\CommandLine\Exception;
use PEAR2\Net\RouterOS;
use PEAR2\Net\RouterOS\Client;
use PEAR2\Net\RouterOS\Request;
use PEAR2\Net\RouterOS\Response;
use PEAR2\Net\RouterOS\Util;
use PEAR2\Net\RouterOS\Query;

class Mikrotik
{
    public   $host;
    public   $port;
    public   $user;
    public   $password;
    public   $service;
    public   $client;
    /**
     * @var bool
     */
    private $connected = false;

    public function __construct()
    {
        $this->host = Config::get('mikrotik.host');
        $this->port = ( !empty( Config::get('mikrotik.port') ) ) ? '8728' : Config::get('mikrotik.port');
        $this->user = Config::get('mikrotik.user');
        $this->password = Config::get('mikrotik.password');
        $this->service = ( Config::has('mikrotik.service') ) ? Config::get('mikrotik.service') : 'pppoe';

        //check mikrotik enabled then otherwize return with response and return true
        return array('status' => true, 'msg' => 'Mikrotik not enabled in your settings.');
    }

    public function dump() {
        dd( 'Dump from Mikrotik Services for Pear2');
    }

    public function connect()
    {
        if( !empty( $this->host ) && !empty( $this->user ) && !empty( $this->password ) ) {
            try {
                $this->client = new Client($this->host, $this->user, $this->password, $this->port);
                $this->connected = true;
            } catch (Exception $e) {
                $this->connected = false;
            }
        } else {
            $this->connected = false;
        }
    }

    public function logs()
    {
        $response = array('status' => true, 'msg' => '');
        $this->connect();
        if( $this->connected == false ) {
            $response['msg'] = 'Could not connect to router.';
            $response['status'] = false;
            return $response;
        }
        try{
            $util = new Util( $this->client );
            $response['data'] = $util->setMenu('/log')->getAll();
        } catch (Exception $e) {
            $response['msg'] = 'An error occured to collect system user logs';
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
        $response['data'] = $this->client->sendSync(new Request('/ppp/secret/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);
        return $response;
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
            $customer = new Request('/ppp/secret/getall');
            $customer->setQuery(Query::where('.id', $id));
            $info = $this->client->sendSync($customer);
            if ( !empty( $info[0] ) ) :
                $response['status'] = true;
                $response['data'] = $info[0];
            endif;
        } else {
            $response['msg'] = 'Mikrotik ID not provided!';
        }

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
            $customer = new Request('/ppp/secret/getall');
            $customer->setArgument('.proplist', '.id,name,profile,service');
            $customer->setQuery(Query::where('name', $name));
            $info = $this->client->sendSync($customer);
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
        $response = ['status' => false, 'msg' => ''];
        //check mikrotike enabled
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if ($this->connected == false) {
                $response['msg'] = 'Router not connected';
                return $response;
            }
            $customer = new RouterOS\Request('/ppp/secret/add');
            $customer->setArgument('name', $params['name']);
            $customer->setArgument('profile', $params['profile']);
            $customer->setArgument('password', $params['password']);
            $customer->setArgument('service', $this->service);
            $customer->setArgument('comment', $params['comment']);
            $customer->setArgument('disabled', $params['status']);

            if ($this->client->sendSync($customer)->getType() !== RouterOS\Response::TYPE_FINAL) {
                $response['msg'] = 'Sorry! cannot create customer';
            } else {
                $this->client->loop();
                $responses = $this->client->extractNewResponses();
                $response['status'] = true;
                $response['msg'] = 'Customer has been successfully created';
            }
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotik configuration is not set.';
        }
        return $response;
    }

    public function enable( $customer = '' ) {
        $response = ['status' => false, 'msg' => ''];
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if ($this->connected == false) {
                $response['msg'] = 'Could not connect to router';
                return $response;
            }
            if ($customer !== '') {
                $mktikId = $customer['mktikId'];
                if (!empty( $mktikId ) ) {
                    $customer = new RouterOS\Request('/ppp/secret/set');
                    $customer->setArgument('.id', $mktikId);
                    $customer->setArgument('disabled', 'no');
                    $customer->setArgument('.proplist', '.id,name,profile,service');
                    if ($this->client->sendSync($customer)->getType() === Response::TYPE_FINAL) {
                        $response['status'] = true;
                    } else {
                        $response['msg'] = 'Mikrotik! Customer cannot be enabled.';
                    }
                } else {
                    $response['msg'] = 'Mikrotik ID not set.';
                }
            } else {
                $response['msg'] = 'Customer not found.';
            }
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotike configuration not set.';
        }
        return $response;
    }

    public function disable( $customer = '' ) {
        $response = ['status' => true, 'msg' => ''];
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if ($this->connected == false) {
                $response['msg'] = 'Could not connect to router';
                return $response;
            }
            if ($customer != '') :
                $mktikId = $customer['mktikId'];
                if( $mktikId == '' ) {
                    $user = $this->getByName($customer['customerID']);
                    if( !empty($user['.id'] ) ) {
                        $mktikId = $user['.id'];
                    }
                }

                if (empty($mktikId)) {
                    $customer = new Request('/ppp/secret/set');
                    $customer->setArgument('.id', $mktikId);
                    $customer->setArgument('disabled', 'yes');
                    $customer->setArgument('.proplist', '.id,name,profile,service');
                    if ($this->client->sendSync($customer)->getType() === Response::TYPE_FINAL) {
                        $response['status'] = true;
                    } else {
                        $response['msg'] = 'Sorry! cannot disable customer';
                    }
                } else {
                    $response['msg'] = 'Customer not found in router.';
                }
            else :
                $response['msg'] = 'Mikrotik ID not found.';
            endif;
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotike configuration not set';
        }

        return $response;
    }

    public function changeName( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if( $this->connected == false ) {
                $response['msg'] = 'Could not connect to router';
                return $response;
            }
            if( $params['name'] != '' ) {
                $customer = new Request('/ppp/secret/set');
                $customer->setArgument('.id', $params['.id']);
                $customer->setArgument('name', $params['name']);
                if( $this->client->sendSync($customer)->getType() === Response::TYPE_FINAL ) {
                    $response['status'] = true;
                } else {
                    $response['msg'] = 'Sorry! cannot change customer name';
                }
            } else {
                $response['msg'] = 'Mikrotik ID not found.';
            }
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotike configuration not set.';
        }
        return $response;
    }

    public function changePassword( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if ($this->connected == false) {
                $response['msg'] = 'Could not connect to router';
                return $response;
            }
            if ($params['password'] != '') {
                $customer = new Request('/ppp/secret/set');
                $customer->setArgument('.id', $params['.id']);
                $customer->setArgument('password', $params['password']);
                if ($this->client->sendSync($customer)->getType() === Response::TYPE_FINAL) {
                    $response['status'] = true;
                } else {
                    $response['msg'] = 'Sorry! cannot change password';
                }
            } else {
                $response['msg'] = 'Your password is empty';
            }
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotike configuration not set.';
        }
        return $response;
    }

    /**
     * Change Profile means Change the Packege
     **/
    public function changeProfile( $params = array() ) {
        $response = ['status' => false, 'msg' => ''];
        if( $this->mikrotik_enabled() ) {
            $this->connect();
            if ($this->connected == false) {
                $response['msg'] = 'Could not connect to router';
                return $response;
            }
            if ($params['profile'] != '') {
                $customer = new Request('/ppp/secret/set');
                $customer->setArgument('.id', $params['.id']);
                $customer->setArgument('profile', $params['profile']);
                if ($this->client->sendSync($customer)->getType() === Response::TYPE_FINAL) {
                    $response['status'] = true;
                    $response['msg'] = 'Customer package has been successfully changed!';
                } else {
                    $response['msg'] = 'Sorry! cannot change package';
                }
            } else {
                $response['msg'] = 'Customer package not selected.';
            }
        } else {
            $response['status'] = true;
            $response['msg'] = 'Mikrotike configuration not set.';
        }

        return $response;
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

    private function mikrotik_enabled()
    {
        if ( getOption('mikrotik_access') ) {
            if( !empty( $this->host ) && !empty( $this->user ) && $this->password && $this->port ) {
                return true;
            }
        }
        return false;
    }

    private function _exist( $params ) {
        $this->connect();
        if( $this->connected == false ) {
            $this->customer_exist = false;
        }
        if( $params['name'] ) {
            $customer = new Request('/ppp/secret/getall');
            $customer->setArgument('.proplist', '.id,name,profile,service');
            $customer->setQuery(Query::where('name', $params['name']));
            $id = $this->client->sendSync($customer)->getProperty('.id');
            if( !empty($id) && is_array($id) ) {
                $this->customer_exist = true;
            }
        }
    }

    public function __destruct()
    {
//        $this->disconnect();
    }
}
