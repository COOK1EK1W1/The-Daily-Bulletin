//validate_notice.js this file is used in add_notice.php to validate the notice before adding to the main database

function validate_notice() {
    //return true;//uncomment for testing purposes to skip validation

    let start = new Date(document.getElementsByName("start_date")[0].value); //get start date
    let finish = document.getElementsByName("end_date")[0].value;
    let repeata = document.getElementById("repeat").value; //get repeat type


    if (finish == "") {
        finish = start;//if end date is blank set y to 
    } else {
        finish = new Date(finish); //convert string to date
    }

    if (start > finish) {//check if end before start
        alert("Looks like your start date is after your end date!");
        return false;
    }

    if (start.getTime() < new Date().getTime() - 86400000 && repeata == "once") {//check dates are not in the past
        alert("Your trying to set a notice that has passed");
        return false;
    }

    if (finish.getTime() < new Date().getTime() - 86400000) {//check end is not in past
        alert("Looks like you end date is in the past!");
        return false;
    }

    return confirm("Do you wish to add notice to the bulletin");//final check
}
