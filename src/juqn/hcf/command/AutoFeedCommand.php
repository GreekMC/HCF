<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AutoFeedCommand extends Command
{

    public function __construct()
    {
        parent::__construct('autofeed', 'Use command for auto feed');
        $this->setPermission('autofeed.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;

        if ($sender->getSession()->hasAutoFeed()) {
            $sender->getSession()->setAutoFeed(false);
            $sender->sendMessage(TextFormat::colorize('&cAutofeed disabled'));
        } else {
            $sender->getSession()->setAutoFeed(true);
            $sender->sendMessage(TextFormat::colorize('&aAutofeed enabled'));
        }
    }
}