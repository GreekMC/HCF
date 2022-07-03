<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou already have a faction'));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction create [string: name]'));
            return;
        }
        $factionName = $args[0];

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($factionName) !== null || HCFLoader::getInstance()->getClaimManager()->getClaim($factionName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cA faction or a claim already exists with this name'));
            return;
        }

        if (strlen($factionName) < 4) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction must have more than 5 characters to create it!'));
            return;
        }

        if (strlen($factionName) > 10) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction name cannot contain more than 10 characters'));
            return;
        }
        $checkName = explode(' ', $factionName);

        if (count($checkName) > 1) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction name cannot contain spaces'));
            return;
        }

        if (in_array($factionName, ['Spawn', 'Nether-Spawn', 'End-Spawn'])) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid name'));
            return;
        }
        HCFLoader::getInstance()->getFactionManager()->createFaction($factionName, [
            'roles' => [
                $sender->getXuid() => Faction::LEADER
            ],
            'dtr' => 1.1,
            'balance' => 0,
            'points' => 0,
            'kothCaptures' => 0,
            'timeRegeneration' => 0,
            'home' => null,
            'claim' => null
        ]);
        $sender->getSession()->setFaction($factionName);
        $sender->setScoreTag(TextFormat::colorize('&6[&c' . $factionName . ' &c1.0■&6]'));
        $sender->sendMessage(TextFormat::colorize('&aYou have created the faction'));
        $sender->getServer()->broadcastMessage(TextFormat::colorize('&eTeam &9' . $factionName . ' &ehas been &acreated &eby &f' . $sender->getName()));
    }
}