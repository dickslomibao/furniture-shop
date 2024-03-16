<?php require_once('../config.php'); ?>

<div class="container-fluid">
    <table class="table table-stripped table-bordered">
        <colgroup>
            <col width="5%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="15%">
        </colgroup>
        <thead>
            <tr>
                <th class="p-1 text-center">#</th>
                <th class="p-1 text-center">Date Ordered</th>
                <th class="p-1 text-center">Code</th>
                <th class="p-1 text-center">Total Amount</th>
                <th class="p-1 text-center">Status</th>
                <th class="p-1 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $orders = $conn->query("SELECT * FROM `order_list` where customer_id = '{$_settings->userdata('id')}' order by abs(unix_timestamp(date_created)) desc ");
            while ($row = $orders->fetch_assoc()) :
            ?>
                <tr>
                    <td class="p-1 align-middle text-center"><?= $i++ ?></td>
                    <td class="p-1 align-middle"><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                    <td class="p-1 align-middle"><?= $row['code'] ?></td>
                    <td class="p-1 align-middle text-right"><?= format_num($row['total_amount'], 2) ?></td>
                    <td class="p-1 align-middle text-center">
                        <?php
                        switch ($row['status']) {
                            case 1:
                                echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Waiting for payment</span>';
                                break;
                            case 2:
                                echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Payment pending - (Under seller validation)</span>';
                                break;
                            case 3:
                                echo '<span class="badge badge-secondary bg-gradient-warning px-3 rounded-pill">Payment failed</span>';
                                break;
                            case 4:
                                echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Pending</span>';
                                break;
                            case 5:
                                echo '<span class="badge badge-secondary bg-gradient-primary px-3 rounded-pill">On process</span>';
                                break;
                            case 6:
                                echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Ready to deliver</span>';
                                break;
                            case 7:
                                echo '<span class="badge badge-warning bg-gradient-warning px-3 rounded-pill">Out for Delivery</span>';
                                break;
                            case 8:
                                echo '<span class="badge badge-teal bg-gradient-teal px-3 rounded-pill">Completed</span>';
                                break;
                        }
                        ?>
                    </td>
                    <td class="p-1 align-middle text-center">
                        <button class="btn btn-flat btn-sm btn-light border-gradient-light border view-order" type="button" data-id="<?= $row['id'] ?>"><i class="fa fa-eye text-dark"></i> View</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>