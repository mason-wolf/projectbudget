<?php
    include 'header.php';
    include_once 'functions.php';
    session_start();
    $user = validate_session();

    $timeframeQuery = mysqli_query($connection, "select budgetStartDate, budgetEndDate from accounts where owner='" . $user . "'");
    $timeframeResult = mysqli_fetch_assoc($timeframeQuery);

    // create new projected expense 
    if (isset($_POST['newBudgetItem'])) {
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $budgetStartDate = $timeframeResult['budgetStartDate'];
        $budgetEndDate = $timeframeResult['budgetEndDate'];
        $newBudgetItem = mysqli_query($connection, "insert into budgets (owner, category, amount, startDate, endDate) VALUES ('" . $user . "','" . $category . "','" . $amount . 
         "','" . $budgetStartDate . "','" . $budgetEndDate . "')");
    }

    // update budget timeframe
    if (isset($_POST['newTimeframe'])) {
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $timeframeUpdateQuery = mysqli_query($connection, "update accounts set budgetStartDate='" . $startDate . "', budgetEndDate='" . $endDate . "' where owner='" . $user . "'");
        $updateBudgetItems = mysqli_query($connection, "update budgets set startDate='" . $startDate . "', endDate='" . $endDate . "' where owner='" . $user . "' and archived='false'");
        header('location: BudgetManager.php');
    }

    if (isset($_POST['archiveBudget'])) {
        $archiveBudgetQuery = mysqli_query($connection, "update budgets set archived='true' where owner='" . $user . "' and archived='false'");
        $archiveTransactionsQuery = mysqli_query($connection, "update transactions set archived='true' where owner='" . $user . "' and archived='false'");
        $defaultBudgetStartDate = date("Y-m") . "-01"; 
        $defaultBudgetEndDate = date("Y-m-t", strtotime($defaultBudgetStartDate));
        $defaultTimeframeQuery = mysqli_query($connection, "update accounts set budgetStartDate='" . $defaultBudgetStartDate . "', budgetEndDate='" . $defaultBudgetEndDate . "' where owner='" . $user . "'");
    }

?>
                        
<div class="col-12" style="padding:0px">
    <div class="col-7 center shadow"  style="padding:0px;">
        <div class="col-12" style="padding:1em;">
            <div class="col-12"><a href="Dashboard.php">Dashboard</a> <span style="margin-left:15px;margin-right:15px;">></span> <a href="BudgetManager.php">Manage Budget</a></div>
            <div class="col-12">
                <h2>Manage Budget</h2></br>
                <h3 style="float:right;">
                    <a href="#" onclick="document.getElementById('Timeframes').style.display='block'" style="font-weight:100;">
                    <?php 
                            $startDate = date("F j, Y", strtotime($timeframeResult['budgetStartDate']));
                            $endDate = date("F j, Y", strtotime($timeframeResult['budgetEndDate']));
                            echo $startDate . " - " . $endDate . "</a>";
                    ?>
                </h3>
                </br></br>
                    <?php
                        $budgetQuery = mysqli_query($connection, "select * from budgets where owner='" . $user . "' and archived='false'");
                        $budgetCount = mysqli_fetch_assoc($budgetQuery);
                        if($budgetCount == 0) {                     
                    ?> 
                <table>
                    <tr>
                        <th style="border-bottom:0px;">Categories <?php include('categorymanager.php');?> </th>
                        <th style="border-bottom:0px;">Projected Expenses</th>
                    </tr> 
                </table>
            <div class="col-12" style="border:1px solid #ddd;text-align:center;">No projected expenses.</div>
                    <table>
                            <?php 
                                $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
                                $balance = mysqli_fetch_assoc($balanceQuery);
                                echo "<td style='border-top:0px;'><b>Remaining Balance</b></td>";
                                echo "<td style='border-top:0px;' class='money'><b>$ " . number_format($balance["balance"]) . "</b></td>";
                            ?>
                    </table>

                <?php } else { ?>

                <table>
                    <tr>
                        <th style="border-bottom:0px;">Categories <?php include('categorymanager.php');?> </th>
                        <th style="border-bottom:0px;">Projected Expense</th>
                    </tr> 
                <?php
                    // retrieve budget categories by group
                    $budgetItemQuery = mysqli_query($connection, "select * from budgets where owner='" . $user . "' and archived='false' group by category");
                    while($budgetItem = mysqli_fetch_assoc($budgetItemQuery)) {
                        echo "<tr><td>" . $budgetItem['category'] . "</td>";
                        $projectedExpenseQuery = mysqli_query($connection, "select sum(amount) as total from budgets where owner='" . $user . "' and category='" . $budgetItem['category'] . "' and archived='false'");
                        while($projectedExpense = mysqli_fetch_assoc($projectedExpenseQuery)) {

                            echo "<td class='money'>$ " . number_format((float)$projectedExpense['total'], 2, '.', '') . "<a href='RemoveBudgetItem.php?category=" . $budgetItem['id'] . "' style='float:right;'>Remove</a></tr></td>"; 
                        }
                    }
                    $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
                    $balanceResult = mysqli_fetch_assoc($balanceQuery);
                    $balance = $balanceResult["balance"];
                    
                    $projectedSpendingQuery = mysqli_query($connection, "select sum(amount) as total from budgets where owner='" . $user . "' and archived='false'");
                    $projectedSpendingResult = mysqli_fetch_assoc($projectedSpendingQuery);
                    $projectedSpending = $projectedSpendingResult['total'];
                    $projectedSavings = $balance - $projectedSpending;
        
                    echo "<td style='border-top:0px;'><b>Remaining Balance</b></td>";
                    echo "<td style='border-top:0px;' class='money'><b>$ " . $balance . "</b></td>";
                    echo "<tr><td style='border-top:0px;'><b>Projected Savings</b></td>";
                    echo "<td style='border-top:0px;' class='money'><b>$ " . number_format((float)$projectedSavings, 2, '.', '')  . "</b></td></tr>";
                    echo "<td style='border-top:0px;'><b>Total Budgeted</b></td>";
                    echo "<td style='border-top:0px;' class='money'><b>$ " . number_format((float)$projectedSpendingResult['total'], 2, '.', '') . "</b></td></tr>";
                }
                ?>  </table>

           <div class="col-6" style="padding:5px;margin-top:50px;margin-bottom:15px;">
            <h2>Add Projected Expense</h2>
                <div class="col-4" style="margin-top:25px;margin-bottom:15px;padding:0px;">Amount:</div>

                <form method="post" action="BudgetManager.php">
                    <input type="hidden" name="newBudgetItem">
                    <div class="col-12" style="padding:0px;"><span class="currency"><input  type="text" class="field"  placeholder="0.00"  style="padding-left:17px;" name="amount"></span></div>
                    <div class="col-12" style="padding:0px;margin-top:25px;">
                        <?php if(isset($connection)) { populate_categories($connection, $user); }?>
                    </div>
                    <div class="col-12" style="padding:0px;">
                    <sub><a href="#" onclick="document.getElementById('categoryManager').style.display='block'" style="float:right;margin-top:15px;">Manage Categories</a></sub>
                    </div>
                   
                    <div class="col-5" style="float:right;padding:0px;margin-top:15px;"><input type="submit" class="button" value="Add" ></div>
                </form>
            </div>

                     <div class="col-6" style="padding:10px;margin-top:40px;margin-bottom:15px;">
            <h2>Budget History</h2>
                <?php
                    $budgetHistoryQuery = mysqli_query($connection, "select distinct startDate, endDate from budgets where owner='" . $user . "' and archived='true'");
                    if(mysqli_num_rows($budgetHistoryQuery) == 0) {
                        echo "<div style='border:1px solid #ddd;padding:1em;text-align:center;margin-top:25px;height:190px;'>Budget archive empty.</div>";
                    }
                    else {
                        echo "<div class='col-12' style='border:1px solid #ddd;padding:1em;text-align:center;margin-top:50px;'>";
                        while($budgetHistory = mysqli_fetch_assoc($budgetHistoryQuery)) {
                            echo "<a href='#'>" . date("F Y", strtotime( $budgetHistory['startDate'])) . "</a></br>";
                        }
                        echo "</div>";
                    }
                ?>
                 <div class="col-5" style="float:right;padding:0px;margin-top:15px;"><input type="button" onclick="document.getElementById('ArchiveBudget').style.display='block'" class="button" value="Archive" ></div>
            </div>


                 <?php include('Timeframes.php'); ?>
                 <?php include('ArchiveBudget.php'); ?>
        </div>
    </div>
</div>
