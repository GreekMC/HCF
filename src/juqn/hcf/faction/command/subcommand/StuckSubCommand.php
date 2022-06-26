<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\Position;
use pocketmine\utils\TextFormat;

class StuckSubCommand implements FactionSubCommand
{
    
    /**
     * @param Player $player
     */
    private function teleport(Player $player): void
    {
        $world = $player->getWorld();
        $x = mt_rand($player->getPosition()->getFloorX() - 100, $player->getPosition()->getFloorX() + 100);
        $z = mt_rand($player->getPosition()->getFloorZ() - 100, $player->getPosition()->getFloorZ() + 100);
        $y = $world->getHighestBlockAt($x, $z);
        
        $position = new Position($x, $y, $z, $world);
        
        if (($claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($position)) !== null) {
            $this->teleport($player);
            return;
        }
        $player->teleport($position->add(0, 1, 0));
    }
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if ($sender->getSession()->getCooldown('faction.stuck') !== null)
            return;
        $sender->getSession()->addCooldown('faction.stuck',  '&l&3Stuck&r&7: &c', 45);
        
        $xuid = $sender->getXuid();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('faction.stuck') !== null) $s->removeCooldown('faction.stuck');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('faction.stuck') !== null) $s->removeCooldown('faction.stuck');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('faction.stuck') === null) {
                $this->teleport($sender);
                $handler->cancel();
            }
        }), 20);
    }
}