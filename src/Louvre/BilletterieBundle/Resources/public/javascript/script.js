$(function () {
    
    function Commande(dateVisite, typeBillet, nbVisiteur, totalPrix) {
            
        this.dateVisite = dateVisite;
        this.typeBillet = typeBillet;
        this.nbVisiteur = nbVisiteur;
        this.totalPrix  = totalPrix;
    
    }
    
    function Billet(nomBillet, prenomBillet, paysBillet, naissanceBillet, idBillet, nomTarif, prixTarif) {
        
        this.nomBillet       = nomBillet;
        this.prenomBillet    = prenomBillet;
        this.paysBillet      = paysBillet;
        this.naissanceBillet = naissanceBillet;
        this.idBillet        = idBillet;
        this.nomTarif        = nomTarif;
        this.prixTarif       = prixTarif;
    }
    
    function Facturation(nomFacturation, prenomFacturation, paysFacturation, naissanceFacturation) {
        
        this.nomFacturation       = nomFacturation;
        this.prenomFacturation    = prenomFacturation;
        this.paysFacturation      = paysFacturation;
        this.naissanceFacturation = naissanceFacturation;
        
    }
    
    /**********************************************************************************************
    *********************** VARIABLES *************************************************************
    **********************************************************************************************/
    
    var $dateConnexion = $('#dateConnexion').text(),
        $dateConnexionSp = $dateConnexion.split('/'),
        $pickerReserv = $.datepicker.parseDate("dd/mm/yy", $dateConnexion),
        $heureConnexion = parseInt($('#heureConnexion').text()),
        $commande,
        $facturation,
        $billets = [],
        $tabTarif = [],
        $indexEtape,
        $indexBillet = 0,
        $validationChoix = 0,
        $prixTotal = 0,
        $checkNom,
        $checkPrenom,
        $checkPays,
        $checkDate,
        $checkTarif,
        $checkCourriel,
        $dateValide,
        $tarifReduit,
        $nbBillet,
        regexDate = /^[0-3][0-9]\/[0-1][0-9]\/[1-2][901][0-9][0-9]$/,
        regexNom = /^[a-zA-Z-'éíóúèìòùâêîôûëäïöüãñæçÉÍÓÚÈÌÒÙÂÊÎÔÛËÄÏÖÜÃÑÆÇ]+$/,
        regexCourriel = /^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,10}$/,
        $messageVide = '<p class="messageErreur">Doit comporter entre 2 et 30 caractères alphabétiques.</p>',
        $messagePays = '<p class="messageErreur">Veuillez indiquer votre pays de résidence.</p>',
        $messageDate = '<p class="messageErreur">Date incorrecte, format : JJ/MM/AAAA</p>',
        $messageAge = '<p class="messageErreur">Entrée gratuite pour les moins de 4 ans.</p>',
        $messageTarifReduit = '<p class="messageErreur">Tarif réduit non applicable.</p>',
        $messageCourrielFormat = '<p class="messageErreur">Adresse courriel invalide, format : adresse@mail.com</p>',
        $messageCourrielInval = '<p class="messageErreur">Le champ de confirmation doit être identique au champ courriel.</p>';

    /**********************************************************************************************
    *********************** FONCTIONS *************************************************************
    **********************************************************************************************/
    
    function checkDateTap(selecteur) {
    
        selecteur.keydown(function (e) {
            if (e.keyCode !== 193 && e.keyCode !== 111) {
                if (e.keyCode !== 8) {
                    if ($(this).val().length === 2) {
                        $(this).val($(this).val() + '/');
                    } else if ($(this).val().length === 5) {
                        $(this).val($(this).val() + '/');
                    }
                } else {
                    var temp = $(this).val();
                    if ($(this).val().length === 5) {
                        $(this).val(temp.substring(0, 4));
                    } else if ($(this).val().length === 2) {
                        $(this).val(temp.substring(0, 1));
                    }
                }
            } else {
                var temp = $(this).val(),
                    tam = $(this).val().length;
                $(this).val(temp.substring(0, tam - 1));
            }
        });
    }
    
    function addErreurs (valeur, message) {
        valeur.addClass('champVide');
            if (!valeur.next().hasClass('messageErreur')) {
                valeur.parent().append(message);
        }
    }
    
    function removeErreurs (valeur) {
        valeur.removeClass('champVide');
            if (valeur.next().hasClass('messageErreur')) {
                valeur.next().remove();
            }
    }
    
    function ajoutPanier (billet) {
        var $testPanier = $('#panierBillets').find('div').hasClass(billet.idBillet);
        
        if ($testPanier === true) {
            
            $('div.' + billet.idBillet + ' > p.nomPanier').text('Nom : ' + billet.nomBillet);
            $('div.' + billet.idBillet + ' > p.tarifPanier').text(billet.nomTarif + ' : ' + billet.prixTarif + '€');
            
        } else {
            var $ajoutPanier = $('<div></div>'),
                $ajoutNom = $('<p class="nomPanier"></p>'),
                $ajoutPrenom = $('<p class="prenomPanier"></p>'),
                $ajoutTarif = $('<p class="tarifPanier"></p>');
            
            $ajoutPanier.addClass(billet.idBillet);
            $ajoutNom.text('Nom : ' + billet.nomBillet);
            $ajoutPrenom.text(' Prenom : ' + billet.prenomBillet);
            $ajoutTarif.text(billet.nomTarif + ' : ' + billet.prixTarif + '€');
            
            $ajoutPanier.append($ajoutNom);
            $ajoutPanier.append($ajoutPrenom);
            $ajoutPanier.append($ajoutTarif);
            
            $('#panierBillets').append($ajoutPanier);
            $('#icoPanier').text($billets.length);
        }
        
        $prixTotal = 0;
                    
        for (var i = 0; i < $billets.length; i++) {
            $prixTotal += parseFloat($billets[i].prixTarif);
        }
                    
        if ($('#commande_demiJournee_1').is(':checked')) {
            $('#totalPanier').text('TOTAL (-50% demi-journée) : ' + ($prixTotal/2) + '€');
        } else {
            $('#totalPanier').text('TOTAL : ' + $prixTotal + '€');
        }
    }
    
    function creationBillet (nom, prenom, pays, naissance, idBillet, nomTarif, prix) {
        
        var $testBilletMatch = 0;
        
        if ($billets.length === 0) {
            
            var billet = new Billet(nom, prenom, pays, naissance, idBillet, nomTarif, prix);
            $billets.push(billet); 
            ajoutPanier (billet);
            
        } else {
            
            for (var i = 0; i < $billets.length; i++) {

                if ($billets[i].idBillet === idBillet) {
                    
                    $testBilletMatch = 1;

                    $billets[i].nomBillet       = nom;
                    $billets[i].prenomBillet    = prenom;
                    $billets[i].paysBillet      = pays;
                    $billets[i].naissanceBillet = naissance;
                    $billets[i].nomTarif        = nomTarif;
                    $billets[i].prixTarif       = prix;
                    
                    ajoutPanier ($billets[i]);

                } 
            }
            
            if ($testBilletMatch === 0) {
                var billet = new Billet(nom, prenom, pays, naissance, idBillet, nomTarif, prix);
                $billets.push(billet);
                ajoutPanier (billet);
            }
        } 
    } 
    
    function isValidDate(date) {
        var $dateSplit =  date.split('/');
        
        if (($dateSplit[2] > $dateConnexionSp[2]) ||
            ($dateSplit[2] === $dateConnexionSp[2] && $dateSplit[1] > $dateConnexionSp[1]) ||
            ($dateSplit[2] === $dateConnexionSp[2] && $dateSplit[1] === $dateConnexionSp[1] && 
             $dateSplit[0] > $dateConnexionSp[0])) {
            
            $dateValide = 1;
            
        } else if ($dateSplit[0] > 31 || $dateSplit[0] === 0) {
            
            $dateValide = 1;
            
        } else if (($dateSplit[0] > 30) && ($dateSplit[1] === '04' || $dateSplit[1] === '06' ||
                     $dateSplit[1] === '09' || $dateSplit[1] === '11')) {
            
            $dateValide = 1;
            
        } else if ($dateSplit[1] === '02' && $dateSplit[0] > 28) {
            
            if ($dateSplit[0] === '29' && ($dateSplit[2] % 400 === 0 || ($dateSplit[2] % 100 !== 0 && $dateSplit[2] % 4 === 0))) {
                
                $dateValide = 0;
                
            } else {
                
                $dateValide = 1;
                
            }
  
        } else {
            
            $dateValide = 0;
            
        }
    }
    
    function testFormDate(date) {
        
        if (date.val().length < 10 || date.val() === '' || date.val() === null || !regexDate.test(date.val())) {
            addErreurs(date, $messageDate);
            $checkDate = 1;
        
        } else {
            
            isValidDate(date.val());
           
            if ($dateValide === 1) {
                addErreurs(date, $messageDate);
                $checkDate = 1;
            } else if (date.hasClass('champVide')) {
                removeErreurs(date);
                $checkDate = 0;
            }
        }
    }
    
    function testTarifReduit() {
        
        if ($('#commande_billets_' + $indexBillet + '_tarifReduit').is(':checked')) {
            
            $tarifReduit = 1;
            
        } else {
            
            $tarifReduit = 0;
            $checkTarif = 0;
            
            if ($('#commande_billets_' + $indexBillet + '_tarifReduit').hasClass('champVide')) {
                
                removeErreurs($('#commande_billets_' + $indexBillet + '_tarifReduit'));
                
            }
        }
    }
    
    function testFormBillet(nom, prenom, pays, date) {
        
        if (nom.val().length <= 1 || nom.val().length > 30 || nom.val() === '' || !regexNom.test(nom.val())) {
            
            addErreurs(nom, $messageVide);
            $checkNom = 1;

        } else {
        
            if (nom.hasClass('champVide')) {
                removeErreurs(nom);
                $checkNom = 0;
            }
        }
        
        if (prenom.val().length <= 1 || nom.val().length > 30 || prenom.val() === '') {
        
            addErreurs(prenom, $messageVide);
            $checkPrenom = 1;

        } else {
        
            if (prenom.hasClass('champVide')) {
                removeErreurs(prenom);
                $checkPrenom = 0;
            }
        }
        
        if (pays.val() === null || pays.val() === '') {
            
            addErreurs(pays, $messagePays);
            $checkPays = 1;

        } else {
        
            if (pays.hasClass('champVide')) {
                removeErreurs(pays);
                $checkPays = 0;
            }
        }
        testFormDate(date);
    }
    
    function testCourriel (courriel) {
        
        if (!regexCourriel.test(courriel.val())) {
            
            addErreurs(courriel, $messageCourrielFormat);
            $checkCourriel = 1;
            
        } else {
            
            if (courriel.hasClass('champVide')) {
                
                removeErreurs(courriel);
                $checkCourriel = 0;
                
            }
        }
    }
    
    function testCourrielValid (courriel, validCourriel) {
        if (courriel.val() != validCourriel.val()) {
            
            addErreurs(validCourriel, $messageCourrielInval);
            $checkCourriel = 1;
                
        } else {
            
            if (validCourriel.hasClass('champVide')) {
                
                removeErreurs(validCourriel);
                $checkCourriel = 0;
                
            }
        }
    }
    
    function deffTarif (date) {
        
        var $dateVisite = $('.dateReserv').val().split('/'),
            $dateNaissance = $(date).val().split('/'),
            $anneeDiff = $dateVisite[2] - $dateNaissance[2],
            $age,
            $tarif,
            $tarifDesc,
            $nomTarif;
        
        testTarifReduit();
                        
        /**********************************************************************************************
        *********************** TEST DATE DE NAISSANCE/AGE ********************************************
        **********************************************************************************************/
                   
        if (regexDate.test($('.dateReserv').val())) {
            if (($dateNaissance[1] < $dateVisite[1]) ||
                (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] < $dateVisite[0]))) {
                $age = $anneeDiff - 1;
            } else if (($dateNaissance[1] > $dateVisite[1]) ||
                (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] > $dateVisite[0]))) {
                $age = $anneeDiff;
            } else if (($dateNaissance[1] === $dateVisite[1]) && ($dateNaissance[0] === $dateVisite[0])) {
                $age = $anneeDiff;
            } 
        } else {
            $age = false;
        }
                    
        if ($age < 4) {
            $tarif = 0;
            $tarifDesc = 'Enfant de moins de 4 ans : Entrée gratuite';
        } else if ($tarifReduit === 1 && $age >= 12) {
            $tarif = 10;
            $tarifDesc = 'Tarif réduit unique';
            $nomTarif = 'reduit';
            $checkTarif = 0;
            if ($('#commande_billets_' + $indexBillet + '_tarifReduit').hasClass('champVide')) {
                removeErreurs($('#commande_billets_' + $indexBillet + '_tarifReduit'));
            }
        } else if ($age >= 4 && $age < 12) {
            $tarif = 8;
            $tarifDesc = 'Tarif enfant, de 4 à 11 ans';
            $nomTarif = 'enfant';
        } else if ($age >= 12 && $age < 60) {
            $tarif = 16;
            $tarifDesc = 'Tarif normal, de 12 à 59 ans';
            $nomTarif = 'normal';
        } else if ($age >= 60) {
            $tarif = 12;
            $tarifDesc = 'Tarif senior, 60 ans et plus';
            $nomTarif = 'senior';
        } else {
            $tarif = 0;
            $tarifDesc = 'Date de naissance invalide.';
            $nomTarif = 'invalide';
        }
        
        if ($tarifReduit === 1 && $age < 12){ 
            $checkTarif = 1;
            addErreurs($('#commande_billets_' + $indexBillet + '_tarifReduit'), $messageTarifReduit);
            return false;
        } else if ($age < 4 || $tarif === 0) {
            $checkTarif = 1;
            addErreurs($('#commande_billets_' + $indexBillet + '_naissanceBillet'), $messageAge);
            return false;
        }
        
        creationBillet($('#commande_billets_' + $indexBillet + '_nomBillet').val(),
                       $('#commande_billets_' + $indexBillet + '_prenomBillet').val(),
                       $('#commande_billets_' + $indexBillet + '_paysBillet').val(),
                       $('#commande_billets_' + $indexBillet + '_naissanceBillet').val(),
                       $('#commande_billets_' + $indexBillet + '_nomBillet').parent().parent().attr('id'),
                       $tarifDesc,
                       $tarif);
        
        $('#commande_billets_' + $indexBillet + '_prixBillet').val($tarif);
        $('#commande_billets_' + $indexBillet + '_tarifBillet').val($nomTarif);
        
    }
    
    /**********************************************************************************************
    *********************** PARAMETRES DE DEPART **************************************************
    **********************************************************************************************/

    $('#addBillet').prop('disabled', true);
    $('#supBillet').prop('disabled', true);
    $('#subForm').prop('disabled', true);
    $('#totalPanier').val(0);
    $('#commande_demiJournee_0').attr('checked', false).prop('disabled', true);
    $('#commande_demiJournee_1').attr('checked', false).prop('disabled', true);
    $('#next1').prop('disabled', true);
    checkDateTap($('.dateReserv'));
    checkDateTap($('.dateFacturation'));
    
    /**********************************************************************************************
    *********************** ANIMATION PANIER ******************************************************
    **********************************************************************************************/
    
    $('#btPanier').click(function () {
        if ($('#panier').is(":visible")) {
            $('#panier').fadeOut(250);
        } else {
            $('#panier').fadeIn(250);
        }
    });
    
    $(window).scroll(function () {
        $('#panier').fadeOut(50);
    })
    
    /**********************************************************************************************
    *********************** NAVIGATION ETAPES *****************************************************
    **********************************************************************************************/
    
    $('#next0').click(function () {
        
        $indexEtape = 0;
        
        $('#secuHorraire').hide(1000, function () {
            $('#bloc_choix').show(800);
        });
        
    });
        
    $('#next1').click(function () {

        if (($indexEtape === 0) && ($validationChoix === 0)) {
            
            $indexEtape = 1;
            
            $indexBillet = 0;
            
            $commande = new Commande();
            
            $commande.dateVisite = $('.dateReserv').val();
            $commande.typeBillet = $('#typeBillet').text();
            $commande.nbVisiteur = $('#commande_qte').val();
            $commande.totalPrix  = 0;
            
            $nbBillet = $('#commande_qte').val();
            
            if ($nbBillet > 1) {
                $('#next2').prop('disabled', true);
            } else {
                $('#next2').prop('disabled', false);
            }
            
            $('#step1').removeClass('stepActuOn');
            $('#step1').addClass('stepActuOff');
            
            $('#bloc_choix').hide(1000, function () {
                $('#step2').removeClass('stepActuOff');
                $('#step2').addClass('stepActuOn');
                $('#bloc_billet').show(800);
                $('.blocUnBillet0').show(800);
                $('#backB').prop('disabled', true);
                if ($commande.nbVisiteur <= 1) {
                    $('#nextB').prop('disabled', true);
                } else if ($commande.nbVisiteur > 1) {
                    $('#nextB').prop('disabled', false);
                }
            });
        }
    });
        
    $('#next2').click(function () {
        
        if ($indexEtape === 1) {
            
            testFormBillet($('#commande_billets_' + $indexBillet + '_nomBillet'),
                           $('#commande_billets_' + $indexBillet + '_prenomBillet'),
                           $('#commande_billets_' + $indexBillet + '_paysBillet'),
                           $('#commande_billets_' + $indexBillet + '_naissanceBillet'));
                             
            if ($checkNom === 1 || $checkPrenom === 1 || $checkPays === 1 || $checkDate === 1) {
                return false;
            }

            deffTarif($('#commande_billets_' + $indexBillet + '_naissanceBillet'));
            
            if ($checkTarif === 1) {
                return false;
            }
            
            $indexEtape = 2;
            
            $('.blocUnBillet' + $indexBillet).hide(800);
            
            $('#step2').removeClass('stepActuOn');
            $('#step2').addClass('stepActuOff');
            
            $('#bloc_billet').hide(1000, function (){
                $('#step3').removeClass('stepActuOff');
                $('#step3').addClass('stepActuOn');
                $('#bloc_facturation').show(800);
            });    
        }
    });
    
    $('#next3').click(function() {                 
                      
        if ($indexEtape === 2) {
            
            testFormBillet($('#commande_facturation_nomFacture'),
                           $('#commande_facturation_prenomFacture'),
                           $('#commande_facturation_pays'),
                           $('#commande_facturation_naissanceFacture'));
            
            testCourriel ($('#commande_facturation_courriel_first'));
            
            testCourrielValid ($('#commande_facturation_courriel_first'), 
                               $('#commande_facturation_courriel_second'));
                             
            if ($checkNom === 1 || $checkPrenom === 1 || $checkPays === 1 || $checkDate === 1  || $checkCourriel === 1) {
                return false;
            }
            
            $indexEtape = 3;
            
            $facturation = new Facturation();
            
            $facturation.nomFacturation       = $('#commande_facturation_nomFacture').val();
            $facturation.prenomFacturation    = $('#commande_facturation_prenomFacture').val();
            $facturation.paysFacturation      = $('#commande_facturation_pays option:selected').text();
            $facturation.naissanceFacturation = $('#commande_facturation_naissanceFacture').val();
            $facturation.courrielFacturation  = $('#commande_facturation_courriel_first').val();
            $facturation.courrielValidation   = $('#commande_facturation_courriel_second').val();
            
            $('#ValNomFa').text($facturation.nomFacturation);
            $('#ValPreFa').text($facturation.prenomFacturation);
            $('#ValPaysFa').text($facturation.paysFacturation);
            $('#ValCourrielFa').text($facturation.courrielFacturation);
            $('#ValTotal').text($('#totalPanier').text());
            
            $('#recap').empty();
            $('#panierBillets').clone().appendTo($('#recap'));
            
            $('#step3').removeClass('stepActuOn');
            $('#step3').addClass('stepActuOff');
            
            $('#bloc_facturation').hide(1000, function (){
                $('#step4').removeClass('stepActuOff');
                $('#step4').addClass('stepActuOn');
                $('#bloc_validation').show(800);
            });
        }
    });
    
    $('#back2').click(function() {
        
        if ($indexEtape === 1) {
            
            $indexEtape = 0;
            
            $('.blocUnBillet' + $indexBillet).hide(800);
            
            $indexBillet = 0;
            
            $('#step2').removeClass('stepActuOn');
            $('#step2').addClass('stepActuOff');
            
            $('#bloc_billet').hide(1000, function (){
                $('#step1').removeClass('stepActuOff');
                $('#step1').addClass('stepActuOn');
                $('#bloc_choix').show(800);
            });
            $('#back').prop('disabled', true);
        } 
    });
    
    $('#back3').click(function() {
        
        if ($indexEtape === 2) {
            
            $indexEtape = 1;
            
            $indexBillet = 0;
            
            $('#step3').removeClass('stepActuOn');
            $('#step3').addClass('stepActuOff');
            
            $('#bloc_facturation').hide(1000, function (){
                $('#step2').removeClass('stepActuOff');
                $('#step2').addClass('stepActuOn');
                if ($nbBillet > 1) {
                    $('#nextB').prop('disabled', false);
                    $('#next2').prop('disabled', true);
                }
                $('#backB').prop('disabled', true);
                $('#bloc_billet').show(800);
                $('.blocUnBillet0').show(800);
            });
        }
    });
        
    $('#back4').click(function() {
    
        if ($indexEtape === 3) {
            
            $indexEtape = 2;
            
            $('#step4').removeClass('stepActuOn');
            $('#step4').addClass('stepActuOff');
            
            $('#bloc_validation').hide(1000, function (){
                $('#step3').removeClass('stepActuOff');
                $('#step3').addClass('stepActuOn');
                $('#bloc_facturation').show(800);
            });
        }
    });
    
    /**********************************************************************************************
    *********************** NAVIGATION BILLETS ****************************************************
    **********************************************************************************************/
        
    $('#nextB').click(function (e) {
            
        testFormBillet($('#commande_billets_' + $indexBillet + '_nomBillet'),
                        $('#commande_billets_' + $indexBillet + '_prenomBillet'),
                        $('#commande_billets_' + $indexBillet + '_paysBillet'),
                        $('#commande_billets_' + $indexBillet + '_naissanceBillet'));
                             
        if ($checkNom === 1 || $checkPrenom === 1 || $checkPays === 1 || $checkDate === 1) {
            return false;
        }
        
        deffTarif($('#commande_billets_' + $indexBillet + '_naissanceBillet'));
        
        if ($checkTarif === 1) {
                return false;
        }
        
        $('#nextB').prop('disabled', true);
        $('#backB').prop('disabled', true);
            
        $('.blocUnBillet' + $indexBillet).fadeOut(500, function () {
            $indexBillet += 1;
                
            $('#nextB').prop('disabled', false);
            $('.blocUnBillet' + $indexBillet).fadeIn(250);
                
            if ($indexBillet >= (($('#commande_qte').val()) - 1)) {
                $('#nextB').prop('disabled', true);
                $('#backB').prop('disabled', false);
                $('#next2').prop('disabled', false);
            } else {
                $('#nextB').prop('disabled', false);
                $('#backB').prop('disabled', false);
            }
        });
    });
                
    $('#backB').click(function (e) {
            
        $checkNom = 0;
        $checkPrenom = 0;
        $checkPays = 0;
        $checkDate = 0;
        $checkTarif = 0;
        $tarifReduit = 0;
            
        $('#backB').prop('disabled', true);
        $('#nextB').prop('disabled', true);
            
        if (($indexBillet < $('#commande_qte').val()) && ($('#commande_qte').val() > 1)) {
            $('#next2').prop('disabled', true);
        }
                        
        $('.blocUnBillet' + $indexBillet).fadeOut(500, function () {
                
            $indexBillet -= 1;
            $('#backB').prop('disabled', false);
            $('.blocUnBillet' + $indexBillet).fadeIn(250);
                
            if ($indexBillet <= 0) {
                $('#backB').prop('disabled', true);
                $('#nextB').prop('disabled', false);
            } else {
                $('#nextB').prop('disabled', false);
                $('#backB').prop('disabled', false);
            }
        });
            
    });
    
    /**********************************************************************************************
    *********************** DATEPICKER DATE RESERVATION *******************************************
    **********************************************************************************************/
    
    $('.dateReserv').datepicker({
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
        onSelect: function(d,i){
            if(d !== i.lastVal){
                $(this).change();
            }
        }
    });
    
    /**********************************************************************************************
    *********************** DATEPICKER FACTURATION ************************************************
    **********************************************************************************************/
    
    $('.dateFacturation').datepicker({
        defaultDate: null,
        dateFormat: 'dd/mm/yy',
        changeYear: true,
        changeMonth: true,
        yearRange: '-130:',
        maxDate: $pickerReserv,
        onSelect: function(d,i) {
            if(d !== i.lastVal) {
                $(this).change();
            }
        }
    });
    
    /**********************************************************************************************
    *********************** CHANGEMENT DE DATE RESERVATION ****************************************
    **********************************************************************************************/
    
    $('.dateReserv').change(function () {
            
        $('#dateVisite').text($('.dateReserv').val());
        $('#next1').prop('disabled', true);
            
        if (($heureConnexion >= 18) && ($dateConnexion === $('.dateReserv').val())) {
            $('#commande_demiJournee_0').attr('checked', false).prop('disabled', true);
            $('#commande_demiJournee_1').attr('checked', false).prop('disabled', true);
            $('#dateVisite').text('Heure de visite dépassée pour cette date.').css('color', 'red');
            $('#typeBillet').text('Aucun type de billet selectionné.');
            $('#next1').prop('disabled', true);
        } else if (($heureConnexion >= 14) && ($dateConnexion === $('.dateReserv').val())) {
            $('#commande_demiJournee_0').attr('checked', false).prop('disabled', true);
            $('#commande_demiJournee_1').attr('checked', false).prop('disabled', false);
            $('#typeBillet').text('Demi-journée');
            $('#dateVisite').css('color', 'black');
        } else {
            $('#commande_demiJournee_0').attr('checked', false).prop('disabled', false);
            $('#commande_demiJournee_1').attr('checked', false).prop('disabled', false);
            $('#typeBillet').text('Aucun type de billet selectionné.');
            $('#dateVisite').css('color', 'black');
        }
    });
        
    /**********************************************************************************************
    *********************** CHOIX JOURNEE OU DEMI JOURNNE *****************************************
    **********************************************************************************************/
    
    $('#commande_demiJournee').change(function () {
        if ( $('#commande_qte').val() > 0 ) {
            $('#next1').prop('disabled', false);
        }
        if ($('#commande_demiJournee_0').is(':checked')) {
            $('#typeBillet').text('Journée');
            $('#addBillet').prop('disabled', false);
            $('#supBillet').prop('disabled', false);
            $('#totalPanier').text('TOTAL : ' + $prixTotal + '€');
        } else if ($('#commande_demiJournee_1').is(':checked')) {
            $('#typeBillet').text('Demi-journée');
            $('#addBillet').prop('disabled', false);
            $('#supBillet').prop('disabled', false);
            $('#totalPanier').text('TOTAL (-50% demi-journée) : ' + ($prixTotal/2) + '€');
        } else if ($('#commande_demiJournee_0').is(':not:checked') && $('#commande_demiJournee_1').is(':not:checked')) {
            $('#typeBillet').text('Aucun type de billet selectionné.');
            $('#addBillet').prop('disabled', true);
            $('#supBillet').prop('disabled', true);
            $('#subForm').prop('disabled', true);
            $('#totalPanier').text('TOTAL : ' + $prixTotal + '€');
        }
    });
    
    /**********************************************************************************************
    *********************** DECOMPTE DU NOMBRE DE VISITEUR/BILLET *********************************
    **********************************************************************************************/
    
    $('#commande_qte').change(function () {
        $('#nbPersonne').text($('#commande_qte').val());
    });
    
    /**********************************************************************************************
    *********************** AJOUT ET SUPPRESSION BILLETS ******************************************
    **********************************************************************************************/
    
    $(document).ready(function () {
        
        var $container = $('div#commande_billets'),
            index = $container.find(':input').length;

        /**********************************************************************************************
        *********************** AJOUT D'UN BILLET DANS LE DOM *****************************************
        **********************************************************************************************/
        
        $('#addBillet').click(function (e) {
            
            if (index >= 0 && index < 9) {
                addBillet($container);
                $('.date').css('background-color', '#FFF');
                $('#nbPersonne').text($('#commande_qte').val());
                $('#next1').prop('disabled', false);
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
                    $('#next1').prop('disabled', true);
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
                $prototype = $(template).addClass('well hideBillet blocUnBillet'+index);

            $container.append($prototype);
            index++;
            $('#commande_qte').val(index);
            
            $('.dateBillets').datepicker({
                defaultDate: null,
                dateFormat: 'dd/mm/yy',
                changeYear: true,
                changeMonth: true,
                yearRange: '-130:',
                maxDate: $pickerReserv,
                onSelect: function(d,i) {
                    if(d !== i.lastVal) {
                        $(this).change();
                    }
                }
            });
            checkDateTap($('.dateBillets'));
            
        }
                 
        /**********************************************************************************************
        *********************** FONCTION DE SUPRESSION DE BILLET **************************************
        **********************************************************************************************/
        
        function suppBillet() {
            
            var $testPanier = $('#panierBillets').find('div').hasClass('commande_billets_'+ (index - 1) );

            if ($testPanier === true) {
                for (i = 0; i < $billets.length; i++) {
                    if ($billets[i].idBillet === $('div.commande_billets_'+ (index - 1)).attr('class')) {
                        $prixTotal = $prixTotal - $billets[i].prixTarif;
                        $billets.splice(i,1);
                        $('#icoPanier').text($billets.length);
                    }
                }
                
                $('div.commande_billets_'+ (index - 1) ).remove();
                
                if ($('#commande_demiJournee_1').is(':checked')) {
                    $('#totalPanier').text('TOTAL (-50% demi-journée) : ' + ($prixTotal/2) + '€');
                } else {
                    $('#totalPanier').text('TOTAL : ' + $prixTotal + '€');
                }
            }
            
            $('div#commande_billets > div.form-group:last-of-type').remove();
            
            index--;
            $('#commande_qte').val(index);
        }
    });
});