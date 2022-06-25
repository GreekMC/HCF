<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

/**
 * Class HomeSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class HomeSubCommand implements FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\' have a faction'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if ($faction->getHome() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction has no home'));
            return;
        }
        
        if ($sender->getCooldown('faction.teleport.home') !== null)
            return;
        
        $sender->getSession()->addCooldown('faction.teleport.home',  '&l&1Home&r&7: &c', 15);
        
        $xuid = $sender->getXuid();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($s->getFaction());
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($faction === null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('spawn.tag') !== null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($faction->getHome() === null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('faction.teleport.home') === null) {
                $sender->teleport($faction->getHome());
                $handler->cancel();
            }
        }), 20);
    }
}