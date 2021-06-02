function update() {
    var inputs = document.getElementsByClassName('tag_input');
    var allfalse = true;
    var shownTags = [];
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].checked) {
            allfalse = false;
            shownTags.push(inputs[i].name);
        }
    }
    if (allfalse) {
        var notices = document.getElementsByTagName("tr");
        for (var i = 0; i < notices.length; i++) {
            notices[i].style = "display:table-row"
        }
    } else {
        var notices = document.getElementsByTagName("tr");
        for (var i = 1; i < notices.length; i++) {
            var notice_tags = notices[i].childNodes[0].childNodes[1].innerHTML.split(",");
            notices[i].style = "display:none";
            for (var j = 0; j < shownTags.length; j++) {
                if (notice_tags.includes(shownTags[j])) {
                    notices[i].style = "display:table-row;";
                }
            }

        }
    }

}