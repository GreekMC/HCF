<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class RallySubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class WithdrawSubCommand implements FactionSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&cUse /f withdraw [amount | all]'));
            return;
        }

        $cantidad = $args[0];

        if ($cantidad < 0) {
            return;
        }

        if (($cantidad) === "all") {
            $sender->sendMessage('§cUse /f withdraw [amount | all]');
            $sender->getSession()->setBalance($sender->getSession()->getBalance() + $faction->getBalance());
            $faction->setBalance(0);
            return;
        }

        if (!is_numeric($cantidad)) {
            $sender->sendMessage('§cUse /f withdraw [amount | all]');
            return;
        }

        if($faction->getBalance() >= $cantidad) {
            $sender->getSession()->setBalance($sender->getSession()->getBalance() + (int)$cantidad);
            $sender->sendMessage('§aYour new balance is ' . $sender->getSession()->getBalance());
            $faction->setBalance($faction->getBalance() - (int)$cantidad);
        }else{
            $sender->sendMessage('§cThe amount you entered exceeds your faction balance!');
        }
    }
}
