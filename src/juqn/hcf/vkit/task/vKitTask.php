<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\task;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

/**
 * Class vKitTask
 * @package juqn\hcf\vkit\task
 */
class vKitTask extends Task
{
    
    /** @var Player */
    private Player $player;
    /** @var string */
    private string $vKitName;
    /** @var InvMenu */
    private InvMenu $menu;
    /** @var int[] */
    private array $slots = [
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17
    ];
    /** @var int */
    private int $time, $slot;
    
    /** @var bool */
    public bool $cancel = false;
    
    /**
     * vKitTask construct.
     * @param Player $player
     * @param string $vKitName
     */
    public function __construct(Player $player, string $vKitName)
    {
        $this->player = $player;
        $this->vKitName = $vKitName;
        
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->getInventory()->setContents([
            0 => ItemFactory::getInstance()->get(20, 7),
            1 => ItemFactory::getInstance()->get(20, 7),
            2 => ItemFactory::getInstance()->get(20, 7),
            3 => ItemFactory::getInstance()->get(20, 7),
            4 => ItemFactory::getInstance()->get(410), // Hopper
            5 => ItemFactory::getInstance()->get(20, 7),
            6 => ItemFactory::getInstance()->get(20, 7),
            7 => ItemFactory::getInstance()->get(20, 7),
            8 => ItemFactory::getInstance()->get(20, 7),
            9 => ItemFactory::getInstance()->get(155),
            10 => ItemFactory::getInstance()->get(155),
            11 => ItemFactory::getInstance()->get(155),
            12 => ItemFactory::getInstance()->get(155),
            13 => ItemFactory::getInstance()->get(155),
            14 => ItemFactory::getInstance()->get(155),
            15 => ItemFactory::getInstance()->get(155),
            16 => ItemFactory::getInstance()->get(155),
            17 => ItemFactory::getInstance()->get(155),
            18 => ItemFactory::getInstance()->get(20, 7),
            19 => ItemFactory::getInstance()->get(20, 7),
            20 => ItemFactory::getInstance()->get(20, 7),
            21 => ItemFactory::getInstance()->get(20, 7),
            22 => ItemFactory::getInstance()->get(20, 7),
            23 => ItemFactory::getInstance()->get(20, 7),
            24 => ItemFactory::getInstance()->get(20, 7),
            25 => ItemFactory::getInstance()->get(20, 7),
            26 => ItemFactory::getInstance()->get(20, 7),
        ]);
        $this->menu->setListener(InvMenu::readonly());
        $this->menu->send($player, TextFormat::colorize('&r&8Unlock &b' . $vKitName . ' &r&8vKit?'));
        
        $this->slot = $this->slots[array_rand($this->slots)];
        $this->menu->getInventory()->setItem($this->slot, ItemFactory::getInstance()->get(133));
        
        $chance = rand(0, 100);
        
        if ($chance <= 5)
            $this->time = 58 - $this->slot;
        else {
            $times = [56, 55, 54, 53, 52];
            $this->time = $times[array_rand($times)] - $this->slot;
        }
    }
    
    public function onRun(): void
    {
        if (!$this->player->isOnline()) {
            $this->cancel = true;
        }
        
        if ($this->cancel) {
            $this->getHandler()?->cancel();
            return;
        }
        $this->time--;
        $this->menu->getInventory()->setItem($this->slot, ItemFactory::getInstance()->get(155));
        $this->slot++;
            
        if ($this->slot === 18)
            $this->slot = 9;
        $this->menu->getInventory()->setItem($this->slot, ItemFactory::getInstance()->get(133));
            
        if ($this->slot === 13)
            $this->menu->setName(TextFormat::colorize('&r&8Unlock &b' . $this->vKitName . ' &r&7vKit?'));
        else
            $this->menu->setName(TextFormat::colorize('&r&8Unlock &b' . $this->vKitName . ' &r&8vKit?'));
        
        if ($this->time === 0) {
            $this->cancel = true;
            
            $vKit = HCFLoader::getInstance()->getvKitManager()->getvKit($this->vKitName);  
            $vKit?->giveItems($this->player);
            
            $this->player->getSession()->addCooldown('vkit.' . $this->vKitName, '    ', 259200, false, false);
            
            if ($this->slot === 13) {
                $this->player->getSession()->addCooldown('vkit.unlocked.' . $this->vKitName, '  ', 86400 * 60, true, false);
                $this->player->sendMessage(TextFormat::colorize('&a You have unlocked the ' . $this->vKitName . ' vkit for 60 days'));
            }
        }
    }
}