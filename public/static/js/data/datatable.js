yOSON.datable = {
    "tdAction4": {
        "aoColumns": [
            null, null, null, {"sClass": "center"}
        ]
    },
    "tdAction3": {
        "aoColumns": [
            null, null, {"sClass": "center"}
        ]
    },
    "#tblBanner": { 
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(1)', nRow).html('<a target="_blank" href="' + aData[1] + '">\n\
                <img width="160px" height="40px" src="' + aData[1] + '"/>\n\
            </a>');
            return nRow;
        }, "aoColumns": [
            null, {"sClass": "center"}, {"sClass": "center"},{"sClass": "center"},{"sClass": "center"}
        ]
    },
    "#tblJuego": {
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(1)', nRow).html('<a target="_blank" href="' + aData[1] + '">\n\
                <img width="50px" height="40px" src="' + aData[1] + '"/>\n\
            </a>');
            return nRow;
        }, "aoColumns": [
            null, {"sClass": "center"},{"sClass": "center"},{"sClass": "center"}
        ]
    },
    "#tblMusica": {
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(1)', nRow).html('<a target="_blank" href="' + aData[1] + '">\n\
                <img width="50px" height="40px" src="' + aData[1] + '"/>\n\
            </a>');
            return nRow;
        }, "aoColumns": [
            null, {"sClass": "center"},{"sClass": "center"},{"sClass": "center"}
        ]
    },
    "#tblServicio": {
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(1)', nRow).html('<a target="_blank" href="' + aData[1] + '">\n\
                <img width="50px" height="40px" src="' + aData[1] + '"/>\n\
            </a>');
            return nRow;
        }, "aoColumns": [
            null, {"sClass": "center"},{"sClass": "center"},{"sClass": "center"}
        ]
    },
};

