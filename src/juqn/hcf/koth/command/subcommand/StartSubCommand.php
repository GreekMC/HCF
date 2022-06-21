<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command\subcommand;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\koth\command\KothSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class StartSubCommand
 * @package juqn\hcf\koth\command\subcommand
 */
class StartSubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&c/koth start [string: name]'));
            return;
        }
        $name = $args[0];
        
        if (HCFLoader::getInstance()->getKothManager()->getKothActive() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is already activated a koth right now'));
            return;
        }
        
        if (HCFLoader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }
        $koth = HCFLoader::getInstance()->getKothManager()->getKoth($name);
        $location = HCFLoader::getInstance()->getKothManager()->getKoth($name)->getCoords();
        $time = HCFLoader::getInstance()->getKothManager()->getKoth($name)->getTime() / 60;
        $points = HCFLoader::getInstance()->getKothManager()->getKoth($name)->getPoints();
        $keys = HCFLoader::getInstance()->getKothManager()->getKoth($name)->getKeyCount();
        
        if ($koth->getCapzone() === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe capzone is not selected'));
            return;
        }
        HCFLoader::getInstance()->getKothManager()->setKothActive($name);
        $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

        $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('koth.webhook'));


        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("KotH " . $name . " has started");
        $embed->setColor(0xD87200);
        $embed->addField("Location", "{$location}");
        $embed->addField("Time", "{$time} minutes", true);
        $embed->addField("Rewards", "{$points} Points & {$keys} Keys", true);
        $embed->setFooter("greekmc.net");
        $msg->addEmbed($embed);


        $webHook->send($msg);
    }
}