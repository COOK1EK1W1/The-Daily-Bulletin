setTimeout(somethine, 200);

function somethine() {
    if (window.confirm("There are overdue notices still on the Bulletin. Do you want to archive old notices?")) {
        let form = document.getElementById("archive_old");
        console.log(form);
        form.submit();
        console.log("go to somewhere");
    } else {
        console.log("stay here");
    }
}