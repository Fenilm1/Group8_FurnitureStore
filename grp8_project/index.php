<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/Product.php';
include_once 'classes/ProductFilter.php';

// Initialize database and get connection
$database = new Database();
$pdo = $database->getConnection();

// Initialize the ProductFilter class
$productFilter = new ProductFilter($pdo);

// Fetch unique categories from the products table
$categories = $productFilter->getUniqueCategories();

// Get the selected filter option and search term from the request
$filterOption = isset($_GET['filter_option']) ? $_GET['filter_option'] : null;
$searchTerm = isset($_GET['search_term']) ? $_GET['search_term'] : '';

// Fetch products based on the filter option and search term
$products = $productFilter->getFilteredProducts($filterOption, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/nav.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Products</h1>
        <form method="GET" action="" class="form-inline mb-4 justify-content-center">
            <div class="form-group mr-2">
                <label for="search_term" class="sr-only">Search:</label>
                <input type="text" name="search_term" id="search_term" class="form-control" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search products">
            </div>
            <div class="form-group mr-2">
                <label for="filter_option" class="sr-only">Filter and Sort:</label>
                <select name="filter_option" id="filter_option" class="custom-select" onchange="this.form.submit()">
                    <option value="">All Products</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="category_<?= htmlspecialchars($category['category']) ?>" <?= $filterOption === 'category_' . $category['category'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category']) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="price_asc" <?= $filterOption === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $filterOption === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="row">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $row): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="admin/<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="card-text"><strong><?php echo '$' . number_format($row['price'], 2); ?></strong></p>
                                <a href="cart.php?add=<?php echo $row['id']; ?>" class="btn btn-primary mt-auto">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        No products found for the given criteria. Please try different search terms or filters.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
