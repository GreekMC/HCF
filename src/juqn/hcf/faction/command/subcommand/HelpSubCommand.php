<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class HelpSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class HelpSubCommand implements FactionSubCommand
{
    
    /*
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $sender->sendMessage(TextFormat::colorize('&7---------------------'));
        $sender->sendMessage(TextFormat::colorize('&9&lFaction Help'));
        $sender->sendMessage(TextFormat::colorize('&7---------------------'));
        $sender->sendMessage(TextFormat::colorize(' '));
        $sender->sendMessage(TextFormat::colorize('&r&9General Commands:'));
        $sender->sendMessage(TextFormat::colorize('  '));
        $sender->sendMessage(TextFormat::colorize('&r&e/f create <teamName> &7- Create a new team' . PHP_EOL . '&r&e/f (accept | join) &7- Accept a pending invitation' . PHP_EOL . '&r&e/f leave &7- Leave your corrent team' . PHP_EOL . '&r&e/f (home | hq) &7- Teleport to your team home' . PHP_EOL . '&r&e/f stuck &7 Teleport out of enemy territory' . PHP_EOL . '&r&e/f (deposit | d) <amount | all> &7- Deposit money into your team balance' . PHP_EOL . '&r&e/f (chat|c) (f|p|a) &7- It switches you to the faction, public and ally chat if your faction has ally'));
        $sender->sendMessage(TextFormat::colorize('    '));
        $sender->sendMessage(TextFormat::colorize('&9Information Commands:'));
        $sender->sendMessage(TextFormat::colorize('      '));
        $sender->sendMessage(TextFormat::colorize('&r&e/f (who | show | info | i) <player | teamName> &7- Display team information' . PHP_EOL . '&r&e/f map &7- Show nearby claims (identified by pillars)' . PHP_EOL . '&r&e/f top &7- Shows the 10 teams with the most points on the map'));
        $sender->sendMessage(TextFormat::colorize('            '));
        $sender->sendMessage(TextFormat::colorize('&9Coleader Commands:'));
        $sender->sendMessage(TextFormat::colorize('        '));
        $sender->sendMessage(TextFormat::colorize('&r&e/f invite <player> &7- Invite a player to your team' . PHP_EOL . '&r&e/f (withdraw | w) <amount> &7- Withdraw money yout team balance' . PHP_EOL . '&r&e/f (sethome | sethq) &7- Set your team home at your current location'));
        $sender->sendMessage(TextFormat::colorize('          '));
        $sender->sendMessage(TextFormat::colorize('&9Leader Commands:'));
        $sender->sendMessage(TextFormat::colorize('               '));
        $sender->sendMessage(TextFormat::colorize('&r&e/f promote <player> &7- Add or remove the tail of your faction member' . PHP_EOL . '&r&e/f unclaim &7- Unclaim land ' . PHP_EOL . '&r&e/f claim &7- Claim land' . PHP_EOL . '&r&e/f disband &7- Disband your team' . PHP_EOL . '&r&e/f ally (add|accept|remove|chat) &7- Ally commands'));
        $sender->sendMessage(TextFormat::colorize('&7---------------------'));
    }
}