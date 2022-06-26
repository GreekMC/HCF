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
        
        if (isset($args[0])) {
            $factionName = (string) $args[0];
            $playerInvites = HCFLoader::getInstance()->getFactionManager()->getInvites($sender->getXuid());
            
            if ($playerInvites === null || count($playerInvites) === 0) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
                return;
            }
            $invites = array_filter($playerInvites, function (FactionInvite $invite): bool {
                return $invite->getTime() > time();
            });

            if (!isset($invites[$factionName])) {
                $sender->sendMessage(TextFormat::colorize('&cYou have no invites from this faction'));
                return;
            }
            $invite = $invites[$factionName];
            
            if ($invite->getTime() < time()) {
                $sender->sendMessage(TextFormat::colorize('&cThis invite has already expired'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            
            if ($invite->getPlayer()->getSession()->getFaction() !== $invite->getFaction()) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($factionName);
    
            if ($faction->getRole($invite->getPlayer()->getName()) === Faction::MEMBER) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            $player = $invite->getPlayer();
            
            if ($player->isOnline()) {
                $player->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' accepted invitation for join in your faction'));
            }
            $sender->sendMessage(TextFormat::colorize('&aYou have accepted ' . $player->getName() . '\' invite for join in faction'));
            
            $faction->addRole($sender->getXuid(), Faction::MEMBER);
            $faction->announce(TextFormat::colorize('&a' . $sender->getName() . ' joined the faction'));
            $faction->setDtr(0.01 + (count($faction->getMembers()) * 1.00));
            
            $sender->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' ' . ($faction->getDtr() === (count($faction->getMembers()) + 0.1) ? '&a' : '&c') . $faction->getDtr() . '■&6]'));
            $sender->getSession()->setFaction($faction->getName());
    
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
            return;
        }
        $playerInvites = HCFLoader::getInstance()->getFactionManager()->getInvites($sender->getXuid());
        
        if ($playerInvites === null || count($playerInvites) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
            return;
        }
        $invites = array_values(array_filter($playerInvites, function (FactionInvite $invite): bool {
            return $invite->getTime() > time();
        }));
        
        if (count($invites) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
            return;
        }
        $invite = $invites[0];
        
        if ($invite->getPlayer()->getSession()->getFaction() !== $invite->getFaction()) {
            $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $invite->getFaction());
            return;
        }
        $inviter = $invite->getPlayer();
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($inviter->getSession()->getFaction());

        if ($faction->getRole($inviter->getName()) === Faction::MEMBER) {
            $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
            HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $faction->getName());
            return;
        }

        if ($inviter->isOnline()) {
            $inviter->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' accepted invitation for join in your faction'));
        }
        $sender->sendMessage(TextFormat::colorize('&aYou have accepted ' . $inviter->getName() . '\' invite for join in faction'));
        
        $faction->addRole($sender->getXuid(), Faction::MEMBER);
        $faction->announce(TextFormat::colorize('&a' . $sender->getName() . ' joined the faction'));
        
        $sender->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' ' . ($faction->getDtr() === (count($faction->getMembers()) + 0.1) ? '&a' : '&c') . $faction->getDtr() . '■&6]'));
        $sender->getSession()->setFaction($faction->getName());

        HCFLoader::getInstance()->getFactionManager()->removeInvite($sender, $faction->getName());
    }
}