<script type="text/javascript">

    var loader ="<div class='loader'><img src='<?= URL ?>image/squares.gif' class='w3-round img-loader' /></div>";
    
    function verbose(color, txt ){
        return "<div class='w3-panel w3-leftbar "+color+" '> <span class='w3-closebtn' onclick=\"this.parentElement.style.display='none'\"> x </span>"+
            "<p>"+txt+"</p></div>" ;
    }

    function fichier( li ){
        $("body").append(loader);
        $.post("<?= URL ?>ajax/config" , { action:"get_file_code" , colonne: li.getAttribute("data-col") })
            .done(function (data){
                var list = $.parseJSON(data);
                $("#list_code").html("");
                $.each( list ,function ( key , value ) {
                    var li = "<li> <span class='w3-text-dark-gray' style='display:inline-block;width:70px;'>"+ value.code +"</span> - "+
                        "<span class='w3-text-gray'>"+ value.signification +"</span></li>";
                    $("#list_code").append(li);
                });
            });
        window.setTimeout(function() { $("div.loader").remove(); }, 500);
    }

    function chercher(){

        var input, filter, ul, li, a, i;
        input = document.getElementById("code_filter");
        filter = input.value.toUpperCase();
        ul = document.getElementById("list_code");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {

            if ( li[i].innerHTML.toUpperCase().indexOf(filter) > -1 ) { li[i].style.display = ""; }
            else { li[i].style.display = "none"; }

        }
    }

    $("body").append(loader);

    $(document).ready(function(){

        $("select[name='colonne']").chosen({width: "100%", no_results_text: "Pas de resultat!" });

        $("#form_add_codif").submit(function(event ){
            event.preventDefault();

            $("body").append(loader);
            $.ajax({ url: '<?= URL ?>ajax/uploader', type: "POST", data: new FormData(this), contentType: false, cache: false, processData:false })
                .done(function(data){
                    if( parseInt(data) > 0 ){

                        $("#list_col").append("<li> <i class='fa fa-spinner fa-pulse fa-fw' aria-hidden='true'></i> </li>");

                        $("#verbose").html(verbose( "w3-green" , data + " nouveaux codes enregistrés."));

                        window.setTimeout(function() {
                            $.post("<?= URL ?>ajax/config" , { action:"list_files"  })
                                .done(function (data){
                                    var list = $.parseJSON(data);
                                    $("#list_col").html("");
                                    $.each( list ,function ( key , value ) {

                                        var li =   "<li class='w3-padding' onclick=\"fichier(this);\" style='cursor:pointer;' data-col='"+value.col+"' >"+
                                            value.col + "<span class='w3-badge w3-right w3-margin-right'> "+value.nbre+"</span> </li>";
                                        $("#list_col").append(li);

                                    });
                                });
                        }, 2500);

                    }else if(parseInt(data) == 0 ){
                        $("#verbose").html(verbose( "w3-yellow", "Pas de nouveau code à ajouter "));
                    }else if(parseInt(data) == -1 ){
                        $("#verbose").html(verbose( "w3-red", "L'entête du fichier est incorrect. <br> Veillez lire les indications. "));
                    }else if(parseInt(data) == -2 ){
                        $("#verbose").html(verbose( "w3-red", "Impossible d'ouvrir le fichier.")) ;
                    }else{
                        $("#verbose").html(verbose( "w3-red", data )) ;
                    }

                    window.setTimeout(function() { $("div.loader").remove(); }, 500);

                });

        });
        window.setTimeout(function() { $("div.loader").remove(); }, 500);
    });

</script>