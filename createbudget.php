<?php
    include 'header.php';
    include_once 'functions.php';
    session_start();
    $user = validate_session();
    if (isset($_POST['newBudgetItem'])) {
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $newBudgetItem = mysqli_query($connection, "insert into budgets (owner, category, amount, datecreated, datefinished) VALUES ('" . $user . "','" . $category . "','" . $amount . "','" . $today . "', 'n/a')");
    }

?>
                        
<div class="col-12">
    <div class="col-7 center shadow"  style="padding:0px;">
        <div class="col-12" style="padding:0px;">
            <div class="col-12"><a href="dashboard.php">Back</a></div>
            <div class="col-12">
                <h2>Manage Budget</h2></br>
                    <?php
                        $budgetQuery = mysqli_query($connection, "select * from budgets where owner='" . $user . "'");
                        $budgetCount = mysqli_fetch_assoc($budgetQuery);
                        if($budgetCount == 0) {                     
                    ?> 
                <table>
                    <tr>
                        <th style="border-bottom:0px;">Categories <?php include('categorymanager.php');?> 
                        <sub><a href="#" onclick="document.getElementById('categoryManager').style.display='block'">Edit</a></sub></th>
                        <th style="border-bottom:0px;">Projected Expense</th>
                    </tr> 
                </table>
            <div class="col-12" style="border:1px solid #ddd;text-align:center;">No projected expenses.</div>
                    <table>
                            <?php 
                                $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
                                $balance = mysqli_fetch_assoc($balanceQuery);
                                echo "<td style='border-top:0px;'><b>Remaining Balance</b></td>";
                                echo "<td style='border-top:0px;'><b>$ " . number_format($balance["balance"]) . "</b></td>";
                            ?>
                    </table>

                <?php } else { ?>

                <table>
                    <tr>
                        <th style="border-bottom:0px;">Categories <?php include('categorymanager.php');?> 
                        <sub><a href="#" onclick="document.getElementById('categoryManager').style.display='block'"> Edit</a></sub></th>
                        <th style="border-bottom:0px;">Projected Expense</th>
                    </tr> 
                <?php
                    // retrieve budget categories by group
                    $budgetItemQuery = mysqli_query($connection, "select * from budgets where owner='" . $user . "' and datefinished='n/a' group by category");
                    while($budgetItem = mysqli_fetch_assoc($budgetItemQuery)) {
                        echo "<tr><td>" . $budgetItem['category'] . "</td>";
                        $projectedExpenseQuery = mysqli_query($connection, "select sum(amount) as total from budgets where owner='" . $user . "' and category='" . $budgetItem['category'] . "'");
                        while($projectedExpense = mysqli_fetch_assoc($projectedExpenseQuery)) {
                            echo "<td>$ " . number_format($projectedExpense['total']) . "</tr></td>"; 
                        }
                    }
                    $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
                    $balance = mysqli_fetch_assoc($balanceQuery);
                    echo "<td style='border-top:0px;'><b>Remaining Balance</b></td>";
                    echo "<td style='border-top:0px;'><b>$ " . number_format($balance["balance"]) . "</b></td>";
                }
                ?>  </table>
           <?php include('budgetmanager.php'); ?>
                <div class="col-2" style="float:right;padding:0px;margin-top:10px;"><input type="button" class="button" value="Add Item" onclick="document.getElementById('budgetManager').style.display='block'"></div>
            </div>
        </div>
    </div>
</div>
