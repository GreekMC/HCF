<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListSubCommand implements FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        $factions = array_filter(array_values(HCFLoader::getInstance()->getFactionManager()->getFactions()), function (Faction $faction): bool {
            $types = Faction::getTypes();
            return !isset($types[$faction->getName()]);
        });
        uasort($factions, function (Faction $firstFaction, Faction $secondFaction) {
            return count($firstFaction->getOnlineMembers()) <=> count($secondFaction->getOnlineMembers());
        });
        
        if (count($factions) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cNo factions created'));
            return;
        }
        $page = 0;
        
        if (isset($args[0])) {
            if (!is_numeric($args[0])) {
                $sender->sendMessage(TextFormat::colorize('&cNumber invalid'));
                return;
            }
            $a = intval($args[0]);
            
            if ($a <= 0) {
                $sender->sendMessage(TextFormat::colorize('&cPage invalid'));
                return;
            }
            $page = $a - 1;
        }
        $pages = array_chunk($factions, 10, true);
        
        if (isset($pages[$page])) {
            $list = $pages[$page];
            $message = TextFormat::colorize('&6Faction List &7[' . ($page + 1) . '/' . count($pages));
            
            foreach ($pages[$page] as $faction) {
                $message .= TextFormat::colorize(PHP_EOL . '&6'  . $faction->getName() . '&7: &eDTR: ' . $faction->getDtr() . ' &7[' . count($faction->getOnlineMembers()) . '/' . count($faction->getMembers()) . ']');
            }
            $sender->sendMessage($message);
        }
    }
}