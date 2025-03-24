<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <title>Filter Packs</title>
</head>
<body>
<div class="container mt-4">
    <form id="filter-form" method="POST">
        <div class="row">
            <!-- Price Range Filter -->
            <div class="col-md-4">
                <label for="filter-price-range">Price Range:</label>
                <select id="filter-price-range" name="priceRange" class="form-control">
                    <option value="">All</option>
                    <option value="0-50">0 - 50</option>
                    <option value="51-100">51 - 100</option>
                    <option value="101-200">101 - 200</option>
                    <option value="201-500">201 - 500</option>
                    <option value="501-1000">501 - 1000</option>
                </select>
            </div>

            <!-- Pack Name Search Bar -->
            <div class="col-md-4">
                <label for="filter-packName">Search Pack Name:</label>
                <input type="text" id="filter-packName" name="packName" class="form-control" placeholder="Enter pack name...">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </div>
        </div>
        
    </form>
</div>

</body>
</html>