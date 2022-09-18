<?php

declare(strict_types=1);

namespace juqn\hcf\event\command;

use juqn\hcf\HCFLoader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class PurgeCommand
 * @package juqn\hcf\event\command
 */
class PurgeCommand extends Command
{
    
    /**
     * PurgeCommand construct.
     */
    public function __construct()
    {
        parent::__construct('purge', 'Command for purge');
        $this->setPermission('sotw.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;
            
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /purge help'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&ePurge Commands') . PHP_EOL .
                    TextFormat::colorize('&7/purge start [time] - &eUse this command to start the purge') . PHP_EOL .
                    TextFormat::colorize('&7/purge stop - &eUse this command to stop purge')
                );
                break;
            
            case 'start':
                if (HCFLoader::getInstance()->getEventManager()->getPurge()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe purge is already started'));
                    return;
                }
                
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /purge start [time]'));
                    return;
                }
                $time = $args[1];
                
                if (!is_numeric($time)) {
                    $sender->sendMessage(TextFormat::colorize('&cInvalid numbers'));
                    return;
                }
                HCFLoader::getInstance()->getEventManager()->getPurge()->setActive(true);
                HCFLoader::getInstance()->getEventManager()->getPurge()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe purge has started!'));
                break;
            
            case 'stop':
                if (!HCFLoader::getInstance()->getEventManager()->getPurge()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe purge has not started'));
                    return;
                }
                HCFLoader::getInstance()->getEventManager()->getPurge()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the purge'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /purge help'));
                break;
        }
    }
}