<?php

namespace HappyDemon\Transmission\Torrents;


use HappyDemon\Transmission\Request as BaseRequest;

class Request extends BaseRequest
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

    protected $defaults = [];

    /**
     * Get all torrents.
     *
     * @param null|integer|array $id
     *
     * @return Entity[]
     */
    public function get($id = null)
    {
        $arguments = [
            'fields' => $this->fields
        ];

        // Register the id(s)
        if($id !== null)
        {
            $arguments['ids'] = (is_array($id)) ? $id : [$id];
        }

        return $this->send([
            'arguments' => $arguments,
            'method' => 'torrent-get'
        ]);
    }

    /**
     * Extra param can contain:
     *  - download-dir: The directory you want the download to end up in
     *  - paused: Should it start paused?
     *  - peer-limit: maximum number of peers
     *  - files-wanted: indices of file(s) to download
     *  - files-unwanted: indices of file(s) not to download
     *  - priority-high: indices of high-priority file(s)
     *  - priority-low: indices of low-priority file(s)
     *  - priority-normal: indices of normal-priority file(s)
     *
     * @param       $url
     * @param array $extra_param
     *
     * @return array
     */
    public function addFromUrl( $url, $extra_param = [] )
    {
        return $this->send([
            'method' => 'torrent-add',
            'arguments' => array_merge($extra_param, $this->defaults, ['filename' => $url])
        ]);
    }

    /**
     * @see addFromUrl
     *
     * @param       $fileName
     * @param array $extra_param
     *
     * @return array
     * @throws \Exception
     */
    public function addFile( $fileName, $extra_param = []  )
    {
        if(!file_exists($fileName)) throw new \Exception('File does not exist: ' . $fileName);

        $content = base64_encode(file_get_contents($fileName));

        return $this->addFromBase64($content, $extra_param);
    }

    /**
     * @see addFromUrl
     *
     * @param       $content
     * @param array $extra_param
     *
     * @return array
     */
    public function addFromBase64( $content, $extra_param = []  )
    {
        // Make sure filename is not present
        unset($extra_param['filename']);

        return $this->send([
            'method' => 'torrent-add',
            'arguments' => array_merge($extra_param, $this->defaults, ['metainfo' => $content])
        ]);
    }

    /**
     * Set defaults properties that you want to send over every time you create a torrent.
     * For example you could set the seed or upload limit.
     *
     * These defaults will always overwrite you transmission program's default settings.
     *
     * @param array $properties
     */
    public function defaults( array $properties )
    {
        $this->defaults = $properties;
    }
}