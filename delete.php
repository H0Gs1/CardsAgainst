<div class="col-md-9">
                   
                <?php
                    //checks if the User management tab is clicked
                    if (isset($_GET['user_management']) && $_GET['user_management'] == 1) {
                        $Account = array('UserName', 'Password', 'Email', 'UserRole'); //references the table headings
                        $accountCount = count($Pack); //gets $Account's number of attributes
                        $sql = "SELECT * FROM Pack;"; 
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);//checks if there is an actual table
                        
                        if ($resultCheck > 0) {
                            //prints table html
                            echo "<table class='table'>"; 
                            echo "<tr>";
                            echo "<th>Username</th>";
                            echo "<th>Password</th>";
                            echo "<th>Email</th>";
                            echo "<th>UserRole</th>";
                            echo "</tr>";

                            //processes table data into table
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='clickable-row' data-id='" . $row['Id'] . 
                                "' data-userName='" . $row['UserName'] .
                                "' data-password='" . $row['Password'] . 
                                "' data-email='" . $row['Email'] . 
                                "' data-userRole='" . $row['UserRole'] . "'>";
                                for ($i = 0; $i < $accountCount; $i++) {
                                    echo "<td>" . $row[$Account[$i]] . "</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                        
                    }
                    
                    else {
                        echo " ";
                    }
                ?>

            <!-- Modal Structure for Editing users -->
            <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel">User Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editUserForm">
                                <div class="mb-3">
                                    <label for="modal-user-id" class="form-label" style="display: none;">Id</label>
                                    <input type="text" class="form-control" id="modal-user-id" disabled style="display: none;">
                                </div>
                                <div class="mb-3">
                                    <label for="modal-user-name" class="form-label">Username</label>
                                    <textarea class="form-control" id="modal-user-name" disabled></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modal-user-password" class="form-label">Password</label>
                                    <textarea class="form-control" id="modal-user-password" disabled></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modal-user-email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="modal-user-email" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="modal-user-role" class="form-label">User Role</label>
                                    <select class="form-control" id="modal-user-role" disabled>
                                        <option value="1">Admin</option>
                                        <option value="0">User</option>
                                    </select>
                                </div>
                            </form>
                            <button type="button" class="btn btn-success" id="saveUserChangesButton" style="display:none;">Save Changes</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="editUserButton">Edit</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
if (window.location.search.includes("card_management=1")) {
    $("#add-card-button-container").show();
    }
    //checks if user tab is active
    if (window.location.search.includes("user_management=1")) {
        
        //fetches the row data when the row is clicked
        $(".clickable-row").click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var password = $(this).data('password');
            var email = $(this).data('email');
            var userRole = $(this).data('userRole');

            // Set values in modal for users
            $("#modal-user-id").val(id);
            $("#modal-user-name").val(content);
            $("#modal-user-password").val(isAnswer);
            $("#modal-user-email").val(isCommunity);
            $("#modal-user-userRole").val(packName);

            // Shows the user modal
            var myModal = new bootstrap.Modal(document.getElementById('userModal'));
            myModal.show();

            // Enable fields for editing on click of "Edit" button
            $("#editUserButton").click(function() {
                $("#modal-user-name").prop('disabled', false);
                $("modal-user-password").prop('disabled', false);
                $("#modal-user-email").prop('disabled', false);
                $("#modal-user-userRole").prop('disabled', false);
                $("#saveUserChangesButton").show();
                $("#editUserdButton").hide();
            });

            // Handle Save Changes for Card
            $("#saveUserChangesButton").off('click').on('click', function() {
                var updatedData = {
                    id: $("#modal-user-id").val(),
                    username: $("#modal-user-name").val(),
                    password: $("modal-user-password").val(),
                    email: $("#modal-user-email").val(),
                    userrole: $("#modal-user-userRole").val()
                };
                //ajax for posting to the database
                $.ajax({
                    url: 'update_account.php',
                    type: 'POST',
                    data: updatedData,
                    success: function(response) {
                        alert('User updated successfully!');
                        myModal.hide();
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating the user. Please try again.');
                    }
                });
            });

            // Close modal and reset form when "Close" or "X" is clicked
            $("#userModal .btn-close, #userModal .btn-danger").click(function() {
                // Reset the fields to disabled on close
                $("#modal-user-name").prop('disabled', true);
                $("modal-user-password").prop('disabled', true);
                $("#modal-user-email").prop('disabled', true);
                $("#modal-user-userRole").prop('disabled', true);
                $("#saveUserChangesButton").hide();
                $("#editUserButton").show();
                myModal.hide();
            });
        });
    }
            </script>