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
class FtopCommand extends Command
{

    /**
     * GodCommand construct.
     */
    public function __construct()
    {
        parent::__construct('ftop');
        $this->setPermission('ftop.discord.command');
    }

    /**
     * @return array
     */
    private function getFactions(): array
    {
        $points = [];

        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            if (in_array($faction->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn']))
                continue;
            $points[$name] = $faction->getPoints();
        }
        return $points;
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

        $webHook = new Webhook("https://discord.com/api/webhooks/991109809745174638/J6yK3ZHQ9dY-r6VWJrXHgt9eR09JNlzMzGUibCBE_YoZmSePncxgqKNCN4W8v32KxU2O");

        $msg = new Message();

        $data = $this->getFactions();
        arsort($data);
        $message = "";

        for ($i = 0; $i < 10; $i++) {
            $position = $i + 1;
            $factions = array_keys($data);
            $points = array_values($data);

            if (isset($factions[$i]))
                $message .= TextFormat::colorize(PHP_EOL . '' . $position . '. ' . $factions[$i] . ' ✾ **Points:** ' . $points[$i] . '');
        }

        $embed = new Embed();
        $embed->setTitle("Top Factions (Points) ⚔");
        $embed->setColor(0xFC7308);
        $embed->setDescription("{$message}");
        $embed->setFooter("greekmc.net");
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }
}