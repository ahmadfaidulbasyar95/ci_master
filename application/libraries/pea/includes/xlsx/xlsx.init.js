
function UploadExel(s_input,header,sheet, callback_end, callback_start, callback_error) {
	if (callback_start) {
	  window[callback_start](s_input, header, sheet);
	}
	setTimeout(function() {
		header = header.split('|');

		//Reference the FileUpload element.
		var fileUpload = $(s_input)[0];

		//Validate whether File is valid Excel file.
		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
		if (regex.test(fileUpload.value.toLowerCase())) {
			if (typeof (FileReader) != "undefined") {
				var reader = new FileReader();
				//For Browsers other than IE.
				if (reader.readAsBinaryString) {
					reader.onload = function (e) {
						ProcessExcel(e.target.result, s_input ,header, sheet, callback_end);
					};
					reader.readAsBinaryString(fileUpload.files[0]);
				} else {
					//For IE Browser.
					reader.onload = function (e) {
						var data = "";
						var bytes = new Uint8Array(e.target.result);
						for (var i = 0; i < bytes.byteLength; i++) {
							data += String.fromCharCode(bytes[i]);
						}
						ProcessExcel(data, s_input ,header, sheet, callback_end);
					};
					reader.readAsArrayBuffer(fileUpload.files[0]);
				}
			} else {
				if (callback_error) {
				  window[callback_error](s_input, header, sheet, "This browser does not support HTML5.");
				}
			}
		} else {
			if (callback_error) {
			  window[callback_error](s_input, header, sheet, "Please upload a valid Excel file.");
			}
		}
	}, 200);
}
function ProcessExcel(data, s_input, header, sheet, callback_end) {
	var data_row = [];

	//Read the Excel File data.
	var workbook = XLSX.read(data, {
		type: 'binary'
	});

	//Fetch the name of First Sheet.
	var firstSheet = workbook.SheetNames[sheet];

	//Read all rows from First Sheet into an JSON array.
	var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);
	
	//Add the data rows from Excel file.
	for (var i = 0; i < excelRows.length; i++) {
		//Add the data cells.
		var row = [excelRows[i]['__rowNum__']];
		$.each(header, function(index, val) {
			var cell = excelRows[i][val];
			if(cell == undefined) cell = '';
		  row.push(cell);
		});
		data_row.push(row);
	}

	if (callback_end) {
	  window[callback_end](s_input, header, sheet, data_row);
	}
};