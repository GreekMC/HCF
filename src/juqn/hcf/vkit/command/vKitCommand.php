<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command;

use juqn\hcf\HCFLoader;
use juqn\hcf\utils\Inventories;
use juqn\hcf\vkit\command\subcommand\CreateSubCommand;
use juqn\hcf\vkit\command\subcommand\DeleteSubCommand;
use juqn\hcf\vkit\command\subcommand\EditSubCommand;
use juqn\hcf\vkit\command\subcommand\GiveSubCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class vKitCommand
 * @package juqn\hcf\vkit\command
 */
class vKitCommand extends Command
{
    
    /** @var vKitSubCommand[] */
    private array $subCommands = [];
    
    /**
     * vKitCommand construct.
     */
    public function __construct()
    {
        parent::__construct('vkit', 'vKit commands');
        
        $this->subCommands['create'] = new CreateSubCommand;
        $this->subCommands['delete'] = new DeleteSubCommand;
        $this->subCommands['edit'] = new EditSubCommand;
        $this->subCommands['give'] = new GiveSubCommand;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            Inventories::createvKitOrganization($sender);
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
     * @param string $command
     * @return bool
     */
    private function checkPermissionByCommand(CommandSender $player, string $command): bool
    {
        return $player->hasPermission('vkit.command.' . $command);
    }
}