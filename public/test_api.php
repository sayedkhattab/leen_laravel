<?php

// Set headers to ensure JSON response
header('Content-Type: application/json');

// Output a simple JSON response
echo json_encode([
    'status' => true,
    'message' => 'API test successful',
    'data' => [
        'request_status' => 'approved',
        'is_approved' => true,
        'rejection_reason' => null
    ]
]); 