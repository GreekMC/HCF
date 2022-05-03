<?php

declare(strict_types=1);

namespace juqn\hcf\item;

use pocketmine\item\ItemFactory;

class ItemManager
{
    
    public function __construct()
    {
        ItemFactory::getInstance()->register(new EnderPearlItem());
    }
}