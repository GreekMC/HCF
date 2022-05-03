<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FeedCommand extends Command
{

    public function __construct()
    {
        parent::__construct('feed', 'Use command for feed');
        $this->setPermission('feed.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            $sender->getHungerManager()->setFood($sender->getHungerManager()->getMaxFood());
            return;
        }
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);

        if (!$sender->hasPermission('player.feed.command')) {
            $sender->sendMessage(TextFormat::colorize('&cYou dont have permission for use feed in other player'));
            return;
        }

        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found'));
            return;
        }
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
    }
}