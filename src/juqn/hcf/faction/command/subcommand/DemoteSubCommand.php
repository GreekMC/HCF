<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DemoteSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
    
        if (!in_array($faction->getRole($sender->getXuid()), [Faction::LEADER, Faction::CO_LEADER])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader or co-leader of the invite member'));
            return;
        }
    
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f promote [player]'));
            return;
        }
        $session = null;
        $p = null;
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
            
        if ($player instanceof Player) {
            if ($player->getId() === $sender->getId()) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\'t promote yourself'));
                return;
            }
                
            if ($player->getSession()->getFaction() !== $faction->getName()) {
                $sender->sendMessage(TextFormat::colorize('&cThe player is not a member'));
                return;
            }
            $session = $player->getSession();
            $p = $player;
        } else {
            $members = $faction->getMembers();
                
            foreach ($members as $member) {
                if ($member->getName() === $args[0]) {
                    $session = $member;
                    break;
                }
            }
                
            if ($session === null) {
                $sender->sendMessage(TextFormat::colorize('&cMember not found'));
                return;
            }
        }

        if ($faction->getRole($session->getXuid()) === Faction::MEMBER) {
            return;
        }
        $roles = [
            Faction::CO_LEADER => Faction::CAPTAIN,
            Faction::CAPTAIN => Faction::MEMBER
        ];

        if ($faction->getRole($sender->getXuid()) === Faction::CO_LEADER) {
            if ($faction->getRole($session->getXuid()) === Faction::LEADER || $faction->getRole($session->getXuid()) === Faction::CO_LEADER) {
                return;
            }
        }
        $faction->addRole($session->getXuid(), $roles[$faction->getRole($session->getXuid())]);
    }
}