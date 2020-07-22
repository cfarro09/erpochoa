<!-- END FOOTER -->
<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script>
	function clearselect2(id) {
		getSelector(id).innerHTML = "";
		$(id).val(null).trigger('change');
	}
	const cargarselect2 = (id, arrayres, key, value, data = false, strselect = true) => {
		getSelector(id).innerHTML = ""
		arrayres.forEach(xx => {
			let datastr = "";
			if (data) {
				data.forEach(yy => {
					datastr += ` data-${yy}="${xx[yy]}"`
				});
			}
			getSelector(id).innerHTML += `<option ${datastr} value="${xx[key]}">${xx[value]}</option>`
		});
		$(id).select2();
	}

	const cargarselect2withobject = (id, arrayres, key, value, data = false, strselect = true) => {
		getSelector(id).innerHTML = ""
		arrayres.forEach(xx => {
			let datastr = "";
			if (data) {
				data.forEach(yy => {
					datastr += ` data-${yy.dataset}="${xx[yy.key]}"`
				});
			}
			getSelector(id).innerHTML += `<option ${datastr} value="${xx[key]}">${xx[value]}</option>`
		});
		$(id).select2();
	}

	const get_data_dynamic = async (query) => {
		var formData = new FormData();
		formData.append("query", query)
		const response = await fetch("get_data_dynamic2.php", {
			method: 'POST',
			body: formData,
		});
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				alert(e)
			}
		} else {
			alert("hubo un problema")
		}
	};
	const ff_dynamic = async (query) => {
		var formData = new FormData();
		formData.append("query", query)
		const response = await fetch("ff_dynamic.php", {
			method: 'POST',
			body: formData,
		});
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				alert(e)
			}
		} else {
			alert("hubo un problema")
		}
	};
	const ll_dynamic = async (data) => {
		const jjson = JSON.stringify(data).replace(/select/gi, "lieuiwuygyq").replace(/delete/gi, "dsjndasjdas")
		
		var formData = new FormData();
		formData.append("json", jjson);

		const response = await fetch("setVenta.php", {
			method: 'POST',
			body: formData,
		});
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				alert(e)
			}
		} else {
			alert("hubo un problema")
		}
	};
</script>
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>



<!-- END PAGELEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/pages/scripts/table-datatables-buttons.min.js" type="text/javascript"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="assets/layouts/layout4/scripts/layout.min.js" type="text/javascript"></script>
<script src="assets/layouts/layout4/scripts/demo.min.js" type="text/javascript"></script>
<script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->

<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/pages/scripts/components-typeahead.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->

<!-- END THEME LAYOUT SCRIPTS -->

<script src="assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.input-ip-address-control-1.0.min.js" type="text/javascript"></script>
<script src="assets/pages/scripts/form-input-mask.min.js" type="text/javascript"></script>

<script src="assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>



</body>

</html>