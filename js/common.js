var ft_timer = null;
var ft_finished = true;
var accent_map = {
  'À':'A', 'È':'E', 'Ì':'I', 'Ò':'O', 'Ù':'U', 'Á':'A', 'É':'E', 'Í':'I','Ó':'O','Ú':'U',
  'à':'a', 'è':'e', 'ì':'i', 'ò':'o', 'ù':'u', 'á':'a', 'é':'e', 'í':'i','ó':'o','ú':'u'
};

function accent_fold (s) {
  if (!s) { return ''; }
  var ret = '';
  for (var i = 0; i < s.length; i++) {
    ret += accent_map[s.charAt(i)] || s.charAt(i);
  }
  return ret;
};

function filterTableDelay(tableName, ms){
	if(ft_timer != null) clearTimeout(ft_timer);
	var callback = function(){filterTable(tableName); };
	ft_timer = setTimeout(function (){
		if(ft_finished){callback();}
		else{ft_timer = setTimeout(callback, 200);}
		}, ms);
}

function filterTableInput(evt){
	var tableid = evt.target.parentNode.getAttribute("data-table-id");
	filterTableDelay(tableid, 200);
}

function filterTable(tableName){
	ft_finished = false;
	var i;
	var j;
	var filters = [];
	var inputs = [];
	var filter_form = document.getElementById(tableName + "_filter");
	inputs.push(filter_form.getElementsByTagName("input"));
	inputs.push(filter_form.getElementsByTagName("select"));
	
	for(i = 0; i < inputs.length; i++){
		for(j = 0; j < inputs[i].length; j++){
			filters.push([inputs[i][j].getAttribute("data-filter-id"), accent_fold(inputs[i][j].value.toLowerCase())]);
		}
	}
	
	var table = document.getElementById(tableName);
	var table_new = document.createDocumentFragment();
	var copy = table.cloneNode(true);
	copy.id = tableName;
	table_new.appendChild(copy);
	
	var tr = copy.getElementsByTagName("tr");
	
	for(i = 0; i < tr.length; i++){
		var show = true;
		
		for(j = 0; j < filters.length; j++){
			var td = tr[i].getElementsByTagName("td")[filters[j][0]];
			
			if(td){
				if(accent_fold(td.innerText.toLowerCase()).indexOf(filters[j][1]) == -1){
					show = false;
				}
			}
		}
		
		if(show){
			tr[i].style.display = "";
		}else{
			tr[i].style.display = "none";
		}
	}
	
	table.parentNode.replaceChild(table_new, table);
	sorttable.init();
	ft_finished = true;
}

function toggleShowTable(evt){
	var id = evt.target.getAttribute("data-table-id");
	var hideBtn = evt.target;
	var table = document.getElementById(id);
	table.style.display = table.style.display == "none" ? "" : "none";
	hideBtn.innerHTML = hideBtn.innerHTML == "[-]" ? "[+]" : "[-]";
	evt.preventDefault();
}

var txtboxid = 0;
var af_form = null;
var f_txtbox0 = null;

function new_textbox(evt){
	var n = evt.target.getAttribute("data-n");
	
	if(txtboxid == n){
		txtboxid++;
		if(n > 0) af_form.appendChild(document.createElement("br"));
		newtxt = af_form.appendChild(f_txtbox0.cloneNode(true));
		newtxt.name = "nom_membre_afegir[" + txtboxid + "]";
		newtxt.id = "txtbox" + txtboxid;
		newtxt.setAttribute("data-n", txtboxid);
		newtxt.value = "";
		newtxt.addEventListener("input", new_textbox, false);
	}
}

function invert_sel(evt){
	var target = evt.target.getAttribute("data-target");
	var cb = document.getElementsByName(target);

	for(var i = 0, n = cb.length; i < n; i++) {
		cb[i].checked = !cb[i].checked;
	}
	
	evt.preventDefault();
}

function execRTECmd(evt){
	document.execCommand("styleWithCSS", false, false);
	
	var cmd = evt.target.getAttribute("data-cmd");
	
	if(evt.target.selectedIndex){
		document.execCommand(cmd, false, this.value);
		evt.target.selectedIndex = 0;
	}else{
		document.execCommand(cmd, false, null);
	}
}

function createRTEButton(type, cmd, htmlc){
	var btn = document.createElement(type);
	btn.setAttribute("data-cmd", cmd);
	btn.innerHTML = htmlc;
	btn.className = "rte-btn";
	
	if(type == "button"){
		btn.setAttribute("type", "button");
		btn.addEventListener("click", execRTECmd, false);
	}
	
	if(type == "select"){
		btn.addEventListener("input", execRTECmd, false);
	}
	
	return btn;
}

function insertRTE(ta){
	var df = document.createDocumentFragment();
	
	if(!ta.readOnly && !ta.disabled){
		df.appendChild(createRTEButton("button", "removeFormat", "✨"));
		
		df.appendChild(createRTEButton("select", "heading", '<option value="">Heading</option><option value="H1">H1</option><option value="H2">H2</option><option value="H3">H3</option><option value="H4">H4</option><option value="H5">H5</option><option value="H6">H6</option>'));
		
		df.appendChild(createRTEButton("button", "bold", "<strong>B</strong>"));
		df.appendChild(createRTEButton("button", "italic", "<i>I</i>"));
		df.appendChild(createRTEButton("button", "underline", "<u>U</u>"));
		df.appendChild(createRTEButton("button", "strikeThrough", "<s>S</s>"));
		df.appendChild(createRTEButton("button", "superscript", "a<sup>b</sup>"));
		df.appendChild(createRTEButton("button", "subscript", "a<sub>b</sub>"));
		df.appendChild(createRTEButton("button", "outdent", "⇤"));
		df.appendChild(createRTEButton("button", "indent", "⇥"));
		df.appendChild(createRTEButton("button", "insertUnorderedList", "•"));
		df.appendChild(createRTEButton("button", "insertOrderedList", "1."));
		
		df.appendChild(createRTEButton("select", "fontName", '<option value="">Font</option><option value="Arial">Arial</option><option value="Times New Roman">Times</option><option value="Courier New">Courier</option><option value="Palatino">Palatino</option><option value="Garamond">Garamond</option><option value="Bookman">Bookman</option><option value="Avant Garde">Avant Garde</option><option value="Verdana">Verdana</option><option value="Georgia">Georgia</option>'));
		df.appendChild(createRTEButton("select", "fontSize", '<option value="">Mida text</option><option value="1">Molt petit</option><option value="2">Petit</option><option value="3">Mitja</option><option value="4">Mitja-gran</option><option value="5">Gran</option><option value="6">Molt gran</option><option value="7">Mes gran</option>'));
		df.appendChild(createRTEButton("select", "foreColor", '<option value="">Color text</option><option class="rte_black" value="#000000">Negre</option><option class="rte_red" value="#e6194b">Vermell</option><option class="rte_green" value="#3cb44b">Verd</option><option class="rte_yellow" value="#ffe119">Groc</option><option class="rte_blue" value="#0082c8">Blau</option><option class="rte_orange" value="#f58231">Taronja</option><option class="rte_purple" value="#911eb4">Porpra</option><option class="rte_cyan" value="#46f0f0">Cian</option>'));
	}
	
	var editDiv = document.createElement("div");
	editDiv.id = "_rte" + ta.id;
	editDiv.className = "rt_edit";
	if(!ta.readOnly && !ta.disabled) editDiv.setAttribute("contenteditable", "true");
	df.appendChild(editDiv);
	editDiv.innerHTML = ta.value;
	ta.parentNode.insertBefore(df, ta);
	ta.style.display = "none";
	ta.setAttribute("data-rte-id", editDiv.id);
}

function submitRTE(evt){
	var ta = evt.target.getElementsByTagName("textarea");
		
	for(i = 0; i < ta.length; i++){
		if(ta[i].getAttribute("data-type") == "rte"){
			ta[i].value = document.getElementById(ta[i].getAttribute("data-rte-id")).innerHTML;
		}
	}
}

function onReady(){
	// afegir botons de amagar taula
	var tables = document.getElementsByTagName("table");
	
	for(i = 0; i < tables.length; i++){
		if(tables[i].id == "") tables[i].id = "_tables_auto_" + i;
		
		if(!(tables[i].getAttribute("data-no-minimize") != null && tables[i].getAttribute("data-no-minimize") == "no-minimize")){
			var hideBtn = document.createElement("a");
			hideBtn.href = "#a";
			hideBtn.innerHTML = "[-]";
			hideBtn.id = "hideBtn" + i;
			hideBtn.setAttribute("data-table-id", tables[i].id);
			hideBtn.addEventListener("click", toggleShowTable, false);
			tables[i].parentNode.insertBefore(hideBtn, tables[i]);
		}
	}
	
	// afegir multiple textboxs dinamicament (afegir membres grup etc.)
	
	if(document.getElementById("afegir_form")) af_form = document.getElementById("afegir_form");
	if(document.getElementById("txtbox0")){
		f_txtbox0 = document.getElementById("txtbox0");
		f_txtbox0.addEventListener("input", new_textbox, false);
		f_txtbox0.setAttribute("data-n", 0);
	}
	
	// botons de invertir selecció
	
	var invert_sel_btns = document.getElementsByName("invert_sel_btn");
	
	for(i = 0; i < invert_sel_btns.length; i++){
		invert_sel_btns[i].addEventListener("click", invert_sel);
	}
	
	// filtrar taules
	
	var filter_table_forms = document.getElementsByName("table_filter");
	
	for(i = 0; i < filter_table_forms.length; i++){
		filter_table_forms[i].addEventListener("input", filterTableInput);
	}
	
	// formularis
	
	var _forms = document.getElementsByTagName("form");
	
	for(i = 0 ; i < _forms.length; i++){
		if(_forms[i].id == ""){
			_forms[i].id = "_form_auto_" + i;
		}
				
		var ta = _forms[i].getElementsByTagName("textarea");
		var containsRTEEditable = false;
		
		// textarea
		for(j = 0; j < ta.length; j++){
			if(ta[j].getAttribute("data-type") == "rte"){ // insertar edición enriquecida
				if(ta[j].id == ""){
					ta[j].id = "_textarea_auto_" + _forms[i].id + "_" + j;
				}
				
				insertRTE(ta[j]);
				
				if(!ta[j].readOnly && !ta[j].disabled){
					containsRTEEditable = true;
				}
			}
		}
		
		if(containsRTEEditable){
			_forms[i].addEventListener("submit", submitRTE, false);
		}
	}
}

window.addEventListener("load", onReady);