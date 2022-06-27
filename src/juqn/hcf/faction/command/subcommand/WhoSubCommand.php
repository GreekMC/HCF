<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WhoSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $faction = null;

        if (!isset($args[0])) {
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction'));
                return;
            }
            $faction = $sender->getSession()->getFaction();
        } else {
            $target = $sender->getServer()->getPlayerByPrefix($args[0]);

            if ($target instanceof Player) {
                if ($target->getSession()->getFaction() === null) {
                    $sender->sendMessage(TextFormat::colorize('Player dont have faction'));
                    return;
                }
                $faction = $target->getSession()->getFaction();
            } else {
                if (HCFLoader::getInstance()->getFactionManager()->getFaction($args[0])) {
                    $faction = $args[0];
                }
            }
        }

        if ($faction === null) {
            $sender->sendMessage(TextFormat::colorize('&cNo faction found'));
            return;
        }
        $message = '&7&m--------------------------' . PHP_EOL;
        $message .= '&9' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' &7[' . count(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/' . count(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembers()) . '] &3- &eHQ: &f' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ');
        $leaders = 
        $message .=  PHP_EOL . '&eLeader: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::LEADER))) . PHP_EOL;
        $message .= '&eColeaders: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CO_LEADER))) . PHP_EOL;
        $message .= '&eCaptains: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CAPTAIN))) . PHP_EOL;
        $message .= '&eMembers: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::MEMBER))) . PHP_EOL;
        $message .= '&eBalance: &9$' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . PHP_EOL;
        $message .= '&eDeaths until Raidable: ' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '&a' : (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? '&c' : '&e')) . round(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . PHP_EOL;

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $message .= '&eTime Until Regen: &9' . gmdate('H:i:s', HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . PHP_EOL;
        }
        $message .= '&ePoints: &c' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . PHP_EOL;
        $message .= '&eKoTH Captures: &c' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures() . PHP_EOL;
        $message .= '&7&m--------------------------';

        $sender->sendMessage(TextFormat::colorize($message));
    }
}