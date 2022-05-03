<?php

declare(strict_types=1);

namespace juqn\hcf\event;

use juqn\hcf\event\command\EotwCommand;
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
    
    /**
     * EventManager construct.
     */
    public function __construct()
    {
        # Setup main events
        $this->sotw = new EventSotw;
        $this->eotw = new EventEotw;
        # Register commands
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EotwCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new SotwCommand());
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
     * @return EventEotw
     */
    public function getEotw(): EventEotw
    {
        return $this->eotw;
    }
}