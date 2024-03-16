<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3 bg-gradient-maroon">
            <h3><b>Order List</b></h3>
        </div>
        <div class="row mt-n4 justify-content-center align-items-center flex-column">
            <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow">
                    <div class="card-body" id="order_table_container">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
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
        font-size: 14px;
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
        font-size: 14px;
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
<!-- Modal -->
<div class="modal fade" id="modalOrderDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding-bottom: 0;margin-bottom:0" id="">
                <div class="row">
                    <div class="col-lg-6">

                        <div id="order_details_body" style="padding:20px;border:1px solid rgba(0,0,0,.1);height:calc(100vh - 100px);display:flex;flex-direction:column">

                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex" style="gap:10px;padding:20px 20px 20px 20px;flex-direction:column;border:1px solid rgba(0,0,0,.1);height:calc(100vh - 100px)">
                            <h6>Message</h6>
                            <div style="flex:1;overflow:auto" id="chat-content">

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
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div> -->
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade " id="uploadGCashReference" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload GCash Reference</h5>
            </div>
            <div class="modal-body">
                <form id="gcashReferenceForm">
                    <input type="hidden" name="order_id" id="order_id">
                    <div class="form-group">
                        <label for="image_reference">Select image:</label>
                        <input accept="image/*" type="file" name="image" class="form-control-file" id="image_reference">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $userId = <?php echo $_SESSION['userdata']['id'] ?>;
    let id;
    $firstLoad = false;
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

    function renderOrderDetails() {
        $.ajax({
            url: 'orders/view_order.php?id=' + id,
            error: err => {
                console.log(err)
                alert("An error occured");
            },
            success: function(resp) {
                if (resp) {
                    $('#order_details_body').html(resp)
                }
            }
        });
    }
    $(function() {
        start_loader();
        renderOrderTable();
        $(document).on('click', '.view-order', function(e) {
            $firstLoad = true;
            $('#modalOrderDetails').modal('show');
            id = $(this).attr('data-id');
            $('#order_id').val(id);
            renderOrderDetails();
            setTimeout(() => {
                get_message();
            }, 3000);
        })
    });
    $(document).on('submit', '#gcashReferenceForm', function(e) {
        e.preventDefault();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=add_gcash_reference",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err)
                alert("An error occured");
                end_loader();
            },
            success: function(resp) {
                console.log(resp);
                $('#uploadGCashReference').modal('hide');
                $('#image_reference').val('');
                end_loader();
                renderOrderDetails();
                renderOrderTable();

            }
        });

    });


    function send_message() {

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=send_message",
            method: "POST",
            type: 'POST',
            data: {
                order_id: id,
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
                order_id: id
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
                <span>${moment(new Date(element.date_created)).format('MMM D, YYYY | h:mm:ss a')}</span>
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

    setInterval(() => {
        get_message()
    }, 2000);
    $('#send').click(function(e) {
        e.preventDefault();
        send_message();
    });

    function renderOrderTable() {
        $.ajax({
            url: _base_url_ + "orders/table_order.php",
            type: 'POST',

            error: err => {
                console.log(err)
                end_loader();
            },
            success: function(resp) {
                $('#order_table_container').html(resp);
                end_loader();
            }
        })
    }
</script>