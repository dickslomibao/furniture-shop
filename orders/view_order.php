<?php
require_once('./../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT *  from `order_list` where id = '{$_GET['id']}' and customer_id = '{$_settings->userdata('id')}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    } else {
        echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}
?>
<style>
    #uni_modal .modal-footer {
        display: none !important;
    }
</style>

<div id="item_list" class="list-group" style="flex:1">
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">Order Reference Code:</label>
                <div class=""><?= isset($code) ? $code : '' ?></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">Status:</label>
                <div class="">
                    <?php
                    $status = isset($status) ? $status : '';
                    switch ($status) {
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
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="" class="control-label">Delivery Address:</label>
                <div class=""><?= isset($delivery_address) ? str_replace(["\r\n", "\r", "\n"], "<br>", $delivery_address) : '' ?>
                    <?= isset($province) ? $province . ", "  : '' ?>
                    <?= isset($municipality) ? $municipality . ", "  : '' ?>
                    <?= isset($barangay) ? $barangay   : '' ?>
                </div>
            </div>
        </div>
        <?php
        $gt = 0;
        $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id and (expiration IS NULL or date(expiration) > '" . date("Y-m-d") . "') ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
        while ($row = $order_items->fetch_assoc()) :
            $gt += $row['price'] * $row['quantity'];
        ?>
            <div style="" class="" data-id='<?= $row['id'] ?>' data-max='<?= format_num($row['available'], 0) ?>'>
                <div class="d-flex align-items-center">
                    <div class="col-2 text-center">
                        <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-logo">
                    </div>
                    <div class="col-auto flex-shrink-1 flex-grow-1">
                        <div style="line-heigth:1em">
                            <h4 class='mb-0'><?= $row['product'] ?></h4>
                            <div class="text-muted"><?= $row['brand'] ?></div>
                            <div class="text-muted"><?= $row['category'] ?></div>
                            <div class="text-muted d-flex w-100">
                                <?= format_num($row['quantity'], 0) ?> x <?= format_num($row['price'], 2) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <h4><b><?= format_num($row['price'] * $row['quantity'], 2) ?></b></h4>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>

<div class="d-flex justify-content-between align-items-center">
    <?php if (in_array($status, [1, 2, 3])) { ?>
        <div>
            <button data-toggle="modal" class="btn btn-primary" data-target="#uploadGCashReference">
                Upload GCash Reference
            </button>
        </div>
    <?php } ?>

    <div class="">
        <h4><b>Grand Total: <?= format_num($gt, 2) ?></b></h4>
    </div>
</div>