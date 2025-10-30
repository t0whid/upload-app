<?php
// app/Services/RateLimitService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RateLimitService
{
    public function checkLimit($type, $sessionId, $ipAddress)
    {
        $limit = $this->getLimit($type);
        $timeout = env('RATE_LIMIT_TIMEOUT', 3600);
        
        $cacheKey = "rate_limit_{$type}";
        
        // Get current active sessions from cache
        $activeSessions = Cache::get($cacheKey, []);
        
        // Clean expired sessions
        $currentTime = time();
        $activeSessions = array_filter($activeSessions, function($sessionTime) use ($currentTime, $timeout) {
            return ($currentTime - $sessionTime) < $timeout;
        });
        
        $currentActive = count($activeSessions);
        
        if ($currentActive >= $limit) {
            return [
                'allowed' => false,
                'current' => $currentActive,
                'limit' => $limit,
                'queue_position' => $currentActive - $limit + 1
            ];
        }
        
        // Add new session
        $activeSessions[$sessionId] = $currentTime;
        Cache::put($cacheKey, $activeSessions, $timeout);
        
        return [
            'allowed' => true,
            'current' => $currentActive + 1,
            'limit' => $limit
        ];
    }
    
    public function releaseSlot($type, $sessionId)
    {
        $cacheKey = "rate_limit_{$type}";
        $activeSessions = Cache::get($cacheKey, []);
        
        if (isset($activeSessions[$sessionId])) {
            unset($activeSessions[$sessionId]);
            $timeout = env('RATE_LIMIT_TIMEOUT', 3600);
            Cache::put($cacheKey, $activeSessions, $timeout);
        }
    }
    
    public function getLimit($type)
    {
        if ($type === 'upload') {
            return (int) env('UPLOAD_RATE_LIMIT', 100);
        } elseif ($type === 'download') {
            return (int) env('DOWNLOAD_RATE_LIMIT', 100);
        }
        
        return 100;
    }
    
    public function getCurrentStats($type)
    {
        $cacheKey = "rate_limit_{$type}";
        $activeSessions = Cache::get($cacheKey, []);
        
        $currentTime = time();
        $timeout = env('RATE_LIMIT_TIMEOUT', 3600);
        $activeSessions = array_filter($activeSessions, function($sessionTime) use ($currentTime, $timeout) {
            return ($currentTime - $sessionTime) < $timeout;
        });
        
        $currentActive = count($activeSessions);
        $limit = $this->getLimit($type);
        
        return [
            'current' => $currentActive,
            'limit' => $limit,
            'available' => max(0, $limit - $currentActive)
        ];
    }
}