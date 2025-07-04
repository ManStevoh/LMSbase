
<!--<link href="{{asset('selects/select2.min.css')}}" rel="stylesheet" />-->

<!--<script src="{{asset('selects/select2.min.js')}} "></script>-->


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
  margin-top: 10px;
}
</style>

<script type="text/javascript">
$(document).ready(function() {

$("select").on("select2:open", function(event) {
    $('input.select2-search__field').attr('placeholder', 'Search Here...');
});


$("#duration").select2({
placeholder: "Select Duration",
allowClear: true,
width: '100%'
});
	
$("#users").select2({
placeholder: "Select Customer",
allowClear: true,
width: '100%'
});
 
 $("#bimafast_users").select2({
placeholder: "Select Customer",
allowClear: true,
width: '100%'
});

$("#bimafast_roles").select2({
placeholder: "Select Role",
 maximumSelectionLength: 1,
allowClear: true,
width: '100%'
});

$("#bimafast_countries").select2({
placeholder: "Select Country",
allowClear: true,
width: '100%'
});


$("#bimafast_towns").select2({
placeholder: "Select Town",
allowClear: true,
width: '100%'
});


$("#vehicle_makes").select2({
placeholder: "Select Vehicle Make",
allowClear: true,
width: '100%'
});


$("#bimafast_drivers").select2({
placeholder: "Select Driver",
allowClear: true,
width: '100%'
});


$("#bimafast_vehicles").select2({
placeholder: "Select Vehicle",
allowClear: true,
width: '100%'
});

$("#bimafast_driver_towns").select2({
dropdownParent: $('#addNewDriver'),
placeholder: "Select Driver Town",
allowClear: true,
width: '100%'
});

$("#bimafast_driver_genders").select2({
dropdownParent: $('#addNewDriver'),
placeholder: "Select Driver Gender",
allowClear: true,
width: '100%'
});


$("#bimafast_driver_customers").select2({
dropdownParent: $('#addNewDriver'),
placeholder: "Select Customer",
allowClear: true,
width: '100%'
});



$("#bimafast_vehicle_makes").select2({
dropdownParent: $('#addNewVehicle'),
placeholder: "Select Vehicle Make",
allowClear: true,
width: '100%'
});


$("#bimafast_vehicle_body_types").select2({
dropdownParent: $('#addNewVehicle'),
placeholder: "Select Vehicle Body Type",
allowClear: true,
width: '100%'
});


$("#bimafast_vehicle_years").select2({
dropdownParent: $('#addNewVehicle'),
placeholder: "Select Vehicle Year",
allowClear: true,
width: '100%'
});




function iconFromValue(val){
    if(val.element){
        val = `<span class="select2-option-img"><i class='fa fa-map-marker'></i><span> ${val.text}`;
    }else{
    	     val = `<span class="select2-option-img"><i class='fa fa-map-marker'></i><span> Select Location`;
    }
    return val;
}


$("#search_locations").select2({
placeholder: ' Location',
allowClear: true,
width: '100%',
        templateResult: iconFromValue,
        templateSelection: iconFromValue,
        escapeMarkup: function(m) { return m; }
});

$("#bimafast_locations").select2({
placeholder: ' Location',
allowClear: true,
width: '100%',
        templateResult: iconFromValue,
        templateSelection: iconFromValue,
        escapeMarkup: function(m) { return m; }
});

function iconCounty(val){
    if(val.element){
        val = `<span class="select2-option-img"><i class='fa fa-map-marker-alt'></i><span> ${val.text}`;
    }else{
    	     val = `<span class="select2-option-img"><i class='fa fa-map-marker-alt'></i><span> Select County`;
    }
    return val;
}


function iconCountyy(val){
    if(val.element){
        val = `<span class="select2-option-img"><i class='fa fa-mountain-city'></i><span> ${val.text}`;
    }else{
    	     val = `<span class="select2-option-img"><i class='fa fa-mountain-city'></i><span> Select County`;
    }
    return val;
}

$("#bimafast_counties").select2({
placeholder: ' County',
allowClear: true,
width: '100%',
        templateResult: iconCounty,
        templateSelection: iconCounty,
        escapeMarkup: function(m) { return m; }
});

 


$("#counties").select2({
placeholder: "Select County",
allowClear: true,
width: '100%'
});
 
 

$("#status").select2({
placeholder: "Select Status",
allowClear: true,
width: '100%'
});
 

$("#update_status").select2({
placeholder: "Select Status",
allowClear: true,
width: '100%'
});

$("#bimafast_gender").select2({
placeholder: "Select Gender",
allowClear: true,
width: '100%'
});
 

});
</script>