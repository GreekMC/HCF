<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DisbandAllSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission('disbandall.permission')) {
            return;
        }
        
        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $faction) {
            $faction->disband();
        }
        /*$faction = HCFLoader::getInstance()->getFactionManager()->getFaction($name);
        $faction->disband();
        HCFLoader::getInstance()->getFactionManager()->removeFaction($name);*/
        $sender->sendMessage(TextFormat::colorize('&aThe ' . $name .' &afaction was disbanded'));
        $webHook = new Webhook("https://discord.com/api/webhooks/992183190963888168/MN_PukFcNaIWtUIsBPD96okyemazZnBS8IFI2ov7_VwN2PD-ZgIhxUNWvUUanO06y_gh");
        $msg = new Message();
        $msg->setContent('Force disband all by  **' . $sender->getName() . '**');
        $webHook->send($msg);
    }
}
