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
        $message .= '&9' . ($faction->getName()). ' &7[' . count($faction->getOnlineMembers()) . '/' . count($faction->getMembers()) . '] &3- &eHQ: &f' . ($faction->getHome() !== null ? 'X: ' . $faction->getHome()->getFloorX() . ' Z: ' . $faction->getHome()->getFloorZ() : 'Not set ');
        $message .= '&eLeader: ' . implode(', ', array_map(function ($session) {
            return $session->getName();
        }, $faction->getMembersByRole(Faction::LEADER))) . PHP_EOL;
        $message .= '&eColeaders: ' . implode(', ', array_map(function ($session) {
            return $session->getName();
        }, $faction->getMembersByRole(Faction::CO_LEADER))) . PHP_EOL;
        $message .= '&eCaptains: ' . implode(', ', array_map(function ($session) {
            return $session->getName();
        }, $faction->getMembersByRole(Faction::CAPTAIN))) . PHP_EOL;
        $message .= '&eMembers: ' . implode(', ', array_map(function ($session) {
            return $session->getName();
        }, $faction->getMembersByRole(Faction::MEMBER))) . PHP_EOL;
        $message .= '&eBalance: &9$' . $faction->getBalance() . PHP_EOL;
        $message .= '&eDeaths until Raidable: ' . ($faction->getDtr() >= $faction->getMaxDtr() ? '&a' : ($faction->getDtr() <= 0.00 ? '&c' : '&e')) . round($faction->getDtr(), 2) . '■' . PHP_EOL;
        
        if ($faction->getTimeRegeneration() !== null) {
            $message .= '&eTime Until Regen: &9' . gmdate('H:i:s', $faction->getTimeRegeneration()) . PHP_EOL;
        }
        $message .= '&ePoints: &c' . $faction->getPoints() . PHP_EOL;
        $message .= '&eKoTH Captures: &c' . $faction->getKothCaptures() . PHP_EOL;
        $message .= '&7&m--------------------------';
        
        $sender->sendMessage(TextFormat::colorize($message));
    }
}