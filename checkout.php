<?php
if ($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2) {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}
?>
<style>
    .product-logo {
        width: 7em;
        height: 7em;
        object-fit: cover;
        object-position: center center
    }
</style>
<section class="py-3">
    <div class="container">
        <div class="content px-3 py-5 bg-gradient-maroon">
            <h3 class=""><b>Cart List</b></h3>
        </div>
        <div class="row mt-n4  justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow">
                    <div class="card-body">
                        <div class="container-fluid">
                            <?php
                            $cart_total = $conn->query("SELECT SUM(c.quantity * p.price) FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join category_list cc on p.category_id = cc.id where customer_id = '{$_settings->userdata('id')}' ")->fetch_array()[0];
                            $cart_total = $cart_total > 0 ? $cart_total : 0;
                            ?>
                            <form action="" id="order-form">
                                <input type="hidden" name="total_amount" value="<?= $cart_total ?>">


                                <h6 class="mt-4" style="font-weight: 700;" for="exampleFormControlSelect1">Delivery address</h6>
                                <div style="border: 1px solid rgba(0,0,0,0.1);padding:10px 20px;margin:20px 0">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label style="font-weight: 500;" for="exampleFormControlSelect1">Province</label>
                                                <select required class="form-control" id="province" name="province">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label style="font-weight: 500;" for="exampleFormControlSelect1">Municipality</label>
                                                <select required class="form-control" id="municipalities" name="municipalities">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label style="font-weight: 500;" for="exampleFormControlSelect1">Barangay</label>
                                                <select required class="form-control" id="barangay" name="barangay">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label style="font-weight: 500;" for="exampleInputEmail1">House number/Street/Purok</label>
                                                <input type="text" required class="form-control" name="delivery_address">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label for="delivery_address" class="control-label">Payment method: </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="1" name="payment_type" required>
                                    <label class="form-check-label" for="exampleRadios1">
                                        Cash on Delivery
                                    </label>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" name="payment_type" required>
                                        <label class="form-check-label" for="exampleRadios1">
                                            Via GCash
                                        </label>
                                    </div>
                                    <!-- <a role="button" class="text-primary text">Merchant GCash OR code</a> -->
                                </div>
                                <div class="py-1 text-center mt-5 d-flex justify-content-between">
                                    <h3><b>Total: <?= format_num($cart_total, 2) ?></b></h3>
                                    <button class="btn btn-lg btn-deafault text-light bg-gradient-maroon col-lg-4 col-md-6 col-sm-12 col-xs-12 rounded-pill">Place Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let base = `<?php echo base_app; ?>`
    $(document).ready(function() {
        $.getJSON(`services/province.json`, function(data) {
            data.sort(function(a, b) {
                var nameA = a.name.toUpperCase();
                var nameB = b.name.toUpperCase();
                if (nameA < nameB) {
                    return -1;
                }
                if (nameA > nameB) {
                    return 1;
                }
                return 0;
            });
            $('#province').html('<option value="">Select Province</option>');
            $('#municipalities').html('<option value="">Select Municipality</option>');
            $('#barangay').html('<option value="">Select Barangay</option>');
            data.forEach(element => {
                $('#province').append(`<option value="${element.name}" data-code="${element.code}">${element.name}</option>`);
            });
        }).fail(function(err) {
            console.log(err);
        });
    });
    $('#province').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        let code = selectedOption.data('code');
        $.getJSON(`services/municipalities.json`, function(data) {
            data = data.filter(function(item) {
                return item.provinceCode == code;
            });
            $('#municipalities').html('<option value="">Select Municipality</option>');
            $('#barangay').html('<option value="">Select Barangay</option>');
            data.forEach(element => {
                $('#municipalities').append(`<option value="${element.name}" data-code="${element.code}">${element.name}</option>`);
            });
        }).fail(function(err) {
            console.log(err);
        });
    });

    $('#municipalities').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        let code = selectedOption.data('code');
        $.getJSON(`services/barangay.json`, function(data) {
            data = data.filter(function(item) {
                if (!item.cityCode) {
                    return item.municipalityCode == code;
                }
                return item.cityCode == code;
            });
            $('#barangay').html('<option value="">Select Barangay</option>');
            data.forEach(element => {
                $('#barangay').append(`<option value="${element.name}" data-code="${element.code}">${element.name}</option>`);
            });
        }).fail(function(err) {
            console.log(err);
        });
    });
</script>
<script>
    $(function() {
        $('#order-form').submit(function(e) {
            e.preventDefault()
            start_loader()
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=place_order',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error occurred.", 'error')
                    end_loader()
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.replace('./')
                    } else {
                        alert_toast("An error occurred.", 'error')
                    }
                    end_loader()
                }
            })
        })
    })
</script>