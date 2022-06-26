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
        $players = array_filter($sender->getServer()->getOnlinePlayers(), function ($player) use ($sender): bool {
            return $player instanceof Player && $player->getId() !== $sender->getId() && $player->getPosition()->distance($sender->getPosition()) <= 100;
        });

        $sender->sendMessage(TextFormat::colorize("&c× Near Players ×\n" . implode("\n", array_map(function (Player $player) use ($sender) {
                return '&f' . $player->getName() . ' &7(' . intval($sender->getPosition()->distance($player->getPosition())) . 'm)&f';
            }, $players))));
    }
}