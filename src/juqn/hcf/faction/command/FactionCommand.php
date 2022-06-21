<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command;

use juqn\hcf\faction\command\subcommand\AcceptInviteSubCommand;
use juqn\hcf\faction\command\subcommand\CreateSubCommand;
use juqn\hcf\faction\command\subcommand\ClaimForSubCommand;
use juqn\hcf\faction\command\subcommand\ClaimSubCommand;
use juqn\hcf\faction\command\subcommand\FocusSubCommand;
use juqn\hcf\faction\command\subcommand\HelpSubCommand;
use juqn\hcf\faction\command\subcommand\HomeSubCommand;
use juqn\hcf\faction\command\subcommand\InviteSubCommand;
use juqn\hcf\faction\command\subcommand\RallySubCommand;
use juqn\hcf\faction\command\subcommand\SetHomeSubCommand;
use juqn\hcf\faction\command\subcommand\TopSubCommand;
use juqn\hcf\faction\command\subcommand\UnfocusSubCommand;
use juqn\hcf\faction\command\subcommand\UnrallySubCommand;
use juqn\hcf\faction\command\subcommand\WhoSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class FactionCommand
 * @package juqn\hcf\faction\command
 */
class FactionCommand extends Command
{
    
    /** @var FactionSubCommand[] */
    private array $subCommands = [];
    
    /**
     * FactionCommand construct.
     */
    public function __construct()
    {
        parent::__construct('faction', 'Faction commands');
        $this->setAliases(['f']);
        
        $this->subCommands['accept'] = new AcceptInviteSubCommand;
        $this->subCommands['create'] = new CreateSubCommand;
        $this->subCommands['claimfor'] = new ClaimForSubCommand;
        $this->subCommands['claim'] = new ClaimSubCommand;
        $this->subCommands['focus'] = new FocusSubCommand;
        $this->subCommands['help'] = new HelpSubCommand;
        $this->subCommands['home'] = new HomeSubCommand;
        $this->subCommands['hq'] = new HomeSubCommand;
        $this->subCommands['rally'] = new RallySubCommand;
        $this->subCommands['sethome'] = new SetHomeSubCommand;
        $this->subCommands['sethq'] = new SetHomeSubCommand;
        $this->subCommands['top'] = new TopSubCommand;
        $this->subCommands['unfocus'] = new UnfocusSubCommand;
        $this->subCommands['unrally'] = new UnrallySubCommand;
        $this->subCommands['who'] = new WhoSubCommand;
        $this->subCommands['invite'] = new InviteSubCommand();
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f help'));
            return;
        }
        $subCommand = $this->subCommands[$args[0]] ?? null;
        
        if ($subCommand === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        array_shift($args);
        $subCommand->execute($sender, $args);
    }
}