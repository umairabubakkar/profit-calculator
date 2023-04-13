<?php
include 'config.php';

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql = "SELECT o.order_id, o.service_id, o.date, o.credit, o.currency_id, o.qnt, o.ORDERAPI_ID, o.ORDERAPI, c.code, c.rate, a.id, a.gateway, a.credits, s.service_name, ap.setting, ap.value, pc.code AS purchase_currency_code, pc.rate AS purchase_currency_rate, o.purchase_cost
        FROM tbl_order_imei o
        LEFT JOIN tbl_currencies c ON o.currency_id = c.id
        LEFT JOIN tbl_apiserversservices a ON o.ORDERAPI_ID = a.id AND o.ORDERAPI = a.gateway
        LEFT JOIN tbl_services_imei s ON o.service_id = s.id
        LEFT JOIN tbl_apiservers ap ON o.ORDERAPI = ap.gateway AND ap.setting = 'currency'
        LEFT JOIN tbl_currencies pc ON ap.value = pc.code
        WHERE o.user_can_c = 4 AND o.date >= '$start_date' AND o.date <= '$end_date'";






$result = $conn->query($sql);
$currency_profit_sql = "SELECT c.code, SUM((o.credit * o.qnt) - (IF(o.ORDERAPI = 'unlockbase', o.purchase_cost * o.qnt, a.credits * o.qnt * pc.rate * c.rate))) AS total_profit
        FROM tbl_order_imei o
        LEFT JOIN tbl_currencies c ON o.currency_id = c.id
        LEFT JOIN tbl_apiserversservices a ON o.ORDERAPI_ID = a.id AND o.ORDERAPI = a.gateway
        LEFT JOIN tbl_services_imei s ON o.service_id = s.id
        LEFT JOIN tbl_apiservers ap ON o.ORDERAPI = ap.gateway AND ap.setting = 'currency'
        LEFT JOIN tbl_currencies pc ON ap.value = pc.code
        WHERE o.user_can_c = 4 AND o.date >= '$start_date' AND o.date <= '$end_date'
        GROUP BY o.currency_id";




$currency_profit_result = $conn->query($currency_profit_sql);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Report</h1>
<?php
echo "<center><h2>Profit Summary by Currency:</h2></center>";
echo "<div class='currency-summary'>";
if ($currency_profit_result->num_rows > 0) {
    while ($currency_profit_row = $currency_profit_result->fetch_assoc()) {
        echo "<div class='currency-summary-item'>";
        echo "<span class='currency-code'>{$currency_profit_row['code']}</span>";
        echo "<span class='currency-profit'>{$currency_profit_row['total_profit']}</span>";
        echo "</div>";
    }
} else {
    echo "<p>No profit summary available.</p>";
}
echo "</div>";


?>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Order ID</th>
            <th>Service Name</th>
            <th>Purchase Cost</th>
            <th>Sale Price</th>
            <th>Currency</th>
            <th>Gateway Name</th>
            <th>Total Profit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $sale_price = $row['credit'] * $row['qnt'];
        if ($row['ORDERAPI'] == 'unlockbase') {
            $purchase_cost = $row['purchase_cost'] * $row['qnt'];
        } else {
            $purchase_cost = $row['credits'] * $row['qnt'] * $row['purchase_currency_rate'] * $row['rate'];
        }
        $total_profit = $sale_price - $purchase_cost;
                $highlight = '';
                if ($purchase_cost == 0) {
                    $highlight = ' class="highlight"';
                } elseif ($total_profit <= 0) {
                    $highlight = ' class="negative-profit"';
                }
                echo "<tr{$highlight}>
            <td>{$row['date']}</td>
            <td>{$row['order_id']}</td>
            <td>{$row['service_name']}</td>
            <td>{$purchase_cost}</td>
            <td>{$sale_price}</td>
            <td>{$row['code']}</td>";
            
        if ($row['gateway'] == '') {
            echo "<td>Manual Order</td>";
        } else {
            echo "<td>{$row['gateway']}</td>";
        }

        echo "<td>{$total_profit}</td>
        </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No records found.</td></tr>";
        }
        ?>
    </tbody>
</table>



</body>
</html>