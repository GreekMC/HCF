<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PayCommand extends Command
{

    public function __construct()
    {
        parent::__construct('pay', 'Use command for pay');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        
        if (!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /pay [player] [money]'));
            return;
        }
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found'));
            return;
        }
        
        if (!is_numeric($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cWrite an amount in numbers'));
            return;
        }
        $money = intval($args[1]);
        
        if ($sender->getSession()->getBalance() === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou have no money'));
            return;
        }
        $result = $sender->getSession()->getBalance() - $money;
        
        if ($result < 0) {
            $sender->sendMessage(TextFormat::colorize("&cYou don\'t have enough money"));
            return;
        }
        $player->getSession()->setBalance($player->getSession()->getBalance() + $money);
        $sender->getSession()->setBalance($result);
        
        $player->sendMessage(TextFormat::colorize('&aYou received $' . $money . ' from ' . $sender->getName()));
        $sender->sendMessage(TextFormat::colorize('&aYou have sent $' . $money . ' to ' . $player->getName()));
    }
}