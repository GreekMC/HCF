<?php

declare(strict_types=1);

namespace juqn\hcf\utils;

use InvalidArgumentException;

/**
 * Class Timer
 * @package juqn\hcf\utils
 */
final class Timer
{
    
    /**
     * @param string $duration
     * @throws InvalidArgumentException
     * @return int
     */
    public static function time(string $duration): int
    {
        $time_units = ['y' => 'year', 'M' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'm' => 'minute'];
        $regex = '/^([0-9]+y)?([0-9]+M)?([0-9]+w)?([0-9]+d)?([0-9]+h)?([0-9]+m)?$/';
        $matches = [];
        $is_matching = preg_match($regex, $duration, $matches);
        
        if (!$is_matching) {
            throw new InvalidArgumentException('Invalid duration. Please put numbers and letters');
        }
        $time = '';

        foreach ($matches as $index => $match) {
            if ($index === 0 || strlen($match) === 0) continue;
            $n = substr($match, 0, -1);
            $unit = $time_units[substr($match, -1)];
            $time .= "$n $unit ";
        }
        $time = trim($time);

        return $time === '' ? time() : strtotime($time);
    }
    
    /**
     * @param int $time
     * @return string
     */
    public static function date(int $time): string
    {
        $weeks = $time / 604800 % 52;
        $hours = $time / 3600 % 24;
        $minutes = $time / 60 % 60;
        $seconds = $time % 60;
        
        return $weeks . ' week(s), ' . $hours . ' hour(s), ' . $minutes . ' minute(s) and ' . $seconds . ' second(s)';
    }
    
    /**
     * @param int $time
     * @return string
     */
    public static function format(int $time): string
    {
        if ($time >= 3600)
            return gmdate('H:i:s', $time);
        elseif ($time < 60)
            return $time . 's';
        return gmdate('i:s', $time);
    }
    
    public static function convert(int $time): string
    {
        if ($time < 60)
            return $time . 's';
        elseif ($time < 3600) {
            $minutes = $time / 60 % 60;
            return $minutes . 'm';
        } elseif ($time < 86400) {
            $hours = $time / 3600 % 24;
            return $hours . 'h';
        } else {
            $days = floor($time / 86400);
            return $days . 'd';
        }
    }
}