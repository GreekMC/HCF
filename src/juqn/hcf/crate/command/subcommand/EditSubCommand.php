<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command\subcommand;

use juqn\hcf\crate\command\CrateSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\utils\Inventories;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class EditSubCommand
 * @package juqn\hcf\crate\command\subcommand
 */
class EditSubCommand implements CrateSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /crate edit [string: crateName] [string: keyId:keyFormat:nameFormat:items]'));
            return;
        }
        $crateName = $args[0];
        $type = $args[1];
        
        $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($crateName);
        
        if ($crate === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        
        switch($type) {
            case 'keyId':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' keyId [string: id]'));
                    return;
                }
                $key = explode(':', $args[2]);
                
                if (!is_numeric($key[0])) {
                    $sender->sendMessage(TextFormat::colorize('&cUse numbers to select the item id'));
                    return;
                }
                
                if (isset($key[1]) && !is_numeric($key[1])) {
                    $sender->sendMessage(TextFormat::colorize('&cUse numbers to select the item meta'));
                    return;
                }
                $crate->setKeyId($args[2]);
                $sender->sendMessage(TextFormat::colorize('&akeyId of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'keyFormat':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' keyFormat [string: format]'));
                    return;
                }
                $keyFormat = $args[2];
                
                $crate->setKeyFormat($keyFormat);
                $sender->sendMessage(TextFormat::colorize('&akeyFormat of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'nameFormat':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' nameFormat [string: format]'));
                    return;
                }
                $nameFormat = $args[2];
                
                $crate->setNameFormat($nameFormat);
                $sender->sendMessage(TextFormat::colorize('&anameFormat of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'items':
                Inventories::editCrateContent($sender, $crateName);
                break;
                
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' [string: keyId:keyFormat:nameFormat:items]'));
                break;
        }
    }
}