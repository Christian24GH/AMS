<div id='mk_appt' class="mk_appt m-0">
    <form id="clientForm">
        <div class="text-inputs">
            <div class="card-header d-flex justify-content-center poppins-semibold">Appointment Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="studId" class="poppins-medium">Student ID</label>
                    <input type="text" class="form-control p-3" id="studId" value='<?php echo $_SESSION['stud_id']?>' placeholder="<?php echo $_SESSION['stud_id']?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="amount" class="poppins-medium">Amount</label>
                    <input type="text" class="form-control p-3" id="amount" placeholder="Amount" required>
                </div>

                <div class="mb-3">
                    <label for="appt_date" class="poppins-medium">Appointment Date</label>
                    <input id='appt_date' type="date" class="form-control p-3" disabled>
                </div>

                <div class="mb-3">
                    <label for="appt_date" class="poppins-medium">Set Shift</label>
                    <select class="form-select form-select p-3" name="appt_shift" id="appt_shift" required>
                        <option value="">Shift Type</option>
                        <option value="Morning">Morning Shift 7:00am - 11:00am</option>
                        <option value="Afternoon">Afternoon Shift 11:00am - 5:00pm</option>
                    </select>
                </div>

                <label for="" class="poppins-medium">Payable items</label>
                <div class="accordion mb-5 mw-25 mw-100" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                        <button class="accordion-button collapsed p-3" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Select Items
                        </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body accodion_paddingX">
                                <?php
                                    include "requestHandler/fetchItems.php";
                                    fetchItems();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button class='btn btn-primary' id="submitBtn" type="submit">Generate QR</button>
                </div>
            </div>
        </div>   
    </form>
</div>