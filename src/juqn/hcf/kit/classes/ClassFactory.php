<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes;

use juqn\hcf\kit\classes\presets\Archer;
use juqn\hcf\kit\classes\presets\Bard;
use juqn\hcf\kit\classes\presets\Mage;
use juqn\hcf\kit\classes\presets\Miner;
use juqn\hcf\kit\classes\presets\Rogue;

/**
 * Class ClassFactory
 * @package juqn\hcf\kit\classes
 */
class ClassFactory
{

    /** @var HCFClass[] */
    static private array $classes = [];

    static public function init(): void
    {
        self::registerClass(new Archer());
        self::registerClass(new Bard());
        self::registerClass(new Mage());
        self::registerClass(new Miner());
        self::registerClass(new Rogue());
    }

    /**
     * @return HCFClass[]
     */
    static public function getClasses(): array
    {
        return self::$classes;
    }
    
    /**
     * @param int $id
     * @return HCFClass|null
     */
    static public function getClassById(int $id): ?HCFClass
    {
        return self::$classes[$id] ?? null;
    }
    
    /**
     * @param HCFClass $class
     */
    static private function registerClass(HCFClass $class): void
    {
        self::$classes[$class->getId()] = $class;
    }
}