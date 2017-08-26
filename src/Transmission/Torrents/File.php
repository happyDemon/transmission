<?php

namespace HappyDemon\Transmission\Torrents;


class File
{
    /**
     * @var Entity
     */
    protected $torrent;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $bytesCompleted;

    /**
     * @var integer
     */
    public $length;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $priority;

    /**
     * @var bool
     */
    public $wanted;

    /**
     * File constructor.
     *
     * @param Entity    $torrent
     * @param           $id
     */
    public function __construct( Entity $torrent, $id)
    {
        $this->torrent = $torrent;
        $this->id = $id;

        $this->updateFileStats();
    }

    public function updateFileStats()
    {
        $this->bytesCompleted = $torrent->files[$id]->bytesCompleted;
        $this->length = $torrent->files[$id]->length;
        $this->name = $torrent->files[$id]->name;
        $this->wanted = (bool) $torrent->fileStats[$id]->wanted;

        switch($torrent->fileStats[$id]->priority)
        {
            case 0:
                $this->priority = 'normal';
                break;
            case 1:
                $this->priority = 'high';
                break;
            case -1:
                $this->priority = 'low';
                break;
        }
    }

    public function highPriority()
    {
        $this->torrent->setPriorityHigh([$this->id]);
    }

    public function lowPriority()
    {
        $this->torrent->setPriorityLow([$this->id]);
    }

    public function normalPriority()
    {
        $this->torrent->setPriorityNormal([$this->id]);
    }



    public function wanted()
    {
        $this->torrent->setFilesWanted([$this->id]);
    }

    public function unwanted()
    {
        $this->torrent->setFilesUnwanted([$this->id]);
    }
}