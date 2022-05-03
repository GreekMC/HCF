<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command;

use juqn\hcf\koth\command\subcommand\AddKeySubCommand;
use juqn\hcf\koth\command\subcommand\ClaimSubCommand;
use juqn\hcf\koth\command\subcommand\CreateSubCommand;
use juqn\hcf\koth\command\subcommand\DeleteSubCommand;
use juqn\hcf\koth\command\subcommand\ListSubCommand;
use juqn\hcf\koth\command\subcommand\SetCapzoneSubCommand;
use juqn\hcf\koth\command\subcommand\SetCoordsSubCommand;
use juqn\hcf\koth\command\subcommand\SetPointsSubCommand;
use juqn\hcf\koth\command\subcommand\StartSubCommand;
use juqn\hcf\koth\command\subcommand\StopSubCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class KothCommand
 * @package juqn\hcf\koth\command
 */
class KothCommand extends Command
{
    
    /** @var KothSubCommand[] */
    private array $subCommands = [];
    
    /**
     * KothCommand construct.
     */
    public function __construct()
    {
        parent::__construct('koth', 'Koth commands');
        $this->setPermission('koth.command');
        
        $this->subCommands['addkey'] = new AddKeySubCommand;
        $this->subCommands['claim'] = new ClaimSubCommand;
        $this->subCommands['create'] = new CreateSubCommand;
        $this->subCommands['delete'] = new DeleteSubCommand;
        $this->subCommands['list'] = new ListSubCommand;
        $this->subCommands['setcapzone'] = new SetCapzoneSubCommand;
        $this->subCommands['setcoords'] = new SetCoordsSubCommand;
        $this->subCommands['setpoints'] = new SetPointsSubCommand;
        $this->subCommands['start'] = new StartSubCommand;
        $this->subCommands['stop'] = new StopSubCommand;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            return;
        }
        $subCommand = $this->subCommands[$args[0]] ?? null;
        
        if ($subCommand === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        
        if ($args[0] !== 'list' && !$this->checkPermissionByCommand($sender, $args[0])) {
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
        return $player->hasPermission('koth.command.' . $command);
    }
}