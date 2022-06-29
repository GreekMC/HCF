<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class SetStrikesSubCommand
 * @package juqn\hcf\faction\command\subcommand\admin
 */
class SetStrikesSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (!$sender->hasPermission('setstrikes.permission')) {
            return;
        }
        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setstrikes [string: name] [int: strikes] [string: motive]'));
            return;
        }
        if (!is_numeric($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setstrikes [string: name] [int: strikes] [string: motive]'));
            return;
        }

        $name = $args[0];
        $strikes = $args[1];
        $motive = $args[2];

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to add strikes'));
            return;
        }
        HCFLoader::getInstance()->getFactionManager()->getFaction($name)->setStrikes($strikes * 1);
        $sender->sendMessage(TextFormat::colorize('&aThe strikes of the faction ' . $name . ' is now ' . $strikes . ' to motive ' . $motive));


        $webHook = new Webhook("https://discord.com/api/webhooks/991712219958091819/WMYcEO9w-zjZdCQXAzL7oFVCe0olLhB3dRNvA-D2z4CHiI4xSwf3wmQfRFRox255xrj1");

        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("New Faction Strike");
        $embed->setColor(0x9AD800);
        $embed->addField("Faction 👥", "{$name}");
        $embed->addField("Strikes 🚨", "{$strikes}", true);
        $embed->addField("Motive 📢", "{$motive}", true);
        $embed->setFooter("greekmc.net");
        $msg->addEmbed($embed);


        $webHook->send($msg);
    }
}
