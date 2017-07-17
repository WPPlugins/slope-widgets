jQuery(document).ready(function($) {

  $("#arrival").datepicker({
    dateFormat: "dd/mm/yy",
    minDate: 0,
    onSelect: function (date) {
      var date2 = $('#arrival').datepicker('getDate');
      date2.setDate(date2.getDate() + 1);
      $('#departure').datepicker('setDate', date2);
      //sets minDate to dt1 date + 1
      $('#departure').datepicker('option', 'minDate', date2);
    }
  });
  $('#departure').datepicker({
    dateFormat: "dd/mm/yy",
    onClose: function () {
      var dt1 = $('#arrival').datepicker('getDate');
      var dt2 = $('#departure').datepicker('getDate');
      if (dt2 <= dt1) {
        var minDate = $('#departure').datepicker('option', 'minDate');
        $('#departure').datepicker('setDate', minDate);
      }
    }
  });
  $("#arrival").datepicker('setDate', '0');
  $("#departure").datepicker('setDate', '1');

  $.datepicker.regional['it'] = {
    closeText: 'Chiudi',
    currentText: 'Oggi',
    monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',   'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
    monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
    dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
    dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
    dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
    dateFormat: "dd/mm/yy"
  };

  $.datepicker.setDefaults($.datepicker.regional['it']);

  $(".datepicker").datepicker();

});
