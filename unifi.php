<html>
<head>
<meta content="text/html; charset=ISO-8859-1"
http-equiv="content-type">
<title></title>
</head>
<body>

	<input type="button" id="reload" value="Reload" onClick="location.href=location.href">
	<br></br>

    <table id="dnsTable" name="dnsTable" style="text-align: left;" border="0" cellpadding="2" cellspacing="0">
        <tbody id="dnsTableBody">
			<tr>
				<td style="vertical-align: top;">Hostname</td>
				<td style="vertical-align: top;">Alias</td>
				<td style="vertical-align: top;">IP Address</td>
				<td style="vertical-align: top;"></td> 
			</tr>
<?php

    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
      @@  This program is used for the online updating of hostname
      @@    (lightweight dns) entries for the unifi router
      @@  This program is expected to run on apache2 using php mod
      @@  
      @@  URL: http://www.url.com/index.php
      @@  
      @@  Query String Options
      @@    result = The last result of the attempted save
      @@    
      @@  This code is protected under copyright law.  The use of  
      @@    this code for purposes other than those expressly 
      @@    provided by the developer are prohibited.
      @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/

	//Get the file, open it, and load it to an array
	$json_string = file_get_contents("/var/www/config.gateway.json");
	$parsed_json = json_decode($json_string, true);

	//get the hostname values and children loaded into its own array
	$main_values=$parsed_json["system"]["static-host-mapping"]["host-name"];

	//get the list of hostnames only in an array
	$hostnames=array_keys($main_values);

	//loop through each host name
	$count = count($hostnames);
	for ($i = 0; $i < $count; $i++) {
		
		//build a table with fields for the host
		$r = $i + 1;
		//create row
		print "<tr id=\"tr" . $r . "\">";
		//create first cell and put hostname field and row count field
		print "<td style=\"vertical-align: top;\"><input value=\"" . $hostnames[$i] . "\" size=\"100\" name=\"host" . $r . "\"id=\"host" . $r ."\"><br>";
		print "<input name=\"row" . $r . "\" id=\"row" . $r . "\" value=\"" . $r . "\" style=\"display: none;\">";
		print "</td>";
		//create second cell and put alias field
		print "<td style=\"vertical-align: top;\"><input value=\"" . $main_values[$hostnames[$i]]["alias"][0] . "\" name=\"alias" . $r . "\" size=\"30\" id=\"alias" . $r . "\"><br>";
		print "</td>";
		//create third cell and put in IP address field
		print "<td style=\"vertical-align: top;\"><input value=\"" . $main_values[$hostnames[$i]]["inet"][0] . "\" name=\"ip" . $r . "\" size=\"15\" id=\"ip" . $r . "\"><br>";
		print "</td>";
		//create fourth cell and put in delete/add buttons
		print "<td style=\"vertical-align: top;\">";
		print "<button title=\"Add Row\" value=\"add\" name=\"add" . $r . "\" id=\"add" . $r . "\" type=\"button\" onclick=\"addRow(" . $r . ")\">+</button>";
		print "<button title=\"Delete Row\" value=\"del\" name=\"del" . $r . "\" id=\"del" . $r . "\" type=\"button\" onclick=\"delRow(" . $r . ")\">-</button>";
		print "</td>";
		print "</tr>\n\r";

		$lasti = $r;
	}
	//end the table
    echo "</tbody>";
    echo "</table>";

	//add hidden fields to show count of rows and the maximum ID value held
	print "<input name=\"count\" id=\"count\" value=\"" . $lasti . "\"style=\"display: none;\">";
	print "<input name=\"maxids\" id=\"maxids\" value=\"" . $lasti . "\"style=\"display: none;\">";
	
	
?>
	<br></br>
	
	<!--  Create button to add a row to the bottom -->
	<input type="button" id="addRowToEnd" value="Add Row" onClick="addRow(0)">
	<br></br>
	<!-- create a for to push transformed JSON and file information to the save program -->
	<form method="post" onsubmit="return createJSON();" action="test.php" id="updateDNS">
		<input id="output" name="output" style="display: none;">
		<input id="dnsfile" name="dnsfile" style="display: none;" value="/var/www/pathfinder/config.gateway.json">
		<input type="button" id="viewjson" value="View JSON" onClick="createHtmlJSON()">
		<input type="submit" value="Save">
		<br>
	</form>
	
	<!-- paragraph to hold JSON output -->
	<p id="htmljson"></p>
	

<script>

	function addRow(ro) {
		//identify next row number and id number for object id's
		var rownum = parseInt(document.getElementById('count').value) + 1;
		var idnum = parseInt(document.getElementById('maxids').value) + 1;
		
		// define table object
		var oTable = document.getElementById("dnsTable");
		if (oTable && document.createElement) {
			//define a new row and give it an ID
			var oRow = document.createElement('TR');
			oRow.setAttribute("id", "tr" + idnum);
			
			//define first cell for the row
			var oCell0 = document.createElement('TD');
			oCell0.style.verticalAlign = "top";
			//create hostname field for cell
			var oHost = document.createElement("INPUT");
			oHost.setAttribute("id", "host" + idnum);
			oHost.setAttribute("size", 100);
			//add host field to cell
			oCell0.appendChild(oHost);
			//create row count field for cell
			var oRowNum = document.createElement("INPUT");
			oRowNum.setAttribute("id", "row" + idnum);
			oRowNum.style.display = "none"; 
			oRowNum.value = rownum;
			//add row field to cell
			oCell0.appendChild(oRowNum);
			//add cell to row
			oRow.appendChild(oCell0);
			
			
			//define second cell for the row
			var oCell1 = document.createElement('TD');
			oCell1.style.verticalAlign = "top";
			//create alias field for cell
			var oAlias = document.createElement("INPUT");
			oAlias.setAttribute("id", "alias" + idnum);
			oAlias.setAttribute("size", 30);
			//add field to cell
			oCell1.appendChild(oAlias);
			//add cell to row
			oRow.appendChild(oCell1);

			//define third cell for the row
			var oCell2 = document.createElement('TD');
			oCell2.style.verticalAlign = "top";
			//create ip field for cell
			var oIP = document.createElement("INPUT");
			oIP.setAttribute("id", "ip" + idnum);
			oIP.setAttribute("size", 15);
			//add field to cell
			oCell2.appendChild(oIP);
			//add cell to row
			oRow.appendChild(oCell2);
			
			//define fourth cell for the row
			var oCell3 = document.createElement('TD');
			oCell3.style.verticalAlign = "top";
			//create add row button
			var oAddBTN = document.createElement('BUTTON');
			var oAddBTNTxt = document.createTextNode("+");
			oAddBTN.setAttribute("id", "add" + idnum);
			oAddBTN.setAttribute("onclick", "addRow(" + idnum +")");
			oAddBTN.setAttribute("title", "Add Row");
			//add text to the button
			oAddBTN.appendChild(oAddBTNTxt);
			//add button to the cell
			oCell3.appendChild(oAddBTN);
			//create delete row button			
			var oDelBTN = document.createElement('BUTTON');
			var oDelBTNTxt = document.createTextNode("-");
			oDelBTN.setAttribute("id", "del" + idnum);
			oDelBTN.setAttribute("onclick", "delRow(" + idnum +")");
			oDelBTN.setAttribute("title", "Delete Row");
			//add text to the button
			oDelBTN.appendChild(oDelBTNTxt);
			//add button to the cell
			oCell3.appendChild(oDelBTN);
			//add cell to row
			oRow.appendChild(oCell3);
			
			//if the row being added is to the middle of the table, execute the add in that place
			if (ro > 0) {
				var target = document.getElementById("tr" + ro);
				target.parentNode.insertBefore(oRow, target.nextSibling);
			} else {
				//if the add is to the end, simple addition
				oTable.appendChild(oRow);
			}

			//renumber all of the row count fields and set the ending count of rows and highest id number
			rownum = renumberRows();
			document.getElementById('count').value = rownum;
			document.getElementById('maxids').value = idnum;

		}
		
	}
	
	function delRow(i) {
		var maxrows = document.getElementById('count').value;
		var maxids = document.getElementById('maxids').value;
		var rowdel = document.getElementById('row' + i).value;
		var text = "";
		var rowcount = 0;
		
		//confirm if the row should be deleted
		var conf = confirm("Delete " + document.getElementById('host' + i).value + "?");
		if (conf == true) {
			//delete the row, renumber the rows, and update the total row count
			document.getElementById("dnsTable").deleteRow(rowdel);
			
			rowcount = renumberRows();

			document.getElementById('count').value = rowcount;
		
		}
	}
	
	
	function renumberRows() {
		//define the table body and set of rows (array like object)
		var oTable = document.getElementById("dnsTableBody");
		var oTableRows = oTable.children;
		
		//loop through the rows
		for (r = 1; r < oTableRows.length; r++) {
			var oRowCells = oTableRows[r].children;
			//loop through the cells in the row
			for (c=0; c < oRowCells.length; c++) {
				var oCellFields = oRowCells[c].children;
				//for each cell, check if it has an id, and if so, check if it is a row count field
				for (f=0; f<oCellFields.length; f++) {
					if (oCellFields[f].getAttribute("id")!=null){
							//if it's a row count field, update it with current row number as calculated by for loop at top
							if (oCellFields[f].getAttribute("id").substring(0,3)=="row") {
							oCellFields[f].value = r;
						}
					}
				}
			}
		}
		//update the total row count field
		return document.getElementById("dnsTableBody").children.length-1;
	}

	function createJSON() {
		//find out what's the max id used
		var maxids = document.getElementById('maxids').value;
		
		//set the static values
		//   NOTE:  ~ represents the need for a line feed - this is updated on the submitting page and the createHtmlJSON function
		var out_start = '{~        "system": {~               "static-host-mapping": {~                        "host-name": {~                                "';
		var out_end = '~                        }~                }~        }~}';
		var out_post_host = '": {~                                        "alias": ["';
		var out_post_alias = '"],~                                        "inet": ["';
		var out_post_ip = '"]~                                },~                                "';
		
		//start loading the json string
		var out_json = out_start;
		
		//loop through all possible field IDs
		for (i = 1; i <= maxids; i++) { 
			//if the field exists, then and it is not empty (all fields must be completed in each row
			//    then add in the host, alias, and IP information
			if (document.getElementById('row' + i) != null) {
				if (document.getElementById('host' + i).value.replace(/\s/g,"") != "" && document.getElementById('alias' + i).value.replace(/\s/g,"") != "" && document.getElementById('ip' + i).value.replace(/\s/g,"") != "") {
					out_json = out_json + document.getElementById('host' + i).value + out_post_host;
					out_json = out_json + document.getElementById('alias' + i).value + out_post_alias;
					out_json = out_json + document.getElementById('ip' + i).value + out_post_ip;
				}
			}
		}
		//delete out the excess comma and quote at the end
		out_json = out_json.substring(0,out_json.length - 35);
		//add the ending string
		out_json += out_end;
		
		//set the output field to the new JSON to be sent to the posting page for file update
		document.getElementById('output').value = out_json;
	}
	
	//create the json, then transform it to show HTML; load it to the htmljson paragraph
	function createHtmlJSON() {
		createJSON();
		var html_json = document.getElementById('output').value.replace(/ /g,"&nbsp");
		html_json = html_json.replace(/~/g,"<br>");
		document.getElementById('htmljson').innerHTML = html_json;		
	}
	
</script>
	
<?php
    
    if (isset($_GET['result']))
    {
        $result = $_GET['result'];
        switch ($result) {
            case 'success':
                echo "<script type='text/javascript'>alert('Updated Successfully')</script>";
                break;
            case 'unwriteable':
                echo "<script type='text/javascript'>alert('File cannot be updated')</script>";
                break;
            case 'notopen':
                echo "<script type='text/javascript'>alert('File cannot be opened')</script>";
                break;
            case 'writefailed':
                echo "<script type='text/javascript'>alert('A write to the file failed')</script>";
                break;
            case 'logfail':
                echo "<script type='text/javascript'>alert('Failed to update security log')</script>";
        }
    }

?>
</body>
</html>
