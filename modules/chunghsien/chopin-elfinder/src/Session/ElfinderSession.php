<?php

namespace Chopin\Elfinder\Session;

use elFinderSessionInterface;
use Mezzio\Session\LazySession;

class ElfinderSession implements elFinderSessionInterface
{

    /**
     *
     * @var \Mezzio\Session\LazySession
     */
    private $session;

    private $duration = 0;

    public function __construct(LazySession $session)
    {
        $this->session = $session;
    }

    public function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    public function start()
    {
        $this->session->persistSessionFor($this->duration);
        return $this;
    }

    public function close()
    {
        $this->session->clear();
        return $this;
    }

    public function get($key, $empty = '')
    {
        return $this->session->get($key, $empty);
    }

    public function set($key, $data)
    {
        $this->session->set($key, $data);
        return $this;
    }

    public function remove($key)
    {
        $this->session->unset($key);
        return $this;
    }
}
