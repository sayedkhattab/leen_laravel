<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $apiKey;
    protected $projectId;
    protected $databaseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.firebase.api_key');
        $this->projectId = config('services.firebase.project_id');
        $this->databaseUrl = config('services.firebase.database_url');
    }
    
    /**
     * Update user location in Firebase
     * 
     * @param string $userId User ID (seller or customer)
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param string $userType Type of user (seller or customer)
     * @return bool Success status
     */
    public function updateUserLocation($userId, $latitude, $longitude, $userType = 'seller')
    {
        try {
            $endpoint = "{$this->databaseUrl}/locations/{$userType}s/{$userId}.json";
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->patch($endpoint, [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'lastUpdated' => now()->timestamp,
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Firebase location update failed: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Get nearby users based on location
     * 
     * @param float $latitude Current latitude
     * @param float $longitude Current longitude
     * @param float $radiusInKm Search radius in kilometers
     * @param string $userType Type of users to find (seller or customer)
     * @return array List of nearby users with distances
     */
    public function getNearbyUsers($latitude, $longitude, $radiusInKm = 5, $userType = 'seller')
    {
        try {
            // Get all users of specified type
            $endpoint = "{$this->databaseUrl}/locations/{$userType}s.json";
            $response = Http::get($endpoint);
            
            if (!$response->successful() || empty($response->json())) {
                return [];
            }
            
            $users = $response->json();
            $nearbyUsers = [];
            
            foreach ($users as $userId => $locationData) {
                // Calculate distance using Haversine formula
                $distance = $this->calculateDistance(
                    $latitude, 
                    $longitude, 
                    $locationData['latitude'], 
                    $locationData['longitude']
                );
                
                // Add user to results if within radius
                if ($distance <= $radiusInKm) {
                    $nearbyUsers[$userId] = [
                        'latitude' => $locationData['latitude'],
                        'longitude' => $locationData['longitude'],
                        'distance' => $distance,
                        'lastUpdated' => $locationData['lastUpdated'] ?? null
                    ];
                }
            }
            
            // Sort by distance
            uasort($nearbyUsers, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            
            return $nearbyUsers;
        } catch (\Exception $e) {
            Log::error("Firebase nearby users query failed: {$e->getMessage()}");
            return [];
        }
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
} 