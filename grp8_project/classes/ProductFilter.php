<?php
class ProductFilter
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUniqueCategories()
    {
        $categoryStmt = $this->pdo->query("SELECT DISTINCT category FROM products");
        return $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredProducts($filterOption, $searchTerm)
    {
        $sql = "SELECT * FROM products";
        $params = [];
        $conditions = [];

        // Add search term condition
        if (!empty($searchTerm)) {
            $conditions[] = "(name LIKE ? OR description LIKE ?)";
            $params[] = '%' . $searchTerm . '%';
            $params[] = '%' . $searchTerm . '%';
        }

        // Determine the query based on the selected filter option
        if ($filterOption) {
            if (strpos($filterOption, 'category_') === 0) {
                // Extract the category from the filter option
                $selectedCategory = str_replace('category_', '', $filterOption);
                $conditions[] = "category = ?";
                $params[] = $selectedCategory;
            }

            // Add sorting order to the SQL query
            if ($filterOption === 'price_asc') {
                $sql .= " ORDER BY price ASC";
            } elseif ($filterOption === 'price_desc') {
                $sql .= " ORDER BY price DESC";
            }
        }

        // Add conditions to the SQL query
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        // Prepare and execute the statement
        $productStmt = $this->pdo->prepare($sql);
        $productStmt->execute($params);

        return $productStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
