$(document).ready(function() {
    $('#responsive-table').DataTable( {
      paging: false,
      lengthChange: false,
      searching: false,
      ordering: false,
      info: true,
      autoWidth: true,
	  responsive: false,
	  scrollY: false,
	  scrollX: true,
	  iDisplayLength: 10,
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -2 }
        ]
    } );
} );