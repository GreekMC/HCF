<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DepositSubCommand implements FactionSubCommand
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
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());

        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f deposit [amount | all]'));
            return;
        }
        $cantidad = $args[0];

        if ($cantidad < 0) {
            return;
        }

        if (($cantidad) === "all") {
            $faction->setBalance($faction->getBalance() + $sender->getSession()->getBalance());
            $sender->sendMessage('§aThe new balance of the faction is ' . $faction->getBalance() . '$');
            $sender->getSession()->setBalance(0);
            return;
        }

        if (!is_numeric($cantidad)) {
            $sender->sendMessage('§cUse /f deposit [amount | all]');
            return;
        }

        if($sender->getSession()->getBalance() >= $cantidad) {
            $faction->setBalance($faction->getBalance() + (int)$cantidad);
            $sender->sendMessage('§aThe new balance of the faction is ' . $faction->getBalance() . '$');
            $sender->getSession()->setBalance($sender->getSession()->getBalance() - (int)$cantidad);
        }else{
            $sender->sendMessage('§cThe amount you entered exceeds your balance!');
        }
    }
}
