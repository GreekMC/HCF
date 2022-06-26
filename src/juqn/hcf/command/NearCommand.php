<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

class NearCommand extends Command
{
    
    /**
     * NearCommand construct.
     */
    public function __construct()
    {
        parent::__construct('near', 'Command for near');
        $this->setPermission('near.command');
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
        
        if (!$this->testPermission($sender))
            return;
        $entities = $sender->getWorld()->getNearbyEntities($sender->getBoundingBox()->expand(200, 200, 200), $sender);
        $entities = array_filter($entities, function ($entity): bool {
            return $entity instanceof Player;
        });
        
        $sender->sendMessage(TextFormat::colorize('&cNear: &f' . array_map(function (Player $player) use ($sender) {
            return $player->getName() . ' &7(' . intval($sender->getPosition()->distance($player->getPosition())) . ')';
        }, $entities)));
    }
}