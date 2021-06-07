setTimeout(showAlert, 200); //ask for archive old notices after 200ms

function showAlert() {
    if (window.confirm("There are overdue notices still on the Bulletin. Do you want to archive old notices?")) {
        let form = document.getElementById("archive_old");
        form.submit();//post archive old notices to the same page
    }
}