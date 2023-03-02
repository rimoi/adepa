// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('.dataTable').DataTable( {
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.2/i18n/fr-FR.json'
      }
  });
});
