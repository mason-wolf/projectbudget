<?php 

function validate_session() {
    if(isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        return $user;
    }
    else {
      header('location: index.php');
    }
}

function display_balance($connection, $user) {
    $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
    $balance = mysqli_fetch_assoc($balanceQuery);
    echo "<span class='money'>$ " . number_format((float)$balance['balance'], '2', '.', '') . "</span>";
}

function get_categories($connection, $user) {

              $categories = array();

              // retrieve all user-defined categories
              $userCategoryQuery = mysqli_query($connection, "select * from usercategories where owner='" . $user . "'");
              while ($userCategory = mysqli_fetch_assoc($userCategoryQuery)) {
                array_push($categories, $userCategory['title']);
              }

              // retrieve all default categories 
              $defaultCategoryQuery = mysqli_query($connection, "select * from defaultcategories");
              while ($defaultCategory = mysqli_fetch_assoc($defaultCategoryQuery)) {
                array_push($categories, $defaultCategory['title']);
              }

              return $categories;
}

function populate_categories($connection, $user) {
    echo "<select class='field' name='category'>";
    $categories = get_categories($connection, $user);
    sort($categories);
        foreach ($categories as $category) {
            echo "<option value='" . $category . "'>" . $category . "</option>"; 
        }
    echo "</select>";
}

// $spendingProgress - how much has already been spent 
// $spendingPercentage - percentage of budget spent, controls progress bars
// $projectedSpending - total funds allocated 

function show_budget_progress($spendingProgress, $spendingPercentage, $projectedSpending) {

    $divFontColor = "#000";
    $divBackgroundColor = "#00911f";
    $divRadius = "50px 100px";
    $spendingProgressFontColor = "#fff";
    if (empty($spendingProgress)) {
        $spendingProgress = " 0";
        $spendingProgressFontColor = "#000";
    }
    // handle div width and colors when projected expense is exceeded
    if ($spendingPercentage >= 100) {
        $divWidth = 100;
        $divFontColor = "#fff";
        $divBackgroundColor = "#ff6363";
    }
    else {
        $divWidth = $spendingPercentage;
    }

    // change background to color to yellow if more than half was spent than projected 
    if ($spendingPercentage >= 50 && $spendingPercentage < 100) {
        $divBackgroundColor = "#bcb100";
    }

    if ($spendingPercentage < 100) {
        $divRadius = "0px";
    }
    echo "<div class='col-12 projectedSpendingProgress'>
            <div style='padding:10px;border-radius:10px;border-top-left-radius: 50px 100px;border-bottom-left-radius: 50px 100px; border-top-right-radius: " . $divRadius . ";
            border-bottom-right-radius: " .$divRadius . ";background-color:" . $divBackgroundColor . ";width:" . $divWidth . "%;height:100%;'></div>
            <span style='float:left;margin-top:-38px;margin-left:10px;color:" . $spendingProgressFontColor . ";'>$ " . $spendingProgress . "</span>
            <span style='float:right;padding:.4em;margin-top:-50px;color:" . $divFontColor . ";'>$" . $projectedSpending . "</span>
         </div>"; 
}
?>
