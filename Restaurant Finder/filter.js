$("#tableFilter").keyup(function() {
    var data = this.value.toUpperCase().split(" ");
    data = data.filter(Boolean);
    var body = $("#rBody").find("tr");
    if(this.value == "") {
        body.show();
        return;
    }
    body.hide();

    body.filter(function(e) {
        var row = $(this);
        for(var j = 0; j < data.length; j++) {
            if(row.text().toUpperCase().indexOf(data[j]) > -1) {
                return true;
            }
        }
        return false;
    }).show();
});