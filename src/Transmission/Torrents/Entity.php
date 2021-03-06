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
 * @property array fileStats
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
 * @property array peersFrom
 * @property string peersGettingFromUs
 * @property string peersKnown
 * @property string peersSendingToUs
 * @property string percentDone
 * @property string pieces
 * @property string pieceCount
 * @property string pieceSize
 * @property array priorities
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
 * @property array trackers
 * @property array trackerStats
 * @property string totalSize
 * @property string torrentFile
 * @property string uploadedEver
 * @property string uploadLimit
 * @property string uploadLimited
 * @property string uploadRatio
 * @property array wanted
 * @property array webseeds
 * @property string webseedsSendingToUs
 *
 * @method setDownloadLimited(boolean $shouldBeLimited)
 * @method setBandwidthPriority(integer $priority)
 * @method setDownloadLimit(integer $limit)
 * @method setFilesWanted(array $wantedFiles)
 * @method setFilesUnwanted(array $unwantedFiles)
 * @method setLocation(string $directory)
 * @method setPeerLimit(integer $limit)
 * @method setPriorityHigh(array $files)
 * @method setPriorityLow(array $files)
 * @method setPriorityNormal(array $files)
 * @method setSeedRatioLimit(double $ratio)
 * @method setSeedRatioMode(integer $mode)
 * @method setUploadLimit(integer $limit)
 * @method setUploadLimited(boolean $shouldBeLimited)
 */
class Entity extends BaseEntity
{
    protected $requires = ['id'];
    const SINGULAR_OBJECT = ['torrent-added'];
    const MULTIPLE_OBJECT = ['torrents'];

    public $files = [];

    /**
     * Start the torrent.
     *
     * @return array
     */
    public function start()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
            ],
            'method'    => 'torrent-start',
        ]);
    }

    /**
     * Stop or pause the torrent.
     *
     * @return array
     */
    public function stop()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
            ],
            'method'    => 'torrent-stop',
        ]);

    }

    /**
     * Verify the torrent.
     *
     * @return array
     */
    public function verify()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
            ],
            'method'    => 'torrent-verify',
        ]);

    }

    /**
     * Re-announce the torrent
     */
    public function announce()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
            ],
            'method'    => 'torrent-reannounce',
        ]);

    }

    /**
     * Remove the torrent.
     *
     * @return array
     */
    public function remove()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids' => $this->id,
            ],
            'method'    => 'torrent-remove',
        ]);
    }

    /**
     * Move the torrent.
     *
     * @param $location
     *
     * @return array
     */
    public function move( $location )
    {
        return $this->transmission->request->send([
            'arguments' => [
                'ids'      => $this->id,
                'location' => $location,
                'move'     => true,
            ],
            'method'    => 'torrent-move',
        ]);

    }

    protected $allowedUpdates = [
        "bandwidthPriority"   => 'integer', "downloadLimit" => 'integer', "downloadLimited" => 'boolean', "files-wanted" => 'array', "files-unwanted" => 'array',
        "honorsSessionLimits" => "boolean", "location" => 'string', "peer-limit" => 'number', "priority-high" => 'array', "priority-low" => 'array',
        "priority-normal"     => 'array', "seedRatioLimit" => 'double', "seedRatioMode" => 'integer', "uploadLimit" => 'integer', "uploadLimited" => 'boolean',
    ];

    protected function validateUpdateProperty( $key, $value )
    {
        if ( !array_key_exists($key, $this->allowedUpdates) ) throw new \Exception('Property is not updateable: ' . $key);

        switch ( $this->allowedUpdates[$key] ) {
            case 'integer':
                if ( !is_numeric($value) ) throw new \Exception('Property is not an integer: ' . $key);
                break;
            case 'array':
                if ( !is_array($value) ) throw new \Exception('Property is not an integer: ' . $key);
                break;
            case 'boolean':
                if ( !is_bool($value) ) throw new \Exception('Property is not an integer: ' . $key);
                break;
            case 'double':
                if ( !is_double($value) ) throw new \Exception('Property is not an integer: ' . $key);
                break;
        }
    }

    /**
     * Change torrent-specific settings.
     *
     * @param $properties
     *
     * @return array
     */
    public function settings( $properties )
    {
        $propertyKeys = array_keys($properties);

        foreach ( $propertyKeys as $key ) {
            $this->validateUpdateProperty($key, $properties[$key]);
        }

        return $this->transmission->request->send([
            'arguments' => array_merge($properties, ['ids' => $this->id]),
            'method'    => 'torrent-set',
        ]);
    }

    /**
     * Makes the status human-readable.
     *
     * @return string
     */
    public function status()
    {
        $status = [
            'STOPPED', 'CHECK_WAIT', 'CHECK', 'DOWNLOAD_WAIT', 'DOWNLOAD', 'SEED_WAIT', 'SEED', 'ISOLATED',
        ];

        return $status[$this->status];
    }

    /**
     * Returns the activity date as a DateTime Object.
     *
     * @return \DateTime
     */
    public function activityDate()
    {
        return new \DateTime($this->activityDate);
    }

    /**
     * Returns the added date as a DateTime Object.
     *
     * @return \DateTime
     */
    public function addedDate()
    {
        return new \DateTime($this->addedDate);
    }


    /**
     * Returns the created date as a DateTime Object.
     *
     * @return \DateTime
     */
    public function dateCreated()
    {
        return new \DateTime($this->dateCreated);
    }


    /**
     * Returns the done date as a DateTime Object.
     *
     * @return \DateTime
     */
    public function doneDate()
    {
        if ( $this->doneDate == 0 ) return null;

        return new \DateTime($this->doneDate);
    }

    /**
     * Returns the percentage done.
     *
     * @return integer
     */
    public function percentDone()
    {
        return $this->percentDone * 100;
    }

    /**
     * Short hand call to update torrent settings
     *
     * @param $method
     * @param $arguments
     *
     * @return array|void
     */
    public function __call( $method, $arguments )
    {
        // We're only looking for methods prefixed with 'set'
        if ( strpos($method, 'set') != 0 ) return;

        // Normalise the method name to a property's name
        $property = lcfirst(substr($method, 3));

        // handle special cases
        if ( strpos($property, 'files') == 0 )
        {
            $property = 'files-' . strtolower(str_replace('files', '', $property));
        }
        else if ( strpos($property, 'priority') == 0 )
        {
            $property = 'priority-' . strtolower(str_replace('priority', '', $property));
        }
        else if ( strpos($property, 'peer') == 0 )
        {
            $property = 'peer-' . strtolower(str_replace('peer', '', $property));
        }

        // Update the single property
        $this->settings([
            $property => $arguments[0],
        ]);
    }

    public function update( $fields )
    {
        parent::update($fields);

        $this->files = [];

        if(count($this->properties['files']) > 0)
        {
            foreach($this->properties['files'] as $id => $file)
            {
                $this->files[$id] = new File($this, $id);
            }
        }
    }
}