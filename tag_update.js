function update() {
    var tag_checkboxes = document.getElementsByClassName('tag_input');
    var allfalse = true;
    var shownTags = [];
    for (var i = 0; i < tag_checkboxes.length; i++) {//get all tags and check if none are checked
        if (tag_checkboxes[i].checked) {
            allfalse = false;
            shownTags.push(tag_checkboxes[i].name);
        }
    }
    if (allfalse) {//show all notices if all tags are false
        var notices = document.getElementsByTagName("tr");
        for (var i = 0; i < notices.length; i++) {
            notices[i].style.display = "table-row";
        }
    } else {//show notices with common tags that are selected
        var notices = document.getElementsByTagName("tr");
        for (var i = 1; i < notices.length; i++) {
            var notice_tags = notices[i].childNodes[0].childNodes[1].innerHTML.split(",");//get tags from notice(stored in <div> in title as "tag1,tag2,tag3")
            notices[i].style.display = "none";
            for (var j = 0; j < shownTags.length; j++) {

                if (notice_tags.includes(shownTags[j])) {
                    notices[i].style.display = "table-row";
                }
            }

        }
    }

}