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

        if ($koth->getName() !== "§r§5§lCitadel§r") {
            HCFLoader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

            $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('koth.webhook'));

            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7██&3█&7██ &r&6[KingOfTheHill]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3███&7███ &r&9" . $koth->getName() . " &ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7██&3█&7██ &r&6[KingOfTheHill] &eWin the event and get &9x"  . $koth->getKeyCount()  . " ". $koth->getKey() . " Key&e!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));


            $msg = new Message();

            $embed = new Embed();
            $embed->setTitle("KotH " . $name . " has started 🏔️");
            $embed->setColor(0x9AD800);
            $embed->addField("Location 📍", "{$location}");
            $embed->addField("Time 🕐", "{$time} minutes", true);
            $embed->addField("Rewards 🔑", "{$points} Points & {$keys} Keys", true);
            $embed->setFooter("greekmc.net");
            $msg->addEmbed($embed);


            $webHook->send($msg);
        }
        if ($koth->getName() === "§r§5§lCitadel§r") {
            HCFLoader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

            $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('koth.webhook'));

            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[Citadel]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6&eWin the event and get &9x"  . $koth->getKeyCount()  . " ". $koth->getKey() . " Key&e!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));


            $msg = new Message();

            $msg->setContent("@everyone");

            $embed = new Embed();
            $embed->setTitle("Citadel has started 🌌");
            $embed->setColor(0xC13DFF);
            $embed->addField("Location 📍", "{$location}");
            $embed->addField("Time 🕐", "{$time} minutes", true);
            $embed->addField("Rewards 🔑", "{$points} Points & {$keys} Keys", true);
            $embed->setFooter("greekmc.net");
            $msg->addEmbed($embed);


            $webHook->send($msg);
        }
    }
}