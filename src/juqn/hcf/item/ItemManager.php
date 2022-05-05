<?php

declare(strict_types=1);

namespace juqn\hcf\item;

use pocketmine\item\ItemFactory;
use pocketmine\item\PotionType;

class ItemManager
{
    
    /**
     * ItemManager construct.
     */
    public function __construct()
    {
        ItemFactory::getInstance()->register(new EnderpearlItem());
        
        foreach(PotionType::getAll() as $type)
            ItemFactory::getInstance()->register(new SplashPotionItem($type), true);
    }
}