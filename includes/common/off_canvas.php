<?php
  // Cart data
  $cartItems = [
    ['name' => 'Growers cider', 'description' => 'Brief description', 'price' => 12],
    ['name' => 'Fresh grapes', 'description' => 'Brief description', 'price' => 8],
    ['name' => 'Heinz tomato ketchup', 'description' => 'Brief description', 'price' => 5],
  ];

  // Calculate total price
  $totalPrice = array_sum(array_column($cartItems, 'price'));
  ?>

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart">
    <div class="offcanvas-header justify-content-center">
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Your cart</span>
          <span class="badge bg-primary rounded-pill"><?= count($cartItems) ?></span>
        </h4>
        <ul class="list-group mb-3">
          <?php foreach ($cartItems as $item): ?>
            <li class="list-group-item d-flex justify-content-between lh-sm">
              <div>
                <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                <small class="text-body-secondary"><?= htmlspecialchars($item['description']) ?></small>
              </div>
              <span class="text-body-secondary">$<?= htmlspecialchars($item['price']) ?></span>
            </li>
          <?php endforeach; ?>
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>$<?= htmlspecialchars($totalPrice) ?></strong>
          </li>
        </ul>
        <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
      </div>
    </div>
  </div>


  <?php
  // Menu items array
  $menuItems = [
    ['name' => 'Whey protein', 'icon' => 'fruits', 'url' => 'index.html'],
    ['name' => 'Isolate', 'icon' => 'dairy', 'url' => 'index.html'],
    ['name' => 'Plant protein', 'icon' => 'meat', 'url' => 'index.html'],
    ['name' => 'Preworkout', 'icon' => 'seafood', 'url' => 'index.html'],
    ['name' => 'BCAA', 'icon' => 'bakery', 'url' => 'index.html'],
    ['name' => 'EAA', 'icon' => 'canned', 'url' => 'index.html'],
    ['name' => 'Weight gainers', 'icon' => 'frozen', 'url' => 'index.html'],
    ['name' => 'Mass gainers', 'icon' => 'pasta', 'url' => 'index.html'],
    ['name' => 'Fat burner', 'icon' => 'breakfast', 'url' => 'index.html'],
    ['name' => 'L carnitine', 'icon' => 'snacks', 'url' => 'index.html'],
    ['name' => 'Creatine', 'icon' => 'spices', 'url' => 'index.html'],
    ['name' => 'L arginine', 'icon' => 'baby', 'url' => 'index.html'],
    ['name' => 'Glutamine', 'icon' => 'health', 'url' => 'index.html'],
    ['name' => 'Protein bars/ Nutrition bars', 'icon' => 'household', 'url' => 'index.html'],
    ['name' => 'Citrulline', 'icon' => 'personal', 'url' => 'index.html'],
    ['name' => 'Testosterone', 'icon' => 'pet', 'url' => 'index.html'],
  ];

  // Dropdown for Beverages
  $beverages = [
    ['name' => 'Water', 'url' => 'index.html'],
    ['name' => 'Juice', 'url' => 'index.html'],
    ['name' => 'Soda', 'url' => 'index.html'],
    ['name' => 'Tea', 'url' => 'index.html'],
  ];
  ?>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar">
    <div class="offcanvas-header justify-content-between">
      <h4 class="fw-normal text-uppercase fs-6">Menu</h4>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
        <?php foreach ($menuItems as $item): ?>
          <li class="nav-item border-dashed">
            <a href="<?= htmlspecialchars($item['url']) ?>"
              class="nav-link d-flex align-items-center gap-3 text-dark p-2">
              <!-- <svg width="24" height="24" viewBox="0 0 24 24">
                <use xlink:href="#<?= htmlspecialchars($item['icon']) ?>"></use>
              </svg> -->
              <span><?= htmlspecialchars($item['name']) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>