<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListCommand extends Command
{
    
    /**
     * ListCommand construct.
     */
    public function __construct()
    {
        parent::__construct('players', 'Use command for list players');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $sender->sendMessage(TextFormat::colorize('&7' . PHP_EOL . '&ePlayers playing: &f' . count($sender->getServer()->getOnlinePlayers()) . PHP_EOL . '&e&r'));
    }
}