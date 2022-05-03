<?php

declare(strict_types=1);

namespace juqn\hcf\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\utils\TextFormat;

/**
 * Class PartnerItem
 * @package juqn\hcf\item
 */
class PartnerItem extends Item
{
    
    /**
     * PartnerItem construct.
     * @param string $name
     * @param int $id
     * @param int $meta
     */
    public function __construct(string $name, int $id, int $meta = 0)
    {
        parent::__construct(new ItemIdentifier($id, $meta), TextFormat::colorize($name));
        $this->setCustomName(TextFormat::colorize($name));
        $this->setNamedTag($this->getNamedTag()->setString('partner', $name));
    }
}