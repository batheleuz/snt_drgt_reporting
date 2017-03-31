<script type="text/javascript">
   function verbose(color, txt ){
       var rt= "<div class='w3-panel w3-round w3-leftbar w3-animate-zoom "+color+" '> <span class='w3-closebtn' onclick=\"this.parentElement.style.display='none'\"> x </span>"+
               "<p>"+txt+"</p></div>"  ;
       return rt;
   }

  var feedback = $( "#feedback" ).dialog({
      modal: true,  autoOpen:false,
      width : 600,  height : 400, 
      buttons: {  Fermer: function() {  $( this ).dialog( "close" ); }  }
  });

  $(".form").submit(function( e ){

    e.preventDefault();

      var loader = "<div class='loader'><img src='<?= URL ?>image/squares.gif' class='w3-round img-loader' /></div>";

   if ( $("input[name='fichier_encours']").val().length === 0  && $("input[name='fichier_releves']").val().length === 0 ){

        $("#data").html( verbose( "w3-red", "Vous n'avez pas choisis de fihier " ) );
        $("input[name='fichier_releves']").click();

   }else{

        $("body").append(loader);

        $.ajax(
            { url: '<?= URL ?>ajax/uploader',
              type: "POST",
              data: new FormData(this),
              contentType: false,
              cache: false,
              processData:false
            })

            .done(function(data){
                
                var result = $.parseJSON(data);
                $("#alert_rsl").html("");  $("#feedback").html(""); var openDialog  = "close" ;

                $.each( result  , function( key, value ) {
                  if( value.code ==  "0" )  
                      $("#alert_rsl").append(verbose( "w3-red", value.texte ) );
                  else if ( value.code ==  "1" ){
                      openDialog = "open";

                      $("#alert_rsl").append(verbose( "w3-teal", value.texte ) );

                      var txt = "<div class='w3-center'><h6 class='w3-teal w3-padding'>" + value.texte + "</h6></div>"+
                                "<table class='w3-table-all'><tr><td> Doublons rejetées : </td><td> "+ 
                                value.doublon +"</td></tr>" + 
                                "<tr><td> Lignes Enregistrées : </td><td> "+ 
                                value.enrg +"</td></tr></table>" ;

                      $("#feedback").append( txt );    
                  }
                });
                
                 feedback.dialog(openDialog);
                 window.setTimeout(function() { $(".alert").fadeTo(2000, 500).slideUp(500, function(){ $(this).remove();  }); }, 1500);
                 window.setTimeout(function() { $("div.loader").remove(); }, 500);

                 document.getElementById("form").reset();

            })
            .fail(function(){
                alert("Impossible");
            });
   }
  });

</script>
