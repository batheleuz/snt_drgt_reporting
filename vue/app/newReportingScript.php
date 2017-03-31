<script type="text/javascript">

    var loader ="<div class='loader'><img src='<?= URL ?>image/squares.gif' class='w3-round img-loader'/></div>";
    
    var selected_col = null;
    
    var selecteds_cols = Array();
    
    $("body").append(loader);

    function formValidator() {
        var nom = $("input[name='nom_reporting']") ,
            kpi = $("select[name='kpi[]']") ,
            dateD = $("input[name='date_debut']") ,
            dateF = $("input[name='date_fin']") ;
        $(".w3-feedback").remove();
        $("*").removeClass("w3-pale-red");

        if( dateD.val().length  == 0 ){
            dateD.parent()
                .append("<p class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i>Veillez choisir une date .</p>");
            dateD.addClass("w3-pale-red").focus();
            return false;
        }
        if( dateF.val().length  == 0 ){
            dateF.parent()
                .append("<p class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i>Veillez choisir une date .</p>");
            dateF.addClass("w3-pale-red").focus();
            return false;
        }
        if( nom.val().length  == 0 ){
            nom.parent()
                .append("<p class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i>Veillez donner un nom .</p>");
            nom.addClass("w3-pale-red").focus();
            return false;
        }
        if(kpi.children(":selected").length  == 0 ){
            kpi.addClass("w3-pale-red") ;
            kpi.parent().append("<span class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i> Veillez choisir les KPI à calculer.</span>") ;
            return false;
        }
        return true;
    }

    function switcher ( id_1 , id_2 ) {
    
        $("#"+id_1).css({"display" : "none" });
        $("#"+id_2).css({"display" : "block" });
    
    }
    
    function exporter ( name ) {
    
        $("#tabl").table2excel({
            exclude: ".noExl",
            name: "Excel Document Name",
            filename: "Exportation_"+name,
            fileext: ".xls",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    }
    
    function enrgReporting( classe ) {
        var dialogBox  = $("#dialog-confirm") ;

        dialogBox.html("<h3 class='w3-center w3-text-teal'>Voulez vous vremment enregistrer ce reporting comme projet ?</h3>");
        dialogBox.dialog({

            resizable:false, height: 270, modal:true , width: 400, title: "Confirmation" ,
            buttons: {
                "Enregistrer": function () {
                    dialogBox.html("<h1 class='w3-center w3-text-teal'><i class='fa fa-2x fa-spin fa-pulse '></i></h1>");
                    /* $("body").append(loader); */

                    $.post("<?= URL; ?>ajax/reporting", { action: "enrgLastReporting" , classe : classe })
                    .done(function (data) {
                        console.log(data) ;
                        window.setTimeout(function() {
                            if (data == "1") {
                                dialogBox.html("<h3 class='w3-center w3-text-teal'><i class='fa fa-check'></i> Enregistrement Terminé.</h3>");
                                window.setTimeout(function () {
                                    dialogBox.dialog("close");
                                }, 1500);
                            }
                            else if (data == "0")
                                dialogBox.html("<h3 class='w3-center w3-text-red'><i class='fa fa-times'></i> Enregistrment a échoué. </h3>");
                            else if(data == "-1")
                                dialogBox.html("<h3 class='w3-center w3-text-red'><i class='fa fa-times'></i> Un reporting du même nom existe déjà.</h3>");

                         }, 500 );
                    });
                } ,
                "Annuler" : function(){
                    $(this).dialog("close");
                }
            }
        })
    }
    
    function getValue() {
    
        var innerVal = $("#col_distincts_values") , dateD = $("input[name='date_debut']") , dateF = $("input[name='date_fin']") ;
        $(".w3-feedback").remove();
        dateD.removeClass("w3-pale-red");
        dateF.removeClass("w3-pale-red");
        if( dateD.val().length  == 0 ){
            dateD.parent()
                .append("<p class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i>Veillez choisir une date .</p>");
            dateD.addClass("w3-pale-red").focus();
            return false;
        }
        else if( dateF.val().length  == 0 ){
            dateF.parent()
                .append("<p class='w3-feedback w3-text-red w3-right-align'><i class='fa fa-times'></i>Veillez choisir une date .</p>");
            dateF.addClass("w3-pale-red").focus();
            return false;
        }
        else{
            document.getElementById('modal_add_filter').style.display='block';
            $("select[name='colonne']").chosen().change( function(){
    
                selected_col = $(this).val();
    
                innerVal.html("<div class='w3-center' style='margin-top:75px;'><i class='fa fa-pulse fa-pulse fa-3x fa-fw'></i></div>");
                $.post("<?= URL; ?>ajax/reporting", { action: "getDistincts", colonne :selected_col , date_debut : dateD.val()  , date_fin : dateF.val() })
                    .done( function (data) {
                      var liste = "<form id='col_filter'>" ;
                        try{
                         var valeurs= $.parseJSON(data);
                             $.each( valeurs ,function (key , value) {
                                 if( value.val == "")
                                     value.val ="(vide)";

                                 liste = liste + "<div class='w3-half'> " +
                                     "<input type='checkbox' name='"+selected_col+"[]' class='w3-check' " +
                                     "value ='"+ value.val +"' > " +
                                     "<label class='w3-validate'> " + value.val + "</label>" +
                                     "</div> ";
                             });
                             liste = liste + "</form>";
                             innerVal.html(liste);
                        } catch( err ){ innerVal.html( "<p class='w3-text-red'>Echec lors du chargement des données</p>" ); }
                });
            });
    
        }

    }
    
    function addFilter() {
    
        var checked_node = $("input[type='checkbox']:checked") ;
        var checked_value = Array();
    
        $.each( checked_node , function (key, value) {
            checked_value.push( value.value );
        });
    
        if( $.inArray( selected_col , selecteds_cols ) == -1 ){
    
            selecteds_cols.push( selected_col );
            $("#filtered_col")
                .prepend("<input type ='hidden' name='colonnes_selected[]' value='"+selected_col+"' >")
                .prepend("<input type='hidden' value ='"+checked_value.toString()+" ' name='"+selected_col+"' >");
    
        }else{
    
            $("input[name='"+selected_col+"']").remove();
            $("fieldset#field"+selected_col ).remove();
            $("#filtered_col")
                .prepend("<input type='hidden' value ='"+checked_value.toString()+" ' name='"+selected_col+"' >");
    
        }
    
        $("#filtered_col").append( printFilter(checked_value) );
        document.getElementById("modal_add_filter").style.display='none';
    
    }
    
    function printFilter( checked_values ) {
    
       if( selected_col != null && checked_values.length > 0 ){
    
           var printer = "<fieldset id='field"+selected_col+"'><legend><b>"+selected_col+"</b></legend><div class='w3-padding'>";
               $.each(checked_values , function(k , v ){
                   printer = printer + "<span class='w3-tag' style='margin-left:2px;margin-bottom:2px;'>"+v+"</span>" ;
               });
               printer = printer + "</fieldset>";
           return printer;
       }
       return null;
    
    }
    
    $("#form").submit(function( event ) {
        event.preventDefault();
        var loader = "<div class='loader'><img src='<?= URL ?>image/hourglass.gif' class='w3-round img-loader' /></div>";

        if( formValidator () ){

            $("body").append(loader);

            $.ajax({
                url: '<?= URL ?>ajax/calcule',
                type: "POST", data: new FormData(this),
                contentType: false, cache: false, processData:false
            }).done(function( data ){
                window.setTimeout(function() { $("div.loader").remove(); }, 500);
                $("#result") .html(data);
                switcher("content" , "result");
            }).fail(function(){ console.log("impossile"); });

        }
    
    });
    
    $(document).ready(function() {
    
        $( "select.uniq" ).selectmenu({width: 450}) .selectmenu( "menuWidget" );
    
        $( ".datepicker" ).datepicker({ regional : "fr" , firstDay : 1  });
    
        $( "input[type='checkbox'], input[type='radio']" ).checkboxradio();
    
        $('.chosen-select').chosen({ width: "450px" ,  no_results_text: "Pas de resultat!" });
    
        $("select[name='colonne']").chosen({ width: "420px" ,  no_results_text: "Pas de resultat!" });
    
        $( ".w3-btn-floating" ).tooltip({ show: { effect: "slideDown",  delay : 300 } });
    
        window.setTimeout(function() { $("div.loader").remove(); }, 500);
    
    });
    
</script>