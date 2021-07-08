//update.js This file is used by add_notice to update in realtime the preview

var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];


function on_text_update() {
    let title = document.getElementsByName("Title")[0].value;
    let description = document.getElementsByName("Description")[0].value;
    let teacher = document.getElementsByName("Teacher")[0].value;
    let tags = document.getElementsByName("tags")[0].value.split(", ");

    if ((title == "" && description == "" && teacher == "")) {
        title = "⠀"
    }
    document.getElementsByTagName("td")["title"].innerHTML = htmlify(title);
    document.getElementsByTagName("td")["description"].innerHTML = htmlify(description);
    document.getElementsByTagName("td")["teacher"].innerHTML = htmlify(teacher);

    var tag_list = "";
    if (tags[0] == "") {

        tag_list = "<li><label><input class='tag_input' name='No Tags'  type='checkbox'/>No Tags</label></li>"
    } else {
        for (var i = 0; i < tags.length; i++) {
            tag_list += "<li><label><input class='tag_input' name='" + tags[i] + "' type='checkbox'/>" + tags[i] + "</label></li>";
        }
    }
    document.getElementsByTagName("ul")[1].innerHTML = tag_list;
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

function htmlify(string) {//convert raw text to html
    string = string.replaceAll("\n", "<br>")//new line fix

    parts = string.split("*"); //stars for bold
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

    //link formatting - replace www.youtube.com with <a href="www.youtube.com">www.youtube.com</a>
    //To be honest idk how it works
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = string.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText
}

function add_to_tags(tag) {
    tags = document.getElementsByName("tags")[0].value.split(", ");
    if (tags.includes(tag)) {//remove tag
        for (var i = 0; i < tags.length; i++) {
            if (tags[i] == tag) {
                tags.splice(i, 1);
            }
        }
    } else {//add tag
        tags.push(tag)
    }
    for (var i = 0; i < tags.length; i++) {//remove empty tags
        if (tags[i] == "") {
            tags.splice(i, 1);
        }
    }
    document.getElementsByName("tags")[0].value = tags.join(", ");
}