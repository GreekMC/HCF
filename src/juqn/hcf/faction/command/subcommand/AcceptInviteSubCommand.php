<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\faction\FactionInvite;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AcceptInviteSubCommand implements FactionSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&cYou have already faction'));
            return;
        }
        //cambiar tag del player y bajar dtr de la faction y si el tiempo de regeneracion de cooldown esta actibo no pueda usar el comando

        if (isset($args[0])) {
            $player = $sender->getServer()->getPlayerByPrefix($args[0]);

            if (!$player instanceof Player) {
                $sender->sendMessage(TextFormat::colorize('&cPlayer not found'));
                return;
            }

            $playerInvites = HCFLoader::getInstance()->getFactionManager()->getInvites($sender->getXuid());
            $playerInvite = $player->getName();

            if ($playerInvites === null || count($playerInvites) === 0) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
                return;
            }
            $invites = array_filter($playerInvites, function (FactionInvite $invite): bool {
                return $invite->getTime() > time();
            });

            if (!isset($invites[$playerInvite])) {
                $sender->sendMessage(TextFormat::colorize('&cYou have no invites from this player'));
                return;
            }
            $invite = $invites[$playerInvite];

            if ($invite->getTime() < time()) {
                $sender->sendMessage(TextFormat::colorize('&cThis invite has already expired'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $playerInvite);
                return;
            }
            if ($invite->getPlayer()->getSession()->getFaction() !== $invite->getFaction()) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $invite->getPlayer()->getName());
                return;
            }
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
    
            if ($faction->getRole($playerInvite) === Faction::MEMBER) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $player->getName());
                return;
            }
            if ($player->isOnline()) {
                $player->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' accepted invitation for join in your faction'));
            }
            $sender->sendMessage(TextFormat::colorize('&aYou have accepted ' . $player->getName() . '\' invite for join in faction'));
            
            $faction->addRole($sender->getXuid(), Faction::MEMBER);
            $faction->announce(TextFormat::colorize('&a' . $sender->getName() . ' joined the faction'));
    
            $sender->getSession()->setFaction($faction->getName());
    
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $player->getName());
            return;
        }
        $playerInvites = HCFLoader::getInstance()->getFactionManager()->getInvites($sender->getXuid());

        if ($sender->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou cant accept invites becaues you have faction'));

            if ($playerInvites !== null && count($playerInvites) !== 0) {
                HCFLoader::getInstance()->getFactionManager()->removeInvites($sender);
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

        if (count($invites) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
            return;
        }
        $invite = $invites[0];

        if ($invite->getPlayer()->getSession()->getFaction() !== $invite->getFaction()) {
            $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $invite->getPlayer()->getName());
            return;
        }
        $inviter = $invite->getPlayer();
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($inviter->getSession()->getFaction());

        if ($faction->getRole($inviter->getName()) === Faction::MEMBER) {
            $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $inviter->getName());
            return;
        }

        if ($inviter->isOnline()) {
            $inviter->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' accepted invitation for join in your faction'));
        }
        $sender->sendMessage(TextFormat::colorize('&aYou have accepted ' . $inviter->getName() . '\' invite for join in faction'));
        
        $faction->addRole($sender->getXuid(), Faction::MEMBER);
        $faction->announce(TextFormat::colorize('&a' . $sender->getName() . ' joined the faction'));

        $sender->getSession()->setFaction($faction->getName());

        HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $inviter->getName());
    }
}