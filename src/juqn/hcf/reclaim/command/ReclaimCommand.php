<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim\command;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\reclaim\command\subcommand\CreateSubCommand;
use juqn\hcf\reclaim\command\subcommand\DeleteSubCommand;
use juqn\hcf\reclaim\command\subcommand\EditSubCommand;

use juqn\hcf\utils\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class ReclaimCommand
 * @package juqn\hcf\reclaim\command
 */
class ReclaimCommand extends Command
{
    
    /** @var ReclaimSubCommand[] */
    private array $subCommands = [];
    
    /**
     * ReclaimCommand construct.
     */
    public function __construct()
    {
        parent::__construct('reclaim', 'Reclaim commands');
        
        $this->subCommands['create'] = new CreateSubCommand;
        $this->subCommands['delete'] = new DeleteSubCommand;
        $this->subCommands['edit'] = new EditSubCommand;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (!isset($args[0])) {
            $reclaimManager = HCFLoader::getInstance()->getReclaimManager();
            
            foreach ($reclaimManager->getReclaims() as $reclaim) {
                if ($sender->hasPermission($reclaim->getPermission())) {
                    if ($sender->getSession()->getCooldown($reclaim->getName() . '.reclaim') === null) {
                        $reclaim->giveContent($sender);
                        $sender->getSession()->addCooldown($reclaim->getName() . '.reclaim', "", $reclaim->getTime(), false, false);
                        Server::getInstance()->broadcastMessage(TextFormat::colorize("&f" . $sender->getName() . " &7has redeemed their " . $reclaim->getName() . " &7rewards using &o&f/reclaim"));
                    } else {
                        $sender->sendMessage(TextFormat::colorize("&cYou need wait " . Timer::convert($sender->getSession()->getCooldown($reclaim->getName() . '.reclaim')->getTime()) . " for use this!"));
                    }
                }
            }
            return;
        }
        $subCommand = $this->subCommands[$args[0]] ?? null;
        
        if ($subCommand === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        
        if (!$this->checkPermissionByCommand($sender, $args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cYou do not have permission to use this command'));
            return;
        }
        array_shift($args);
        $subCommand->execute($sender, $args);
    }

    /**
     * @param CommandSender $player
     * @param string $command
     * @return bool
     */
    private function checkPermissionByCommand(CommandSender $player, string $command): bool
    {
        return $player->hasPermission('reclaim.command.' . $command);
    }
}