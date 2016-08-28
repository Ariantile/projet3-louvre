$(function () {
	$('a[href="#ancreFormAcc"]').on('click', function (e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: $($(this).attr('href')).offset().top}, 500, 'linear');
	});
    
    var $dateConnexion = $('#dateConnexion').text(),
        $pickerReserv = $.datepicker.parseDate("dd/mm/yy", $dateConnexion),
        $heureConnexion = parseInt($('#heureConnexion').text());
    
    $('.dateReserv').css('background-color', '#FFF').attr('readOnly', 'true').datepicker({
        defaultDate: $pickerReserv,
        dateFormat: 'dd/mm/yy',
        changeYear: true,
        changeMonth: true,
        yearRange: ':+10',
        minDate: $pickerReserv,
        beforeShowDay: function (date) {
            if ((date.getDay() === 2)
                    || ((date.getDate() === 1)  && (date.getMonth() === 4))
                    || ((date.getDate() === 1)  && (date.getMonth() === 10))
                    || ((date.getDate() === 25) && (date.getMonth() === 11))
                    ) {
                return [false, ''];
            } else {
                return [true, ''];
            }
        },
        onSelect: function (date) {
            
            $('#dateVisite').text('dada');
            
            if (($heureConnexion >= 14) && ($dateConnexion === $('.dateReserv').val())) {
                $('#commande_demiJournee_0').attr('checked', false).prop("disabled", true);
                $('#commande_demiJournee_1').attr('checked', true);
            } else {
                $('#commande_demiJournee_1').attr('checked', false);
                $('#commande_demiJournee_0').prop("disabled", false);
            }
        }
    });
    
    $('.date').css('background-color', '#FFF').datepicker({
        changeYear: true,
        changeMonth: true,
        yearRange: "-130:+0"
    });
    
    $('#bloc_billet').hide();
    $('#bloc_facturation').hide();
    
    $(document).ready(function () {

        var $container = $('div#commande_billets'),
            index = $container.find(':input').length;

        $('#addBillet').click(function (e) {
            
            if (index >= 0 && index < 9) {
                addBillet($container);
                $('#bloc_billet').show();
                $('#bloc_facturation').show();
                $('.date').css('background-color', '#FFF');
            }
            e.preventDefault();
            return false;
        });
        
        $('#supBillet').click(function (e) {
            
            if (index > 0 && index < 10) {
                suppBillet($container);
                if (index === 0) {
                    $('#bloc_billet').hide();
                    $('#bloc_facturation').hide();
                }
            }
            e.preventDefault();
            return false;
        });
    
        function addBillet($container) {
            
            var template = $container.attr('data-prototype')
                    .replace(/__name__label__/g, 'Billet n°' + (index + 1))
                    .replace(/__name__/g,        index),
                $prototype = $(template).addClass('blocUnBillet well');

            $container.append($prototype);
            
            index++;
            $('#commande_qte').val(index);
            $('.date').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: "-130:+0"
            });
        }
        
        function suppBillet() {
            
            $('div#commande_billets > div.form-group:last-of-type').remove();
            index--;
            $('#commande_qte').val(index);
        }
    });
});