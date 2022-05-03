<?php

declare(strict_types=1);

namespace juqn\hcf\enchantment;

use juqn\hcf\enchantment\command\EnchantmentCommand;
use juqn\hcf\enchantment\defaults\FireResistanceEnchantment;
use juqn\hcf\enchantment\defaults\ImplantsEnchantment;
use juqn\hcf\enchantment\defaults\InvisibilityEnchantment;
use juqn\hcf\enchantment\defaults\NightVisionEnchantment;
use juqn\hcf\enchantment\defaults\SpeedEnchantment;
use juqn\hcf\HCFLoader;

use pocketmine\data\bedrock\EnchantmentIdMap;

/**
 * Class EnchantmentManager
 * @package juqn\hcf\enchantment
 */
class EnchantmentManager
{
    
    /** @var Enchantment[] */
    private array $enchantments = [];
    
    /**
     * EnchantmentManager construct.
     */
    public function __construct()
    {
        # Register custom enchants
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::SPEED, $this->enchantments[40] = new SpeedEnchantment());
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::INVISIBILITY, $this->enchantments[41] = new InvisibilityEnchantment());
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::NIGHT_VISION, $this->enchantments[42] = new NightVisionEnchantment());
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::FIRE_RESISTANCE, $this->enchantments[43] = new FireResistanceEnchantment());
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::IMPLANTS, $this->enchantments[44] = new ImplantsEnchantment());
        
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EnchantmentCommand());
    }
    
    /**
     * @return Enchantment[]
     */
    public function getEnchantments(): array
    {
        return $this->enchantments;
    }
}