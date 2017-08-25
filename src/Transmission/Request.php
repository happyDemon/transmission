<?php

namespace HappyDemon\Transmission;


use HappyDemon\Transmission\Torrents\Entity as TorrentEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Request
{
    /**
     * @var Transmission
     */
    protected $transmission;

    /**
     * Request constructor.
     *
     * @param Transmission $transmission
     */
    public function __construct( Transmission $transmission )
    {
        $this->transmission = $transmission;
    }

    protected $sessionId;

    /**
     * @param $param
     *
     * @throws RequestError
     * @return array
     */
    public function send( $param, $originalEntity = null )
    {
        $authHeader = $this->transmission->username;
        if ( $this->transmission->password ) $authHeader .= ':' . $this->transmission->password;

        $headers = [
            //'Time'                      => (new DateTime)->,
            'X-Requested-With'          => 'PHP Transmission client',
            'X-Transmission-Session-Id' => $this->sessionId,
            'Content-Length'            => strlen(json_encode($param)),
            'Content-Type'              => 'application/json',
            'Authorization'             => 'Basic ' . base64_encode($authHeader),
        ];

        try {
            $client = new Client();

            $response = $client->post($this->transmission->endpoint, [
                'auth'    => [$this->transmission->username, $this->transmission->password],
                'json'    => $param,
                'headers' => $headers,
            ]);

            switch ( $response->getStatusCode() ) {
                case 200:
                    $body = \GuzzleHttp\json_decode($response->getBody()->getContents());

                    // Handle success
                    if ( $body->result == 'success' ) {
                        // Build object with $response->arguments
                        return $this->buildEntities($body, $originalEntity);
                    }

                    // Handle error
                    throw new RequestError('Unsupported result: ' . $body->result);
                    break;
                default:
                    // Unhandled error
                    throw new RequestError('Status code mismatch', $response->getStatusCode());
                    break;
            }
        } catch ( ClientException $e ) {
            // Unhandled status code
            if($e->getResponse()->getStatusCode() != 409) throw new RequestError('Status code mismatch', $e->getResponse()->getStatusCode());

            // Set the sessions ID
            $this->sessionId = $e->getResponse()->getHeader('x-transmission-session-id');

            // Retry the request
            return $this->send($param);
        }
    }

    /**
     * @param             $body
     *
     * @param Entity|null $originalEntity
     *
     * @return null|Entity[]|Entity
     */
    protected function buildEntities( $body, Entity $originalEntity = null )
    {
        if ( !isset($body->arguments) ) return null;

        $arguments = (array) $body->arguments;

        $keys = array_keys($arguments);

        if ( count($keys) == 0 ) return null;

        $entities = [];

        foreach ( $keys as $objectKey ) {
            // If the request resource returns multiple torrent objects
            if ( in_array($objectKey, TorrentEntity::MULTIPLE_OBJECT) ) {
                foreach ( $arguments[$objectKey] as $torrent ) {
                    $entities[] = new TorrentEntity($this->transmission, $torrent);
                }
            } // If the requested resource returns a single torrent object
            else if ( in_array($objectKey, TorrentEntity::SINGULAR_OBJECT) ) {
                if ( $originalEntity == null )
                    $entities[] = new TorrentEntity($this->transmission, $body['arguments'][$objectKey]);
                else
                    $entities[] = $originalEntity->update($arguments[$objectKey]);
            }
        }

        if(count($entities) == 0) return null;

        // When updating an entity, just return the one
        if ( $originalEntity != null && count($entities) == 1 ) return $entities[0];

        return $entities;
    }
}