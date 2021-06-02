//validate_notice.js this file is used in add_notice.php to validate the notice before adding to the main database

function validate_notice() {

    let x = document.getElementsByName("start_date")[0].value;
    let y = document.getElementsByName("end_date")[0].value;
    let z = document.getElementById("repeat").value
    if (y == "") {
        document.getElementsByName("end_date")[0].value = x;
    }
    y = document.getElementsByName("end_date")[0].value;

    if (x > y && z != "once") {//check dates are not in the past
        alert("Looks like your start date is after your end date!");
        return false;
    }

    if (new Date(y) < new Date().setDate(date.getDate() + 1)) {
        alert("Looks like you end date is in the past!")
        return false;
    }

    if (!confirm("Do you wish to add notice to the bulletin")) {
        return false;

    } else { //notice is good
        return true;
    }
}
