//update.js This file is used by add_notice to update in realtime the preview

var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];


function on_text_update() {
    let title = document.getElementsByName("Title")[0].value;
    let description = document.getElementsByName("Description")[0].value;
    let teacher = document.getElementsByName("Teacher")[0].value;
    if ((title == "" && description == "" && teacher == "")) {
        title = "⠀"
    }
    document.getElementsByTagName("td")["title"].innerHTML = htmlify(title);
    document.getElementsByTagName("td")["description"].innerHTML = htmlify(description);
    document.getElementsByTagName("td")["teacher"].innerHTML = htmlify(teacher);
}


function on_date_update() {
    let text = document.getElementById("date_text");
    let x = document.getElementById("start_date").value;
    let y = document.getElementById("end_date");
    let z = document.getElementById("repeat").value;
    let enddate = document.getElementById("enddatecontainer");
    if (z == "daily" && x != "" && y.value != "") {
        text.innerHTML = "This notice will be displayed <b> every day</b> between and including <b>" + x + "</b> and <b>" + y.value + "</b>";
    }
    else if (z == "weekly" && x != "" && y.value != "") {
        let d = new Date(x);
        text.innerHTML = "This notice will be displayed <b> every " + weekday[d.getDay()] + "</b> between and including <b>" + x + "</b> and <b>" + y.value + "</b>";
    } else if (z == "once" && x != "") {
        text.innerHTML = "This notice will be displayed <b> once </b> on <b>" + x + "</b>";

    } else {
        text.innerHTML = "";
    }
    if (z == "once") {
        enddate.style = "display:none";
        y.removeAttribute("required");
        y.value = x;
    } else {
        enddate.style = "display:inline";
        y.setAttribute("required", "");
    }
}

function htmlify(string) {//convert raw text to html]
    string = string.replaceAll("\n", "<br>")

    parts = string.split("*");
    bold = false;
    string = "";
    for (var i = 0; i < parts.length; i++) {
        string += parts[i];
        if (i != parts.length - 1) {
            if (bold) {
                string += "</b>"
            } else {
                string += "<b>"
            }

        }
        bold = !bold

    }
    return string
}
