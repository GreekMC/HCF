<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\faction\FactionManager;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class RallySubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class DisbandSubCommand implements FactionSubCommand
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
        if($sender->getSession()->getFaction()->getRole() === 'leader'){
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
            HCFLoader::getInstance()->getFactionManager()->removeFaction($sender->getSession()->getFaction());
            $members = $faction->getMembers();
        }else{
            $sender->sendMessage('&cOnly the leader can use this command!');
        }

    }
}
