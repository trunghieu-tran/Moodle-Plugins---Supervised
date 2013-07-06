function selectAll(objectSelectAll, checkboxName) {
    var elements = document.getElementsByName(checkboxName);//.getElementsByTagName('input');
    var checked = objectSelectAll.checked;
    var size = elements.length;
    for (i = 0; i < size; i++) {
        if (elements[i].type == "checkbox") {
            elements[i].checked = checked;
		}
    }
}
function selectItem(checkboxName) {
	var elementSelectAll = document.getElementById("select_all");
	if(!elementSelectAll) {
		return;
	}
	var elements = document.getElementsByName(checkboxName);
	var size = elements.length;
	var checked = true;
	for (i = 0; i < size; i++) {
		if (elements[i].type == "checkbox" && !elements[i].checked) {
			checked = false;
		}
	}
	elementSelectAll.checked = checked;
}
function startJSForTable(text) {
	var divSelectAll = document.getElementById("select_all");
	if(divSelectAll) {
		divSelectAll.outerHTML = "<input type = \"checkbox\" id = \"select_all\" onclick = \"selectAll(this,\'" + text + "\');\"/>";
	}
}