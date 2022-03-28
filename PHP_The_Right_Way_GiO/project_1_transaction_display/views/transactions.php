<!DOCTYPE html>
<html>
    <head>
        <title>Transactions</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
            }
            table tr th, table tr td {
                padding: 5px;
                border: 1px #eee solid;
            }
            tfoot tr th, tfoot tr td {
                font-size: 20px;
            }
            tfoot tr th {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check #</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>

            <?php 

            
            $total = ['income' => 0, "expenses" => 0, 'net' => null];
            foreach($contents as $transaction){
                $date = date('M d, Y', strtotime($transaction[0]));
                echo <<<END
                <tr>
                    <td>{$date}</td>
                    <td>{$transaction[1]}</td>
                    <td>{$transaction[2]}</td>
                    <td>{$transaction[3]}</td>
                </tr>

                END;
                
                $transaction[3] = preg_replace("/[^0-9.-]/", "", $transaction[3]); 
                if($transaction[3] > 0){
                    $total['income'] += $transaction[3];
                } else {
                    $total['expenses'] += $transaction[3];
                }
            }
            $total['net'] = $total['income'] + $total['expenses'];   

            

            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Income:</th>
                    <td><?php echo "$" . number_format($total['income'],2) ?></td>
                </tr>
                <tr>
                    <th colspan="3">Total Expense:</th>
                    <td><?php echo "$" . number_format($total['expenses'], 2) ?></td>
                </tr>
                <tr>
                    <th colspan="3">Net Total:</th>
                    <td><?php echo "$" . number_format($total['net'], 2 )?></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>