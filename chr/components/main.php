<div class="main-qr .poppins gap-1">
    <div class="log-section gap-1">
        <div class="head poppins-bold d-flex justify-content-center p-2">Scan Result</div>

        <div class="user-cred gap-1 px-2 py-3">
            <div class="student-id">Student ID: <span id="student-id"></span></div>
            <div class="student-name">Student Name: <span id="student-name"></span></div>
        </div>

        <div id="payment-items" class="payment-items p-2 gap-1">
            Items:
            <div id="item-list" class="vstack px-3">

            </div>
        </div>

        <div class="payment-amount p-2">Amount: <span id="payment-amount"></span></div>

        <div class="action d-flex justify-content-evenly align-items-center gap-3 p-2">
            <button id="rescan" class="btn btn-danger">Rescan</button>
            <button id="approve" class="btn btn-primary">Approve</button>
        </div>
    </div>
    <div class="scanning-section">
        <div id="reader" class="reader hide"></div>
        <div id="start-scan-btn" class="btn btn-primary">Start Scanning</div>

        <!--<input type="file" id="qr-input-file" accept="image/*">-->
    </div>
</div>