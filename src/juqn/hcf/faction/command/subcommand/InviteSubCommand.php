<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class InviteSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class InviteSubCommand implements FactionSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&cYou don\' have a faction'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if (!in_array($faction->getRole($sender->getXuid()), [Faction::LEADER, Faction::CO_LEADER, Faction::CAPTAIN])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader, co-leader or captain of the invite member'));
            return;
        }

        if (count($faction->getRoles()) === HCFLoader::getInstance()->getConfig()->get('faction.max.members', 8)) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction have max players'));
            return;
        }
        
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f invite [player]'));
            return;
        }
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found!'));
            return;
        }
        
        if ($player->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThe player already has a faction'));
            return;
        }

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize("&cYou can't use this with regeneration time active!"));
            return;
        }
        HCFLoader::getInstance()->getFactionManager()->createInvite($sender, $player);
        $player->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' has invited you to join ' . $sender->getSession()->getFaction() . ' faction'));
        $sender->sendMessage(TextFormat::colorize('&aYou have invited ' . $player->getName() . ' to join your faction'));
    }
}