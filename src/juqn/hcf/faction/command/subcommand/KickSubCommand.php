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
class KickSubCommand implements FactionSubCommand
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
        
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f kick [player]'));
            return;
        }
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found!'));
            return;
        }
        if (HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage("§cYou can't use this with regeneration time active!");
            return;
        }
        if ($faction === HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction())) {
            $faction->removeRole($player->getXuid());
            $player->getSession()->setFaction(null);
            $player->sendMessage("§cYou were kicked out of your faction");
            $player->setScoreTag("");
            $sender->sendMessage("§cThe player was kicked from the faction");
            $faction->setDtr(0.01 + (count($faction->getMembers()) * 1.00));
            //Remover score tag
        }else{
            $sender->sendMessage("§cThe player is not in your faction");
        }
    }
}