<?php

namespace HappyDemon\Transmission\Torrents;


use HappyDemon\Transmission\Entity as BaseEntity;
/**
 * Torrent Entity
 *
 * Defines the entity and allows easy access to torrent-specific calls.
 *
 * @property string activityDate
 * @property string addedDate
 * @property string bandwidthPriority
 * @property string comment
 * @property string corruptEver
 * @property string creator
 * @property string dateCreated
 * @property string desiredAvailable
 * @property string doneDate
 * @property string downloadDir
 * @property string downloadedEver
 * @property string downloadLimit
 * @property string downloadLimited
 * @property string error
 * @property string errorString
 * @property string eta
 * @property string files
 * @property string fileStats
 * @property string hashString
 * @property string haveUnchecked
 * @property string haveValid
 * @property string honorsSessionLimits
 * @property string id
 * @property string isFinished
 * @property string isPrivate
 * @property string leftUntilDone
 * @property string magnetLink
 * @property string manualAnnounceTime
 * @property string maxConnectedPeers
 * @property string metadataPercentComplete
 * @property string name
 * @property string peer-limit
 * @property string peers
 * @property string peersConnected
 * @property string peersFrom
 * @property string peersGettingFromUs
 * @property string peersKnown
 * @property string peersSendingToUs
 * @property string percentDone
 * @property string pieces
 * @property string pieceCount
 * @property string pieceSize
 * @property string priorities
 * @property string rateDownload
 * @property string rateUpload
 * @property string recheckProgress
 * @property string seedIdleLimit
 * @property string seedIdleMode
 * @property string seedRatioLimit
 * @property string seedRatioMode
 * @property string sizeWhenDone
 * @property string startDate
 * @property string status
 * @property string trackers
 * @property string trackerStats
 * @property string totalSize
 * @property string torrentFile
 * @property string uploadedEver
 * @property string uploadLimit
 * @property string uploadLimited
 * @property string uploadRatio
 * @property string wanted
 * @property string webseeds
 * @property string webseedsSendingToUs
 */
class Entity extends BaseEntity
{
    protected $requires = ['id'];
    const SINGULAR_OBJECT = ['torrent-added'];
    const MULTIPLE_OBJECT = ['torrents'];

    public function start(  )
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id
            ],
            'method' => 'torrent-start'
        ]);
    }

    public function stop()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id
            ],
            'method' => 'torrent-stop'
        ]);
        
    }

    public function verify()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id
            ],
            'method' => 'torrent-verify'
        ]);
        
    }

    /**
     * Re-announce the torrent
     */
    public function announce()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id
            ],
            'method' => 'torrent-reannounce'
        ]);
        
    }

    public function remove()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id
            ],
            'method' => 'torrent-remove'
        ]);
    }

    public function move( $location )
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
                'location' => $location,
                'move' => true
            ],
            'method' => 'torrent-move'
        ]);

    }

    public function update( $properties )
    {
        $allowedUpdates = [
            "bandwidthPriority" => 'integer', "downloadLimit" => 'integer', "downloadLimited" => 'boolean', "files-wanted" => 'array', "files-unwanted" => 'array',
            "honorsSessionLimits" => "boolean", "location" => 'string', "peer-limit" => 'number', "priority-high" => 'array', "priority-low" => 'array',
            "priority-normal" => 'array', "seedRatioLimit" => 'double', "seedRatioMode" => 'integer', "uploadLimit" => 'integer', "uploadLimited" => 'boolean'
        ];

        $propertyKeys = array_keys($properties);

        foreach($propertyKeys as $key) {
            if ( !array_key_exists($key, $allowedUpdates) ) throw new \Exception('Property is not updateable: ' . $key);

            switch ( $allowedUpdates[$key] )
            {
                case 'integer':
                    if(!is_numeric($properties[$key])) throw new \Exception('Property is not an integer: ' . $key);
                    break;
                case 'array':
                    if(!is_array($properties[$key])) throw new \Exception('Property is not an integer: ' . $key);
                    break;
                case 'boolean':
                    if(!is_bool($properties[$key])) throw new \Exception('Property is not an integer: ' . $key);
                    break;
                case 'double':
                    if(!is_double($properties[$key])) throw new \Exception('Property is not an integer: ' . $key);
                    break;
            }
        }

        return $this->transmission->request->send([
            'arguments' => array_merge($properties, ['ids' => $this->id]),
            'method' => 'torrent-set'
        ]);
    }

    public function status()
    {
        $status= [
            'STOPPED', 'CHECK_WAIT', 'CHECK', 'DOWNLOAD_WAIT', 'DOWNLOAD', 'SEED_WAIT', 'SEED', 'ISOLATED'
        ];

        return $status[$this->status];
    }

    public function activityDate()
    {
        return new \DateTime($this->activityDate);
    }

    public function addedDate()
    {
        return new \DateTime($this->addedDate);
    }

    public function dateCreated()
    {
        return new \DateTime($this->dateCreated);
    }

    public function doneDate()
    {
        if($this->doneDate == 0) return null;

        return new \DateTime($this->doneDate);
    }

    public function percentDone()
    {
        return $this->percentDone * 100;
    }
}