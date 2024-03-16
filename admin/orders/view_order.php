<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `order_list` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    #order-logo {
        max-width: 100%;
        max-height: 20em;
        object-fit: scale-down;
        object-position: center center;
    }


    .sender {
        width: 100%;
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        align-items: start;
    }

    .sender span {
        margin-top: 5px;
        font-size: 10px;
    }

    .sender .content {
        padding: 10px;
        border-radius: 8px;
        background-color: #0D6EFD;
        width: auto;
        max-width: 70%;
        color: white;
    }

    .user {
        width: 100%;
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        align-items: end;
    }

    .user span {
        margin-top: 5px;
        font-size: 10px;
    }

    .user .content {
        padding: 10px;
        border-radius: 8px;
        background-color: #f7f7f7;
        width: auto;
        max-width: 70%;
    }

    #chat-content::-webkit-scrollbar {
        overflow: hidden;
    }


    /* Hide scrollbar for IE, Edge and Firefox */
    #chat-content {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    pre {

        all: unset;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
<!-- <div class="content py-5 px-3 bg-gradient-danger">
    <h2><b><?= isset($code) ? $code : '' ?> Order Details</b></h2>
</div> -->
<div class="row">
    <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
        <div class="card rounded-0">
            <div class="card-header py-1">
                <div class="card-tools">
                    <?php if (isset($status) && $status < 4) : ?>
                        <button class="btn btn-info btn-sm bg-gradient-info rounded-0" type="button" id="update_status">Update Status</button>
                    <?php endif; ?>
                    <button class="btn btn-navy btn-sm bg-gradient-navy rounded-0" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                    <button class="btn btn-danger btn-sm bg-gradient-danger rounded-0" type="button" id="delete_data"><i class="fa fa-trash"></i> Delete</button>
                    <a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=orders"><i class="fa fa-angle-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 printout">
        <div class=" rounded-0" style="background: white;height:calc(100vh - 135px)">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="mb-3">
                                <label for="" class="control-label">Order Reference Code:</label>
                                <div class="pl-4"><?= isset($code) ? $code : '' ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="control-label">Delivery Address:</label>
                                <div class="pl-4">
                                    <?= isset($delivery_address) ? str_replace(["\r\n", "\r", "\n"], "<br>", $delivery_address) : '' ?>
                                    <?= isset($province) ? $province . ", "  : '' ?>
                                    <?= isset($municipality) ? $municipality . ", "  : '' ?>
                                    <?= isset($barangay) ? $barangay   : '' ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="mb-3">
                                <label for="" class="control-label">Status:</label>
                                <div class="pl-4">
                                    <?php
                                    $status = isset($status) ? $status : '';
                                    switch ($status) {
                                        case 1:
                                            echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Waiting for payment</span>';
                                            break;
                                        case 2:
                                            echo '<div class="d-flex  justify-content-between">
                                            <span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill d-flex align-items-center justify-content-center">Payment pending - (Under seller validation)</span>
                                            <a role="button" data-toggle="modal" data-target="#viewPaymentModal" class="color-primary" >View Payment Reference</a>
                                            </div>  ';
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
                    </div>
                    <div id="item_list" class="list-group">
                        <?php
                        $gt = 0;
                        $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id and (expiration IS NULL or date(expiration) > '" . date("Y-m-d") . "') ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
                        while ($row = $order_items->fetch_assoc()) :
                            $gt += $row['price'] * $row['quantity'];
                        ?>
                            <div class="list-group-item cart-item" data-id='<?= $row['id'] ?>' data-max='<?= format_num($row['available'], 0) ?>'>
                                <div class="d-flex w-100 align-items-center">
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
                    <?php if ($order_items->num_rows <= 0) : ?>
                        <h5 class="text-center text-muted">Order Items is empty.</h5>
                    <?php endif; ?>
                    <div class="d-flex justify-content-end py-3">
                        <div class="col-auto">
                            <h3><b>Grand Total: <?= format_num($gt, 2) ?></b></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6" style="background-color: white;">
        <div class="d-flex" style="gap:10px;padding:20px 20px 20px 20px;flex-direction:column;height:calc(100vh - 135px)">
            <h6>Message</h6>
            <div style="flex:1;overflow:auto" id="chat-content">
                <!-- <div class="sender">
                    <div class="content">
                        asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada
                    </div>
                    <span>December 9 2001</span>
                </div>
                <div class="sender">
                    <div class="content">
                        asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada
                    </div>
                    <span>December 9 2001</span>
                </div>
                <div class="sender">

                    <div class="content">
                        asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada
                    </div>
                    <span>December 9 2001</span>
                </div>
                <div class="user">

                    <div class="content">
                        asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada asdadasdsada asdsadadadaad ad asd ada
                    </div>
                    <span>December 9 2001</span>
                </div> -->
            </div>
            <div class="d-flex" style="gap:10px">
                <div class="" style="flex:1">
                    <textarea class="form-control w-100" id="message" rows="2" style="font-size: 15px;"></textarea>
                </div>
                <button class="btn btn-primary" id="send" style="width:100px">
                    Send
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View GCash Payment Reference</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    $images = $conn->query("SELECT * from `order_payment_image` where order_id = '{$_GET['id']}' ");
                    if ($images->num_rows > 0) {
                        while ($row  = $images->fetch_assoc()) {
                    ?>
                            <div class="col-lg-6">
                                <img src="<?= base_url ?>uploads/reference/<?= $row['url'] ?>" class="img-fluid" />
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <p style="margin: 0;" class="mt-2">Note: Upon accepting payment the order status will now go to Processing.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="accept_payment">Accept Payment</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Decline Payment</button>
            </div>
        </div>
    </div>
</div>


<noscript id="print-header">
    <div>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold">
                        <large><?= $_settings->info('name') ?></large>
                    </div>
                    <div class="text-center font-weight-bold">
                        <large>order Details</large>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</noscript>
<script>
    $userId = <?php echo $_SESSION['userdata']['id'] ?>

    function print_t() {
        var h = $('head').clone()
        var el = ""
        $('.printout').map(function() {
            var p = $(this).clone()
            p.find('.btn').remove()
            p.find('.card').addClass('border')
            p.removeClass('col-lg-8 col-md-10 col-sm-12 col-xs-12')
            p.addClass('col-12')
            el += p[0].outerHTML
        })
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("order Details - Print View")
        var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
        nw.document.querySelector('head').innerHTML = h.html()
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML
        nw.document.querySelector('body').innerHTML += el
        nw.document.close()
        start_loader()
        setTimeout(() => {
            nw.print()
            setTimeout(() => {
                nw.close()
                end_loader()
            }, 200);
        }, 300);
    }
    $(function() {
        $('#print').click(function() {
            print_t()
        })
        $('#assign_team').click(function() {
            uni_modal("Assign a Team", 'orders/assign_team.php?id=<?= isset($id) ? $id : '' ?>')
        })
        $('#delete_data').click(function() {
            _conf("Are you sure to delete this order permanently?", "delete_order", ["<?= isset($id) ? $id : '' ?>"])
        })
        $('#update_status').click(function() {
            uni_modal("Update Status", "orders/update_status.php?id=<?= isset($id) ? $id : '' ?>")
        })
    })

    function delete_order($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_order",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.replace("./?page=orders");
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }

    $firstLoad = true;
    let msgListScrolling = false;

    $('#chat-content').on('scroll', function() {
        if ($(this).scrollTop() +
            $(this).innerHeight() + 100 >=
            $(this)[0].scrollHeight) {
            msgListScrolling = true;
        } else {
            msgListScrolling = false;
        }
        console.log(msgListScrolling);
    });

    setInterval(() => {
        get_message();
    }, 2000);

    function send_message() {
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=send_message",
            method: "POST",
            type: 'POST',
            data: {
                order_id: <?php echo $id; ?>,
                message: $('#message').val(),
            },
            error: err => {
                $('#message').val('')
            },
            success: function(resp) {
                $('#message').val('')
            }
        })
    }

    function get_message() {
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_message",
            method: "POST",
            type: 'POST',
            dataType: 'json',
            data: {
                order_id: <?php echo $id; ?>,
            },
            error: err => {
                $('#message').val('')
            },
            success: function(resp) {
                $('#chat-content').html('');
                for (let index = 0; index < resp.length; index++) {
                    const element = resp[index];
                    const isSender = element.sender_id == $userId;
                    $('#chat-content').append(`
                <div class="${isSender ? 'user' : 'sender'}">
                <div class="content">
<pre>${element.text}</pre>
                </div>
                <span>December 9 2001</span>
            </div>
                `);
                }

                if (msgListScrolling) {

                    $('#chat-content').animate({
                        scrollTop: $('#chat-content')[0].scrollHeight
                    }, "fast");
                }
                if ($firstLoad) {
                    $('#chat-content').animate({
                        scrollTop: $('#chat-content')[0].scrollHeight
                    }, "fast");
                    $firstLoad = false;
                }
            }
        })
    }
    $('#send').click(function(e) {
        e.preventDefault();
        send_message();
    });
    $('#accept_payment').click(function(e) {
        e.preventDefault();
        start_loader();
        $formData = new FormData(),

            $formData.append('status', 5);
        $formData.append('id', <?= $id ?>);
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=update_order_status",
            data: $formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err)
                alert_toast("An error occured", 'error');
                end_loader();
            },
            success: function(resp) {
                end_loader();
                location.reload();
            }
        })
    });
</script>