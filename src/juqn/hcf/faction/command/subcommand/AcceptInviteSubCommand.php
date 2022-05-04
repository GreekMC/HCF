<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\FactionInvite;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AcceptInviteSubCommand extends FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $playerInvites = HCFLoader::getInstance()->getFactionManager()->getInvites($sender->getXuid());

        if ($sender->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou cant accept invites becaues you have faction'));

            if ($playerInvites !== null && count($playerInvites) !== 0) {
                HCFLoader::getInstance()->getFactionManager()->removeInvites($playerInvites);
            }
            return;
        }

        if ($playerInvites === null || count($playerInvites) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
            return;
        }
        $invites = array_filter($playerInvites, function (FactionInvite $invite): bool {
            return $invite->getTime() > time();
        });
    }
}