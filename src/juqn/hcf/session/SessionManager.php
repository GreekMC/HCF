<?php

declare(strict_types=1);

namespace juqn\hcf\session;

use juqn\hcf\HCFLoader;

/**
 * Class SessionManager
 * @package juqn\hcf\session
 */
class SessionManager
{
    
    /** @var Session[] */
    private array $sessions = [];
    
    /**
     * SessionManager construct.
     */
    public function __construct()
    {
        # Register players
        foreach (HCFLoader::getInstance()->getProvider()->getPlayers() as $xuid => $data)
            $this->addSession((string) $xuid, $data, false);
    }
    
    /**
     * @return array
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }
    
    /**
     * @param string $xuid
     * @return Session|null
     */
    public function getSession(string $xuid): ?Session
    {
        return $this->sessions[$xuid] ?? null;
    }
    
    /**
     * @param string $xuid
     * @param array $data
     * @param bool $firstTime
     */
    public function addSession(string $xuid, array $data, bool $firstTime = true): void
    {
        $this->sessions[$xuid] = new Session($data, $firstTime);
    }
}