<form id="clientForm">
    <div class="row">
        <label for="studId">Student ID:</label>
        <input id="studId" type="text" autocomplete=false>
    </div>
    <div class="row">
        <label for="studFirstname">Student First Name:</label>
        <input id="studFirstname" type="text" autocomplete=false>
    </div>
    <div class="row">
        <label for="studMiddle">Student Middle Name:</label>
        <input id="studMiddle" type="text" autocomplete=false>
    </div>
    <div class="row">
        <label for="studLastname">Student Last Name:</label>
        <input id="studLastname" type="text" autocomplete=false>
    </div>
    <div class="row">
        <label for="items">Items</label><br>
        <select name="items" id="items" multiple size="5">
            <?php
                include 'request/fetchItems.php';
                fetchItems();
            ?>
        </select>
    </div>
    <div class="row">
    <label for="amount">Amount:</label>
    <input id="amount" type="text" autocomplete=false>
    </div>

    <button id="submitBtn" type="submit">Generate QR</button>
</form>
<div id="qr_container">

</div>