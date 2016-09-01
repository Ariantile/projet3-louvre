$(function () {
    
    /**********************************************************************************************
    *********************** ANIMATION FLECHE ******************************************************
    **********************************************************************************************/
    
	$('a[href="#ancreFormAcc"]').on('click', function (e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: $($(this).attr('href')).offset().top}, 500, 'linear');
	});
    
    /**********************************************************************************************
    *********************** PARAMETRES DE DEPART **************************************************
    **********************************************************************************************/
    
    $('#bloc_billet').hide();
    $('#bloc_facturation').hide();
    $('#addBillet').prop('disabled', true);
    $('#totalPanier').val(0);
    
    /**********************************************************************************************
    *********************** DATE ET HEURE SERVEUR *************************************************
    **********************************************************************************************/
    
    var $dateConnexion = $('#dateConnexion').text(),
        $pickerReserv = $.datepicker.parseDate("dd/mm/yy", $dateConnexion),
        $heureConnexion = parseInt($('#heureConnexion').text()),
        $tabTarif = [];
    
    /**********************************************************************************************
    *********************** DATEPICKER DATE RESERVATION *******************************************
    **********************************************************************************************/
    
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
            
            $('#dateVisite').text(date);
            
            if (($heureConnexion >= 18) && ($dateConnexion === $('.dateReserv').val())) {
                $('#commande_demiJournee_0').attr('checked', false).prop('disabled', true);
                $('#commande_demiJournee_1').attr('checked', false).prop('disabled', true);
                $('#addBillet').prop('disabled', true);
                $('#subForm').prop('disabled', true);
                $('#dateVisite').text('Heure de visite dépassée pour cette date.').css('color', 'red');
            } else if (($heureConnexion >= 14) && ($dateConnexion === $('.dateReserv').val())) {
                $('#commande_demiJournee_0').attr('checked', false).prop('disabled', true);
                $('#commande_demiJournee_1').attr('checked', true);
                $('#typeBillet').text('Demi-journée');
                $('#addBillet').prop('disabled', false);
                $('#subForm').prop('disabled', false);
                $('#dateVisite').css('color', 'black');
            } else {
                $('#commande_demiJournee_0').attr('checked', false).prop('disabled', false);
                $('#commande_demiJournee_1').attr('checked', false).prop('disabled', false);
                $('#typeBillet').text('Aucun type de billet selectionné.');
                $('#addBillet').prop('disabled', false);
                $('#subForm').prop('disabled', false);
                $('#dateVisite').css('color', 'black');
            }
        }
    });
    
    /**********************************************************************************************
    *********************** CHOIX JOURNEE OU DEMI JOURNNE *****************************************
    **********************************************************************************************/
    
    $('#commande_demiJournee').change(function () {
        if ($('#commande_demiJournee_0').is(':checked')) {
            $('#typeBillet').text('Journée');
        } else if ($('#commande_demiJournee_1').is(':checked')) {
            $('#typeBillet').text('Demi-journée');
        } else if ($('#commande_demiJournee_0').is(':not:checked') && $('#commande_demiJournee_1').is(':not:checked')) {
            $('#typeBillet').text('Aucun type de billet selectionné.');
        }
    });
    
    /**********************************************************************************************
    *********************** DECOMPTE DU NOMBRE DE VISITEUR/BILLET *********************************
    **********************************************************************************************/
    
    $('#commande_qte').change(function () {
        $('#nbPersonne').text($('#commande_qte').val());
    });
        
    $(document).ready(function () {

        var $container = $('div#commande_billets'),
            index = $container.find(':input').length;

        /**********************************************************************************************
        *********************** AJOUT D'UN BILLET DANS LE DOM *****************************************
        **********************************************************************************************/
        
        $('#addBillet').click(function (e) {
            
            if (index >= 0 && index < 9) {
                addBillet($container);
                $('#bloc_billet').show();
                $('#bloc_facturation').show();
                $('.date').css('background-color', '#FFF');
                $('#nbPersonne').text($('#commande_qte').val());
            }
            e.preventDefault();
            return false;
        });
        
        /**********************************************************************************************
        *********************** SUPPRESSION D'UN BILLET DANS LE DOM ***********************************
        **********************************************************************************************/
        
        $('#supBillet').click(function (e) {
            
            if (index > 0 && index < 10) {
                suppBillet($container);
                $('#nbPersonne').text($('#commande_qte').val());
                
                if (index === 0) {
                    $('#bloc_billet').hide();
                    $('#bloc_facturation').hide();
                    $('#nbPersonne').text('Aucun billet ajouté au panier.');
                }
            }
            e.preventDefault();
            return false;
        });
    
        /**********************************************************************************************
        *********************** FONCTION D'AJOUT DE BILLET ********************************************
        **********************************************************************************************/
        
        function addBillet($container) {
            
            var template = $container.attr('data-prototype')
                    .replace(/__name__label__/g, 'Billet n°' + (index + 1))
                    .replace(/__name__/g,        index),
                $prototype = $(template).addClass('blocUnBillet well');

            $container.append($prototype);
            index++;
            $('#commande_qte').val(index);
            
            $('.date').datepicker({
                defaultDate: $pickerReserv,
                dateFormat: 'dd/mm/yy',
                changeYear: true,
                changeMonth: true,
                yearRange: '-130:',
                maxDate: $pickerReserv,
                onSelect: function (date) {
                    
                    var $dateVisite = $('.dateReserv').val().split('/'),
                        $dateVisiteCalcule = new Date($dateVisite[2], $dateVisite[1] - 1, $dateVisite[0]),
                        $dateNaissance = date.split('/'),
                        $dateNaissanceCalcule = new Date($dateNaissance[2], $dateNaissance[1] - 1, $dateNaissance[0]),
                        $anneeDiff = $dateVisite[2] - $dateNaissance[2],
                        $age,
                        $tarif,
                        $tarifDesc;
                        
                    /**********************************************************************************************
                    *********************** TEST DATE DE NAISSANCE/AGE ********************************************
                    **********************************************************************************************/
                    
                    if (($dateNaissance[1] < $dateVisite[1]) ||
                       (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] < $dateVisite[0]))) {
                        $age = $anneeDiff - 1;
                    } else if (($dateNaissance[1] > $dateVisite[1]) ||
                              (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] > $dateVisite[0]))) {
                        $age = $anneeDiff;
                    } else if (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] === $dateVisite[0])) {
                        $age = $anneeDiff;
                    } else {
                        $age = 'Date de naissance incorrecte.';
                    }
                    
                    if ($age < 4) {
                        $tarif = 0;
                        $tarifDesc = 'Enfant de moins de 4 ans : Entrée gratuite';
                    } else if ($age >= 4 && $age < 12) {
                        $tarif = 8;
                        $tarifDesc = 'Tarif enfant, de 4 à 11 ans';
                    } else if ($age >= 12 && $age < 60) {
                        $tarif = 16;
                        $tarifDesc = 'Tarif normal, de 12 à 59 ans';
                    } else if ($age >= 60) {
                        $tarif = 12;
                        $tarifDesc = 'Tarif senior, 60 ans et plus';
                    }
                    
                    /**********************************************************************************************
                    *********************** GESTION DU PANIER *****************************************************
                    **********************************************************************************************/
                    
                    var $idBillet = $(this).parent().parent().attr('id'),
                        $testPanier = $('#panierBillets').find('p').hasClass($idBillet),
                        $prixTotal = 0;
                    
                    if ($testPanier === true) {
                        $('p.'+$idBillet).text($tarifDesc + ' - ' + $tarif + '€');
                        for (i = 0; i < $tabTarif.length; i++) {
                            if ($tabTarif[i].id === $idBillet) {
                                $tabTarif[i].prix = $tarif;
                            }
                        }
                    } else {
                        var $ajoutPanier = $('<p></p>');
                        $ajoutPanier.addClass($idBillet).text($tarifDesc + ' - ' + $tarif + '€');
                        $('#panierBillets').append($ajoutPanier);
                        $tabTarif.push({'id':$idBillet , 'prix': $tarif});
                    }
                    
                    for (i = 0; i < $tabTarif.length; i++) {
                        $prixTotal += parseFloat($tabTarif[i].prix);
                        console.log($prixTotal);
                    }
                    
                    $('#totalPanier').val(parseFloat($prixTotal));
                }
            });
        }
        
        /**********************************************************************************************
        *********************** FONCTION DE SUPRESSION DE BILLET **************************************
        **********************************************************************************************/
        
        function suppBillet() {
            
            $('div#commande_billets > div.form-group:last-of-type').remove();
            index--;
            $('#commande_qte').val(index);
        }
    });
});