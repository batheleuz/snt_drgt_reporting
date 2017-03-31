function verbose( color , txt){  // Function pour alerter prend en argument le texte a écrire

  return "<div class='w3-panel "+ color +" w3-round popupunder alert '> " +
         "<span onclick=\"this.parentElement.style.display='none'\" class='w3-closebtn'>&times;</span> "+
         "<h3><u></u></h3>"+
         "<p>"+ txt +"</p></div>" ;
}

function myFunction() {

    var input, filter, ul, li, a, i;
    input = document.getElementById("kpi_filter");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        if ( li[i].innerHTML.toUpperCase().indexOf(filter) > -1 ) { li[i].style.display = ""; }
        else { li[i].style.display = "none"; }
    }

}