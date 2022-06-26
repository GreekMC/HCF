<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

class LogoutCommand extends Command
{
    
    /**
     * LogoutCommand construct.
     */
    public function __construct()
    {
        parent::__construct('logout', 'Command for Logout');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getCooldown('logout') !== null)
            return;
        $sender->getSession()->addCooldown('logout',  '&l&gLogout&r&7: &c', 35);
        
        $xuid = $sender->getXuid();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('logout') !== null) $s->removeCooldown('logout');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 3) {
                if ($s->getCooldown('logout') !== null) $s->removeCooldown('logout');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('logout') === null) {
                $s->setLogout(true);
                $sender->kick(TextFormat::colorize('&c[Logout] You have successfully logged out '));
                $handler->cancel();
            }
        }), 20);
    }
}