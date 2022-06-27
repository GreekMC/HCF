<?php

declare(strict_types=1);

namespace juqn\hcf\command\discord;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class FwhoCommand
 * @package juqn\hcf\command\discord
 */
class FwhoCommand extends Command
{
    
    /**
     * GodCommand construct.
     */
    public function __construct()
    {
        parent::__construct('fwho');
        $this->setPermission('fwho.discord.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;

        $faction = null;

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($args[0])) {
            $faction = $args[0];
        }

        if ($faction === null) {

            $webHook = new Webhook("https://discord.com/api/webhooks/991050448213520425/WI3NiRcVR-IO1Imw48ngdpXzkDx5NGrigBqKO9tu_w7lmUlGjYQUZGdSS6UQk1JBKS-x");

            $msg = new Message();

            $embed = new Embed();
            $embed->setTitle("No faction found");
            $embed->setColor(0x9E0101 );
            $msg->addEmbed($embed);
            $webHook->send($msg);
            return;
        }

        $generalinfo  = '' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' [' . count(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/' . count(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembers()) . ']' . PHP_EOL;

        $hq = (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ' . PHP_EOL);

        $leaders =  implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
            }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::LEADER))) . PHP_EOL;
        $coliders = implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
            }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CO_LEADER))) . PHP_EOL;
        $captains = implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
            }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CAPTAIN))) . PHP_EOL;
        $members = implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
            }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::MEMBER))) . PHP_EOL;
        $balance = HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . " $" . PHP_EOL;
        $deathsuntilraidable = (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '' : (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? ' ' : '')) . round(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . PHP_EOL;

        $timeuntilregen = gmdate('H:i:s', HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . PHP_EOL;

        $points = HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . PHP_EOL;
        $koths = HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures() . PHP_EOL;

        $webHook = new Webhook("https://discord.com/api/webhooks/991050448213520425/WI3NiRcVR-IO1Imw48ngdpXzkDx5NGrigBqKO9tu_w7lmUlGjYQUZGdSS6UQk1JBKS-x");

        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("Faction Information");
        $embed->setColor(0xFC7308);

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $embed->setDescription('**Faction** 👥' . PHP_EOL . $generalinfo . "**HQ** 📍" . PHP_EOL . $hq . "**Leaders** 👑" . PHP_EOL . $leaders . "**Coleaders** 🛠️" . PHP_EOL . $coliders . "**Captains** 🔨" . PHP_EOL . $captains . "**Members** 👥" . PHP_EOL . $members . "**Balance** 💰" . PHP_EOL . $balance . "**Deaths until Raidable** ♦️" . PHP_EOL . $deathsuntilraidable . "**Time Until Regen** ⏰" . PHP_EOL . $timeuntilregen . "**Points** 🍎" . PHP_EOL . $points . "**KoTH Captures** 🏔️" . PHP_EOL . $hq);
            $embed->setFooter("greekmc.net");
            $msg->addEmbed($embed);

            $webHook->send($msg);
        }
        $embed->setDescription('**Faction** 👥' . PHP_EOL . $generalinfo . "**HQ** 📍" . PHP_EOL . $hq . "**Leaders** 👑" . PHP_EOL . $leaders . "**Coleaders** 🛠️" . PHP_EOL . $coliders . "**Captains** 🔨" . PHP_EOL . $captains . "**Members** 👥" . PHP_EOL . $members . "**Balance** 💰" . PHP_EOL . $balance . "**Deaths until Raidable** ♦️" . PHP_EOL . $deathsuntilraidable . "**Points** 🍎" . PHP_EOL . $points . "**KoTH Captures** 🏔️" . PHP_EOL . $hq);
        $embed->setFooter("greekmc.net");
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }
}