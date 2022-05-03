<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command\subcommand;

use juqn\hcf\koth\command\KothSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class ListSubCommand
 * @package juqn\hcf\koth\command\subcommand
 */
class ListSubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $kothManager = HCFLoader::getInstance()->getKothManager();
        $sender->sendMessage(TextFormat::colorize('&e× KOTH LIST ×'));
        
        foreach ($kothManager->getKoths() as $name => $koth) {
            $sender->sendMessage(TextFormat::colorize('&e' . $name . ' &f' . ($koth->getCoords() ?? 'None')));
        }
    }
}