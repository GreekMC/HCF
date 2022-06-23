<?php

declare(strict_types=1);

namespace juqn\hcf\crate\tile;

use juqn\hcf\entity\CustomItemEntity;
use juqn\hcf\entity\TextEntity;
use juqn\hcf\HCFLoader;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

/**
 * Class CrateTile
 * @package juqn\hcf\crate\tile
 */
class CrateTile extends Chest
{
    
    /** @var string|null */
    private ?string $crateName;
    /** @var TextEntity|null */
    private ?TextEntity $text = null;
    
    /**
     * @return string|null
     */
    public function getCrateName(): ?string
    {
        return $this->crateName;
    }
    
    /**
     * @return TextEntity|null
     */
    public function getText(): ?TextEntity
    {
        return $this->text;
    }
    
    /**
     * @param string|null $crateName
     */
    public function setCrateName(?string $crateName): void
    {
        $this->crateName = $crateName;
        $this->createText();
    }
    
    /**
     * @param TextEntity $text
     */
    public function setText(TextEntity $text): void
    {
        $this->text = $text;
    }
    
    private function createText(): void
    {
        if ($this->text === null && $this->crateName !== null) {
            $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getCrateName());
            
            if ($crate !== null) {
                $nbt = $this->saveNBT();
                $this->text = new TextEntity(new Location($this->getPosition()->getX() + 0.5, $this->getPosition()->getY() + 1.8, $this->getPosition()->getZ() + 0.5, $this->getPosition()->getWorld(), 0.0, 0.0), $nbt);
                $this->text->setNameTag(TextFormat::colorize("\n" . $crate->getNameFormat() . "\n&fLeft click for reward\n". "&fRight click to open\n" . "&r\n" . "&7play.greekmc.net\n" . ""));
                $this->text->spawnToAll();
                $entity = new CustomItemEntity(new Location($this->getPosition()->getX() + 0.5, $this->getPosition()->getY() + 3, $this->getPosition()->getZ() + 0.5, $this->getPosition()->getWorld(), 0.0, 0.0), ItemFactory::getInstance()->get($crate->getKeyId()));
                $entity->setPickupDelay(-1);
                $entity->spawnToAll();
            }
        }
    }
    
    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt) : void
    {
        parent::writeSaveData($nbt);
        $nbt->setString('crate_name', $this->getCrateName());
    }
    
    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void
    {
        parent::readSaveData($nbt);
        $this->crateName = $nbt->getString('crate_name');
        $this->createText();
    }
    
    /**
     * @param Player $player
     */
    public function openCratePreview(Player $player): void
    {
        if ($this->getCrateName() !== null) {
            $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getCrateName());
            
            if ($crate !== null) {
                $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
                $menu->getInventory()->setContents($crate->getItems());
                $menu->setListener(InvMenu::readonly());
                $menu->send($player, TextFormat::colorize('&7Crate ' . $this->getCrateName() . ' Preview'));
            }
        }
    }
    
    /**
     * @param Player $player
     */
    public function openCrateConfiguration(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        
        $update_text = ItemFactory::getInstance()->get(345, 0);
        $update_text->setCustomName(TextFormat::colorize('&eUpdate crate text'));
        $update_text->setNamedTag($update_text->getNamedTag()->setString('update_text', 'true'));
        
        $remove = ItemFactory::getInstance()->get(279, 0);
        $remove->setCustomName(TextFormat::colorize('&cRemove create tile'));
        $remove->setNamedTag($remove->getNamedTag()->setString('remove_tile', 'true'));
        
        $menu->getInventory()->setContents([
            12 => $update_text,
            14 => $remove
        ]);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $player = $transaction->getPlayer();
            
            if ($item->getNamedTag()->getTag('update_text') !== null) {
                if ($this->getCrateName() !== null) {
                    $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getCrateName());
                    
                    if ($crate !== null) {
                        $this->getText()->setNameTag(TextFormat::colorize("\n" . $crate->getNameFormat() . "\n&fLeft click for reward\n". "&fRight click to open\n" . "&r\n" . "&7play.greekmc.net\n" . ""));
                        $entity = new CustomItemEntity(new Location($this->getPosition()->getX() + 0.5, $this->getPosition()->getY() + 3, $this->getPosition()->getZ() + 0.5, $this->getPosition()->getWorld(), 0.0, 0.0), ItemFactory::getInstance()->get($crate->getKeyId()));
                        $entity->setPickupDelay(-1);
                        $entity->spawnToAll();
                        $player->sendMessage(TextFormat::colorize('&aThe text of the crate ' . $this->getCrateName() . ' has been updated'));
                    } else $player->sendMessage(TextFormat::colorize('&cThere is no crate that is defined in the Tile'));
                }
            }
            
            if ($item->getNamedTag()->getTag('remove_tile') !== null) {
                $block = $this->getPosition()->getWorld()->getBlock($this->getPosition()->asVector3());
                $tile = $this->getPosition()->getWorld()->getTile($this->getPosition()->asVector3());
                
                if ($tile instanceof self)
                    $this->getPosition()->getWorld()->removeTile($tile);
                
                if ($block->getId() === 54) $this->getPosition()->getWorld()->setBlock($this->getPosition()->asVector3(), VanillaBlocks::AIR());
                
                if  ($this->getCrateName() !== null) {
                    $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getCrateName());
                    
                    if ($crate !== null) {
                        if ($this->getText() !== null) $this->getText()->close();
                    }
                }
                $player->sendMessage(TextFormat::colorize('&cThe tile has been removed'));
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&3Crate configuration'));
    }
    
    /**
     * @param Player $player
     */
    public function reedemKey(Player $player): void
    {
        if ($this->getCrateName() !== null) {
            $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getCrateName());
            
            if ($crate !== null) {
                $itemInHand = $player->getInventory()->getItemInHand();
                
                if ($itemInHand->hasNamedTag() && $itemInHand->getNamedTag()->getTag('crate_name') !== null) {
                    if ($itemInHand->getNamedTag()->getString('crate_name') === $this->getCrateName())
                        $crate->giveReward($player);
                }
            }
        }
    }
}
