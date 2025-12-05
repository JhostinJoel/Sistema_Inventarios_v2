<?php

/**
 * Unit Tests for Matia's Store CRUD Operations
 * Tests: Products and Categories
 */

require_once __DIR__ . '/../config/db.php';

class TestRunner
{
    private $pdo;
    private $results = [];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Test: Create Category
    public function testCreateCategory()
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $result = $stmt->execute(['Test Category', 'Category for testing']);
            $categoryId = $this->pdo->lastInsertId();

            $this->results[] = [
                'test' => 'Create Category',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Category created with ID: $categoryId" : 'Failed to create category',
                'id' => $categoryId
            ];

            return $categoryId;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Create Category',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Read Category
    public function testReadCategory($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->results[] = [
                'test' => 'Read Category',
                'status' => $category ? 'PASS' : 'FAIL',
                'message' => $category ? "Category found: {$category['name']}" : 'Category not found'
            ];

            return $category;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Read Category',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Update Category
    public function testUpdateCategory($id)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $result = $stmt->execute(['Updated Category', 'Updated description', $id]);

            $this->results[] = [
                'test' => 'Update Category',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Category ID $id updated successfully" : 'Failed to update category'
            ];

            return $result;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Update Category',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Delete Category
    public function testDeleteCategory($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            $result = $stmt->execute([$id]);

            $this->results[] = [
                'test' => 'Delete Category',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Category ID $id deleted successfully" : 'Failed to delete category'
            ];

            return $result;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Delete Category',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Create Product
    public function testCreateProduct($categoryId)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                'Test Product',
                'Product for testing',
                99.99,
                50,
                $categoryId,
                'https://via.placeholder.com/300'
            ]);
            $productId = $this->pdo->lastInsertId();

            $this->results[] = [
                'test' => 'Create Product',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Product created with ID: $productId" : 'Failed to create product',
                'id' => $productId
            ];

            return $productId;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Create Product',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Read Product
    public function testReadProduct($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->results[] = [
                'test' => 'Read Product',
                'status' => $product ? 'PASS' : 'FAIL',
                'message' => $product ? "Product found: {$product['name']}" : 'Product not found'
            ];

            return $product;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Read Product',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Update Product
    public function testUpdateProduct($id)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?");
            $result = $stmt->execute(['Updated Product', 149.99, 75, $id]);

            $this->results[] = [
                'test' => 'Update Product',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Product ID $id updated successfully" : 'Failed to update product'
            ];

            return $result;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Update Product',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    // Test: Delete Product
    public function testDeleteProduct($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            $result = $stmt->execute([$id]);

            $this->results[] = [
                'test' => 'Delete Product',
                'status' => $result ? 'PASS' : 'FAIL',
                'message' => $result ? "Product ID $id deleted successfully" : 'Failed to delete product'
            ];

            return $result;
        } catch (Exception $e) {
            $this->results[] = [
                'test' => 'Delete Product',
                'status' => 'FAIL',
                'message' => 'Error: ' . $e->getMessage()
            ];
            return false;
        }
    }

    public function getResults()
    {
        return $this->results;
    }

    public function printResults()
    {
        echo "<!DOCTYPE html>\n";
        echo "<html lang='es'>\n<head>\n";
        echo "<meta charset='UTF-8'>\n";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
        echo "<title>Test Results - Matia's Store</title>\n";
        echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>\n";
        echo "<style>body { background: #0a0a0f; color: #fff; padding: 40px; }</style>\n";
        echo "</head>\n<body>\n";
        echo "<div class='container'>\n";
        echo "<h1 class='mb-4'>🧪 Unit Test Results - Matia's Store</h1>\n";
        echo "<p class='text-muted'>Executed: " . date('Y-m-d H:i:s') . "</p>\n";

        $passed = 0;
        $failed = 0;

        echo "<table class='table table-dark table-striped'>\n";
        echo "<thead><tr><th>Test</th><th>Status</th><th>Message</th></tr></thead>\n";
        echo "<tbody>\n";

        foreach ($this->results as $result) {
            $statusClass = $result['status'] === 'PASS' ? 'success' : 'danger';
            $statusIcon = $result['status'] === 'PASS' ? '✅' : '❌';

            if ($result['status'] === 'PASS') $passed++;
            else $failed++;

            echo "<tr class='table-{$statusClass}'>\n";
            echo "<td>{$result['test']}</td>\n";
            echo "<td>{$statusIcon} {$result['status']}</td>\n";
            echo "<td>{$result['message']}</td>\n";
            echo "</tr>\n";
        }

        echo "</tbody>\n</table>\n";

        $total = $passed + $failed;
        $percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

        echo "<div class='alert alert-info mt-4'>\n";
        echo "<h4>Summary</h4>\n";
        echo "<p><strong>Total Tests:</strong> $total</p>\n";
        echo "<p><strong>Passed:</strong> $passed</p>\n";
        echo "<p><strong>Failed:</strong> $failed</p>\n";
        echo "<p><strong>Success Rate:</strong> {$percentage}%</p>\n";
        echo "</div>\n";

        echo "</div>\n</body>\n</html>";
    }
}

// Run Tests
try {
    $tester = new TestRunner($pdo);

    // Category CRUD Tests
    $categoryId = $tester->testCreateCategory();
    if ($categoryId) {
        $tester->testReadCategory($categoryId);
        $tester->testUpdateCategory($categoryId);

        // Product CRUD Tests (using the created category)
        $productId = $tester->testCreateProduct($categoryId);
        if ($productId) {
            $tester->testReadProduct($productId);
            $tester->testUpdateProduct($productId);
            $tester->testDeleteProduct($productId);
        }

        // Clean up category
        $tester->testDeleteCategory($categoryId);
    }

    // Display results
    $tester->printResults();
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Fatal Error: " . $e->getMessage() . "</div>";
}
