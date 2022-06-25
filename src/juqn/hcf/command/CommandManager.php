<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\HCFLoader;

/**
 * Class CommandManager
 * @package juqn\hcf\command
 */
class CommandManager
{
    
    /**
     * CommandManager construct.
     */
    public function __construct()
    {
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FixCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new GodCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PvPCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new RenameCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new AutoFeedCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new TLCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FeedCommand());
    }
}