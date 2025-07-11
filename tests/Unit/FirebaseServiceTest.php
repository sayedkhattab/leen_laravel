<?php

namespace Tests\Unit;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class FirebaseServiceTest extends TestCase
{
    protected $firebaseService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up mock Firebase configuration
        Config::set('services.firebase.database_url', 'https://test-project.firebaseio.com');
        
        // Mock the Firebase service
        $this->firebaseService = new FirebaseService();
    }

    /**
     * Test calculating distance between two coordinates.
     *
     * @return void
     */
    public function test_calculate_distance()
    {
        $method = new \ReflectionMethod(FirebaseService::class, 'calculateDistance');
        $method->setAccessible(true);

        // Test distance calculation between Riyadh and Jeddah (approximately 850-900 km)
        $distance = $method->invoke($this->firebaseService, 24.7136, 46.6753, 21.5433, 39.1728);
        
        // Check if the distance is within the expected range
        $this->assertGreaterThan(800, $distance);
        $this->assertLessThan(950, $distance);
    }

    /**
     * Test updating user location in Firebase.
     *
     * @return void
     */
    public function test_update_user_location()
    {
        Http::fake([
            'https://test-project.firebaseio.com/locations/sellers/1.json' => Http::response(['success' => true], 200)
        ]);

        $result = $this->firebaseService->updateUserLocation(1, 24.7136, 46.6753, 'seller');
        
        $this->assertTrue($result);
        
        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/json') &&
                   $request->url() === 'https://test-project.firebaseio.com/locations/sellers/1.json' &&
                   $request->method() === 'PATCH';
        });
    }

    /**
     * Test getting nearby users from Firebase.
     *
     * @return void
     */
    public function test_get_nearby_users()
    {
        // Mock Firebase response with sample location data
        Http::fake([
            'https://test-project.firebaseio.com/locations/sellers.json' => Http::response([
                '1' => [
                    'latitude' => 24.7141,
                    'longitude' => 46.6748,
                    'lastUpdated' => 1687356789
                ],
                '2' => [
                    'latitude' => 24.7150,
                    'longitude' => 46.6760,
                    'lastUpdated' => 1687356800
                ],
                '3' => [
                    'latitude' => 25.7136, // This one is far away (about 111 km)
                    'longitude' => 46.6753,
                    'lastUpdated' => 1687356820
                ]
            ], 200)
        ]);

        // Get nearby users within 5km radius
        $nearbyUsers = $this->firebaseService->getNearbyUsers(24.7136, 46.6753, 5, 'seller');
        
        // Should only include the first two users (within 5km)
        $this->assertCount(2, $nearbyUsers);
        $this->assertArrayHasKey('1', $nearbyUsers);
        $this->assertArrayHasKey('2', $nearbyUsers);
        $this->assertArrayNotHasKey('3', $nearbyUsers);
        
        // Check if distances are calculated correctly
        $this->assertLessThan(1, $nearbyUsers['1']['distance']);
        $this->assertLessThan(2, $nearbyUsers['2']['distance']);
    }
} 