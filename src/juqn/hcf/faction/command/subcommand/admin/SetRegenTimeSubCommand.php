<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetRegenTimeSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission('setregentime.permission')) {
            return;
        }
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setregentime [string: name]  [int: time]'));
            return;
        }
        if (!is_numeric($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setregentime [string: name]  [int: time]'));
            return;
        }

        $name = $args[0];
        $time = $args[1];

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to change the dtr'));
            return;
        }
        HCFLoader::getInstance()->getFactionManager()->getFaction($name)->setTimeRegeneration($time * 60);
        $sender->sendMessage(TextFormat::colorize("&a" . $name . ' faction regen time is now ' . $time . " minutes"));
        $webHook = new Webhook("https://discord.com/api/webhooks/992183190963888168/MN_PukFcNaIWtUIsBPD96okyemazZnBS8IFI2ov7_VwN2PD-ZgIhxUNWvUUanO06y_gh");
        $msg = new Message();
        $msg->setContent('**' . $name . '** faction regen time is now **' . $time . " minutes**" . ' by staff **' . $sender->getName() . '**');
        $webHook->send($msg);
    }
}
