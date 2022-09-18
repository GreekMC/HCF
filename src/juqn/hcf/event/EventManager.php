<?php

declare(strict_types=1);

namespace juqn\hcf\event;

use juqn\hcf\event\command\EotwCommand;
use juqn\hcf\event\command\PurgeCommand;
use juqn\hcf\event\command\SotwCommand;
use juqn\hcf\HCFLoader;

/**
 * Class EventManager
 * @package juqn\hcf\event
 */
class EventManager
{
    
    /** @var EventSotw */
    private EventSotw $sotw;
    /** @var EventEotw */
    private EventEotw $eotw;
    /** @var EventPurge */
    private EventPurge $purge;
    
    /**
     * EventManager construct.
     */
    public function __construct()
    {
        # Setup main events
        $this->sotw = new EventSotw;
        $this->eotw = new EventEotw;
        $this->purge = new EventPurge;
        # Register commands
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EotwCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new SotwCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PurgeCommand());
        # Register listener
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new EventListener(), HCFLoader::getInstance());
    }
    
    /**
     * @return EventSotw
     */
    public function getSotw(): EventSotw
    {
        return $this->sotw;
    }

    /**
     * @return EventPurge
     */
    public function getPurge(): EventPurge
    {
        return $this->purge;
    }
    
    /**
     * @return EventEotw
     */
    public function getEotw(): EventEotw
    {
        return $this->eotw;
    }
}