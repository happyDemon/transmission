<?php

namespace HappyDemon\Transmission\Torrents;


use HappyDemon\Transmission\Transmission;

class Request
{
    protected $transmission;

    protected $fields = [
        'activityDate', 'addedDate', 'bandwidthPriority', 'comment', 'corruptEver', 'creator', 'dateCreated', 'desiredAvailable', 'doneDate',
        'downloadDir', 'downloadedEver', 'downloadLimit', 'downloadLimited', 'error', 'errorString', 'eta', 'files', 'fileStats', 'hashString',
        'haveUnchecked', 'haveValid', 'honorsSessionLimits', 'id', 'isFinished', 'isPrivate', 'leftUntilDone', 'magnetLink', 'manualAnnounceTime',
        'maxConnectedPeers', 'metadataPercentComplete', 'name', 'peer-limit', 'peers', 'peersConnected', 'peersFrom', 'peersGettingFromUs', 'peersKnown',
        'peersSendingToUs', 'percentDone', 'pieces', 'pieceCount', 'pieceSize', 'priorities', 'rateDownload', 'rateUpload', 'recheckProgress',
        'seedIdleLimit', 'seedIdleMode', 'seedRatioLimit', 'seedRatioMode', 'sizeWhenDone', 'startDate', 'status', 'trackers', 'trackerStats', 'totalSize',
        'torrentFile', 'uploadedEver', 'uploadLimit', 'uploadLimited', 'uploadRatio', 'wanted', 'webseeds', 'webseedsSendingToUs'
    ];

    /**
     * Request constructor.
     *
     * @param Transmission $transmission
     */
    public function __construct( Transmission $transmission )
    {
        $this->transmission = $transmission;
    }

    /**
     * Get all torrents.
     *
     * @return Entity[]
     */
    public function get()
    {
        return $this->transmission->request->send([
            'arguments' => [
                'fields' => $this->fields
            ],
            'method' => 'torrent-get'
        ]);
    }

    public function addFromUrl( $url )
    {

    }

    public function addFile( $fileName )
    {

    }

    public function addFromBase64( $blob )
    {

    }
}