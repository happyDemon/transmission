<?php

namespace HappyDemon\Transmission;


class Transmission
{
    public $endpoint = '';
    public $username = '';
    public $password = '';

    /**
     * @var Request
     */
    public $request;

    public function __construct( $config = [] )
    {
        $this->setDefaults($config);
        $this->request = new Request($this);
    }

    /**
     * Builds the url and sets username & password to make requests.
     *
     * Settable values:
     *  - ssl: decides on the protocol
     *  - host: the ip/domain, defaults to localhost
     *  - port: defaults to 9091
     *  - url: the path to the RPC endpoint, defaults to /transmission/rpc
     *
     *  - username: username used for authentication
     *  - password: password used for authentication
     *
     * @param $config
     */
    protected function setDefaults( $config )
    {
        // Set the host
        $url = (isset($config['ssl']) && $config['ssl'] === true) ? 'https://' : 'http://';
        $url .= isset($config['host']) ? $config['host']: 'localhost';
        $url .= ':';
        $url .= isset($config['port'])? $config['port'] : '9091';
        $url .= isset($config['url'])? $config['url'] : '/transmission/rpc';
        $this->endpoint = $url;

        if(isset($config['username'])) $this->username = $config['username'];

        if(isset($config['password'])) $this->password = $config['password'];
    }

    public function torrents()
    {
        return new Torrents\Request($this);
    }
}