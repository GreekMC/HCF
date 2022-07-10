<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\entity\TextEntity;
use juqn\hcf\entity\CustomItemEntity;
use juqn\hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ClearEntitiesCommand extends Command
{

    public function __construct()
    {
        parent::__construct('clearentities', 'Use command for clear entities');
        $this->setPermission('clearentities.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        
        if (!$this->testPermission($sender))
            return;
            
        if ($sender->getName() !== 'JuqnGOOOD') {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have permissions'));
            return;
        }
        $count = 0;
        
        foreach ($sender->getWorld()->getEntities() as $entity) {
            if ($entity instanceof TextEntity || $entity instanceof CustomItemEntity) {
                $entity->flagForDespawn();
                $count++;
            }
        }
        $sender->sendMessage('Entities removed ' . $count);
    }
}