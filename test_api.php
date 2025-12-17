<?php
/**
 * API Test Script for ProductSimple API
 * 
 * Usage: php test_api.php [base_url]
 * Example: php test_api.php http://localhost/webdoan/public
 */

// Get base URL from command line or use default
$baseUrl = $argv[1] ?? 'http://localhost/webdoan/public';
$apiUrl = rtrim($baseUrl, '/') . '/api/products';

echo "========================================\n";
echo "ProductSimple API Test Suite\n";
echo "========================================\n";
echo "Base URL: {$baseUrl}\n";
echo "API URL: {$apiUrl}\n";
echo "========================================\n\n";

// Colors for output
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$blue = "\033[34m";
$reset = "\033[0m";

$testResults = [];
$createdProductId = null;

/**
 * Make HTTP request
 */
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT' || $method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => $response,
        'error' => $error
    ];
}

/**
 * Test function
 */
function test($name, $url, $method = 'GET', $data = null, $expectedCode = 200) {
    global $green, $red, $yellow, $blue, $reset, $testResults;
    
    echo "{$blue}Testing: {$name}{$reset}\n";
    echo "  URL: {$url}\n";
    if ($data) {
        echo "  Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
    $result = makeRequest($url, $method, $data);
    $response = json_decode($result['body'], true);
    
    $success = ($result['code'] == $expectedCode && !$result['error']);
    
    if ($success) {
        echo "  {$green}✓ PASSED{$reset} (HTTP {$result['code']})\n";
    } else {
        echo "  {$red}✗ FAILED{$reset} (HTTP {$result['code']})\n";
        if ($result['error']) {
            echo "  Error: {$result['error']}\n";
        }
    }
    
    if ($response) {
        echo "  Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "  Response: {$result['body']}\n";
    }
    
    echo "\n";
    
    $testResults[] = [
        'name' => $name,
        'success' => $success,
        'code' => $result['code']
    ];
    
    return $response;
}

// Test 1: Get all products
echo "{$yellow}=== Test 1: Get All Products ==={$reset}\n";
$response = test('GET /api/products', $apiUrl, 'GET', null, 200);
echo "\n";

// Test 2: Get all products with pagination
echo "{$yellow}=== Test 2: Get All Products (with pagination) ==={$reset}\n";
$response = test('GET /api/products?per_page=5', $apiUrl . '?per_page=5', 'GET', null, 200);
echo "\n";

// Test 3: Get all products with search
echo "{$yellow}=== Test 3: Get All Products (with search) ==={$reset}\n";
$response = test('GET /api/products?search=test', $apiUrl . '?search=test', 'GET', null, 200);
echo "\n";

// Test 4: Get all products with sorting
echo "{$yellow}=== Test 4: Get All Products (with sorting) ==={$reset}\n";
$response = test('GET /api/products?sort_by=price&sort_order=desc', $apiUrl . '?sort_by=price&sort_order=desc', 'GET', null, 200);
echo "\n";

// Test 5: Get all categories
echo "{$yellow}=== Test 5: Get All Categories ==={$reset}\n";
$response = test('GET /api/products/categories', $apiUrl . '/categories', 'GET', null, 200);
echo "\n";

// Test 6: Create a new product
echo "{$yellow}=== Test 6: Create New Product ==={$reset}\n";
$productData = [
    'title' => 'Test Product ' . time(),
    'price' => '99.99',
    'image' => 'https://example.com/image.jpg',
    'short_description' => 'This is a test product',
    'detail_description' => 'This is a detailed description of the test product',
    'category' => 'Electronics',
    'tags' => ['test', 'electronics', 'sample'],
    'view_count' => 0,
    'rating_count' => 0,
    'average_rating' => 4.5
];
$response = test('POST /api/products', $apiUrl, 'POST', $productData, 201);
if ($response && isset($response['data']['id'])) {
    $createdProductId = $response['data']['id'];
    echo "  Created Product ID: {$createdProductId}\n\n";
}
echo "\n";

// Test 7: Get single product (if we created one)
if ($createdProductId) {
    echo "{$yellow}=== Test 7: Get Single Product ==={$reset}\n";
    $response = test('GET /api/products/{id}', $apiUrl . '/' . $createdProductId, 'GET', null, 200);
    echo "\n";
    
    // Test 8: Update product
    echo "{$yellow}=== Test 8: Update Product ==={$reset}\n";
    $updateData = [
        'title' => 'Updated Test Product',
        'price' => '149.99',
        'average_rating' => 4.8
    ];
    $response = test('PUT /api/products/{id}', $apiUrl . '/' . $createdProductId, 'PUT', $updateData, 200);
    echo "\n";
    
    // Test 9: Get products by category
    echo "{$yellow}=== Test 9: Get Products by Category ==={$reset}\n";
    $response = test('GET /api/products/category/Electronics', $apiUrl . '/category/Electronics', 'GET', null, 200);
    echo "\n";
    
    // Test 10: Delete product
    echo "{$yellow}=== Test 10: Delete Product ==={$reset}\n";
    $response = test('DELETE /api/products/{id}', $apiUrl . '/' . $createdProductId, 'DELETE', null, 200);
    echo "\n";
    
    // Test 11: Try to get deleted product (should fail)
    echo "{$yellow}=== Test 11: Get Deleted Product (should fail) ==={$reset}\n";
    $response = test('GET /api/products/{id}', $apiUrl . '/' . $createdProductId, 'GET', null, 404);
    echo "\n";
} else {
    echo "{$yellow}=== Skipping tests 7-11 (no product created) ==={$reset}\n\n";
}

// Test 12: Get non-existent product
echo "{$yellow}=== Test 12: Get Non-existent Product ==={$reset}\n";
$response = test('GET /api/products/99999', $apiUrl . '/99999', 'GET', null, 404);
echo "\n";

// Test 13: Create product with invalid data (should fail)
echo "{$yellow}=== Test 13: Create Product with Invalid Data (should fail) ==={$reset}\n";
$invalidData = [
    'price' => '99.99'
    // Missing required 'title' field
];
$response = test('POST /api/products', $apiUrl, 'POST', $invalidData, 422);
echo "\n";

// Summary
echo "========================================\n";
echo "Test Summary\n";
echo "========================================\n";

$passed = 0;
$failed = 0;

foreach ($testResults as $result) {
    if ($result['success']) {
        $passed++;
        echo "{$green}✓{$reset} {$result['name']} - HTTP {$result['code']}\n";
    } else {
        $failed++;
        echo "{$red}✗{$reset} {$result['name']} - HTTP {$result['code']}\n";
    }
}

echo "\n";
echo "Total: " . count($testResults) . " tests\n";
echo "{$green}Passed: {$passed}{$reset}\n";
echo "{$red}Failed: {$failed}{$reset}\n";
echo "========================================\n";

