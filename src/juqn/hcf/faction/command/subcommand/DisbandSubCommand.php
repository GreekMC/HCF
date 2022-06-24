<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\entity\CustomItemEntity;
use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\faction\FactionManager;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddItemActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class RallySubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class DisbandSubCommand implements FactionSubCommand
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

        if ($faction->getRole($sender->getXuid()) !== Faction::LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader or co-leader can disband the faction'));
            return;
        }
        if (HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize("&cYou can't use this with regeneration time active!"));
            return;
        }
        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                if ($online_player instanceof Player)
                    if ($online_player->getSession()->getFaction() === $sender->getSession()->getFaction()) {
                        $online_player->setScoreTag("");
                    }
        }
        $faction->disband();
        HCFLoader::getInstance()->getFactionManager()->removeFaction($faction->getName());
        $sender->sendMessage("§cThe factions has disbanded");

    }
}
