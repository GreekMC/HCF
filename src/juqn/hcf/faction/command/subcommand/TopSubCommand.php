<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class TopSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class TopSubCommand implements FactionSubCommand
{
    
    /**
     * @return array
     */
    private function getFactions(): array
    {
        $points = [];
        
        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            if (in_array($faction->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn']))
                continue;
            $points[$name] = $faction->getPoints();
        }
        return $points;
    }
    
    /*
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        $data = $this->getFactions();
        arsort($data);
        
        $sender->sendMessage(TextFormat::colorize('&9Top factions &7(points)'));
        
        for ($i = 0; $i < 10; $i++) {
            $position = $i + 1;
            $factions = array_keys($data);
            $points = array_values($data);
            
            if (isset($factions[$i]))
                $sender->sendMessage(TextFormat::colorize('&7#' . $position . '. &e' . $factions[$i] . ' &7- &f' . $points[$i]));
        }
    }
}