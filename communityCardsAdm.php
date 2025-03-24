<?php
include_once('db_connecter.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Management</title>
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    
    <style>
        #loadingSpinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Optional: adds a semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Make sure it's on top */
        }

        table {
            width: 90%;
            margin: 0 auto;
            word-wrap: break-word;
        }

        td, th {
            padding: 8px 12px;
            font-size: 14px;
        }

        /* Color-coded rows based on status */
        .row-new {
            background-color: #d4edda;
        }

        .row-rejected {
            background-color: #f8d7da;
        }

        .row-other {
            background-color: #fefefe;
        }

        /* Modal customization */
        .modal-content {
            padding: 20px;
        }

        .tab button:hover {
            background-color: #ddd;
        }
        .btn {
            background-color: var(--bs-secondary);
            color: rgb(255, 255, 255) !important; /* Set the text color of the close button */
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <form id="filter-form">
        <div class="row">
            <div class="col-md-4">
                <label for="filter-isAnswer">Answer:</label>
                <select id="filter-isAnswer" class="form-control">
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="filter-packName">Pack Name:</label>
                <select id="filter-packName" class="form-control">
                    <option value="">All</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="filter-status">Status:</label>
                <select id="filter-status" class="form-control">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>
</div>

<div class="container mt-4">
    <h5>Press on content to edit it</h5>
    <div id="card-table-container">
        <table class="table table-bordered table-hover" id="card-table">
            <thead>
                <tr>
                    <th>Content</th>
                    <th>Likes</th>
                    <th>Answer?</th>
                    <th>Pack Name</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic data will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="cardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardModalLabel">Edit Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-card-form">
                    <div class="mb-3">
                        <label for="modal-content-input" class="form-label">Content</label>
                        <input type="text" class="form-control" id="modal-content-input" />
                    </div>
                    <div class="">
                        <button type="button" class="btn btn-success status-btn float-right" data-status="approved">Accept</button>
                        <button type="button" class="btn btn-danger status-btn float-right mr-3" data-status="rejected">Reject</button>
                        <button type="button" class="btn btn-primary save-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="loadingSpinner" style="display: none;">
    <img src="loading-7528_256.gif" alt="Loading...">
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    fetchCards();

    $(document).ready(function () {
    // Populate Pack Name dropdown
    $.ajax({
        url: 'load_pack.php',
        method: 'GET',
        success: function (data) {
            $('#filter-packName').html('<option value="">All</option>' + data);
        },
        error: function () {
            Swal.fire('Error', 'Unable to load packs.', 'error');
        }
    });
});


    function fetchCards(filters = {}) {
        $.ajax({
            url: 'filter_cards.php',
            method: 'GET',
            data: filters,
            success: function (response) {
                $('#card-table tbody').html(response);
                addRowColors();
            },
            error: function () {
                Swal.fire('Error', 'Unable to load cards.', 'error');
            }
        });
    }

    function addRowColors() {
        $('#card-table tbody tr').each(function () {
            const status = $(this).data('status');
            if (status === 'new') {
                $(this).addClass('row-new');
            } else if (status === 'rejected') {
                $(this).addClass('row-rejected');
            } else {
                $(this).addClass('row-other');
            }
        });
    }

    // Open modal with pre-filled content
    $(document).on('click', '#card-table tbody tr', function () {
        const cardId = $(this).data('id');
        const content = $(this).data('content');
        const status = $(this).data('status');

        $('#modal-content-input').val(content);
        $('#cardModal').data('id', cardId).modal('show');
    });

// Handle button actions inside the modal
$('.status-btn').on('click', function () {
    const status = $(this).data('status');
    const cardId = $('#cardModal').data('id');
    const content = $('#modal-content-input').val(); // Get content from the modal input field

    // Show the spinner
    $('#loadingSpinner').show();

    $.ajax({
        url: 'update_card_status.php',
        method: 'POST',
        data: { id: cardId, status: status, content: content },
        success: function () {
            Swal.fire('Success', `Card ${status}`, 'success');
            $('#cardModal').modal('hide');
            fetchCards(filters = {});
        },
        error: function () {
            Swal.fire('Error', `Failed to update card to ${status}`, 'error');
        },
        complete: function () {
            // Hide the spinner after the request is completed
            $('#loadingSpinner').hide();
        }
    });
});



    $('.save-btn').on('click', function () {
        const cardId = $('#cardModal').data('id');
        const content = $('#modal-content-input').val();

        $.ajax({
            url: 'update_card_content.php',
            method: 'POST',
            data: { id: cardId, content: content },
            success: function () {
                Swal.fire('Success', 'Content saved', 'success');
                $('#cardModal').modal('hide');
                fetchCards();
            },
            error: function () {
                Swal.fire('Error', 'Failed to save content', 'error');
            }
        });
    });

    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        const filters = {
            isAnswer: $('#filter-isAnswer').val(),
            packId: $('#filter-packName').val(),
            status: $('#filter-status').val(),
        };
        fetchCards(filters);
    });
});

</script>

</body>
</html>
