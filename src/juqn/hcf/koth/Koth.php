<?php

declare(strict_types=1);

namespace juqn\hcf\koth;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\utils\TextFormat;

/**
 * Class Koth
 * @package juqn\hcf\koth
 */
class Koth
{
    
    /** @var Player|null */
    private ?Player $capturer = null;
    
    /** @var string */
    private string $name;
    /** @var int */
    private int $time, $progress;
    /** @var int */
    private int $points;
    
    /** @var string */
    private string $key;
    /** @var int */
    private int $keyCount;
    
    /** @var string|null */
    private ?string $coords;
    
    /** @var KothCapzone|null */
    private ?KothCapzone $capzone = null;
    
    /**
     * Koth construct.
     * @param string $name
     * @param int $time
     * @param int $points
     * @param string $key
     * @param int $keyCount
     * @param string|null $coords
     * @param array|null $claim
     * @param array|null $capzone
     */
    public function __construct(string $name, int $time, int $points, string $key, int $keyCount, ?string $coords, ?array $claim, ?array $capzone)
    {
        $this->name = $name;
        $this->time = $time;
        $this->points = $points;
        $this->progress = $time;
        $this->key = $key;
        $this->keyCount = $keyCount;
        $this->coords = $coords;
        
        if ($claim !== null)
            HCFLoader::getInstance()->getClaimManager()->createClaim($name, 'koth', (int) $claim['minX'], (int) $claim['maxX'], (int) $claim['minZ'], (int) $claim['maxZ'], $claim['world']);
        
        if ($capzone !== null)
            $this->capzone = new KothCapzone((int) $capzone['minX'], (int) $capzone['maxX'], (int) $capzone['minY'], (int) $capzone['maxY'], (int) $capzone['minZ'], (int) $capzone['maxZ'], $capzone['world']);
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return int
     */
    public function getProgress(): int
    {
        return $this->progress;
    }
    
    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }
    
    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
    
    /**
     * @return int
     */
    public function getKeyCount(): int
    {
        return $this->keyCount;
    }
    
    /**
     * @return string|null
     */
    public function getCoords(): ?string
    {
        return $this->coords;
    }
    
    /**
     * @return KothCapzone|null
     */
    public function getCapzone(): ?KothCapzone
    {
        return $this->capzone;
    }
    
    /**
     * @param int $time
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }
    
    /**
     * @param int $time
     */
    public function setProgress(int $time): void
    {
        $this->progress = $time;
    }
    
    /**
     * @param int $points
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }
    
    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
    
    /**
     * @param int $count
     */
    public function setKeyCount(int $count): void
    {
        $this->keyCount = $count;
    }
    
    /**
     * @param string|null $coords
     */
    public function setCoords(?string $coords): void
    {
        $this->coords = $coords;
    }
    
    /**
     * @param KothCapzone $capzone
     */
    public function setCapzone(KothCapzone $capzone): void
    {
        $this->capzone = $capzone;
    }
    
    public function update(): void
    {
        if ($this->capturer === null) {
            $world = HCFLoader::getInstance()->getServer()->getWorldManager()->getWorldByName($this->getCapzone()->getWorld());
            
            if ($world !== null) {
                foreach ($world->getPlayers() as $player) {
                    if ($player instanceof Player) {
                        if ($this->getCapzone()->inside($player->getPosition()) && $player->getSession()->getFaction() !== null && ($player->getSession()->getCooldown('pvp.timer')  === null && $player->getSession()->getCooldown('starting.timer') === null)) {
                            $this->capturer = $player;
                            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize('&8[&l&6KOTH&r&8] &e' . $player->getName() . ' &6is capturing&e ' . $this->getName()));
                            break;
                        }
                    }
                }
            }
        } else {
            if (!$this->capturer->isOnline() || !$this->getCapzone()->inside($this->capturer->getPosition())) {
                $this->progress = $this->time;
                $this->capturer = null;
                return;
            }
            
            if ($this->getProgress() === 0) {
                $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($this->getKey());
                
                if ($crate !== null) {
                    $crate->giveKey($this->capturer, $this->getKeyCount());
                    $this->capturer->sendMessage(TextFormat::colorize('&8[&6KOTH&r&8] &6You have received &e' . $this->getKey() . ' Key'));
                }
                $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('koth.webhook'));

                $msg = new Message();

                $embed = new Embed();
                $embed->setTitle("KotH " . "{$this->getName()}" . " has finished");
                $embed->setColor(0x9AD800);
                $embed->addField("Was captured by ", "{$this->capturer->getName()}");
                $embed->setFooter("greekmc.net");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize('&8[&l&6KOTH&r&8] &e' . $this->getName() . ' &6has been controlled by&e ' . $this->capturer->getName()));
                $this->progress = $this->time;
                $this->capturer = null;
                
                HCFLoader::getInstance()->getKothManager()->setKothActive(null);

                return;
            }
            $this->progress--;
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'time' => $this->getTime(),
            'points' => $this->getPoints(),
            'key' => $this->getKey(),
            'keyCount' => $this->getKeyCount(),
            'coords' => $this->getCoords(),
            'claim' => null,
            'capzone' => null
        ];
        
        if (($claim = HCFLoader::getInstance()->getClaimManager()->getClaim($this->getName())) !== null) 
            $data['claim'] = [
                'minX' => $claim->getMinX(),
                'maxX' => $claim->getMaxX(),
                'minZ' => $claim->getMinZ(),
                'maxZ' => $claim->getMaxZ(),
                'world' => $claim->getWorld()
            ];
        
        if ($this->getCapzone() !== null)
            $data['capzone'] = [
                'minX' => $this->getCapzone()->getMinX(),
                'maxX' => $this->getCapzone()->getMaxX(),
                'minY' => $this->getCapzone()->getMinY(),
                'maxY' => $this->getCapzone()->getMaxY(),
                'minZ' => $this->getCapzone()->getMinZ(),
                'maxZ' => $this->getCapzone()->getMaxZ(),
                'world' => $this->getCapzone()->getWorld()
            ];
        return $data;
    }
}