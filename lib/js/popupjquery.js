/**
 * Gestione del popup con jQuery.
 * 
 * Per tutti i tag html 'a href' nei quali sono indicati la class popupjq
 * 2 div vengono creati e aggiunti al tag html 'body'
 * - nel primo con id=overlay si crea lo sfondo scuro
 * - nel secondo con id=popupjq si inserisce il contenuto 
 * della pagina indicata nell'attributo href del tag html 'a'
 * Per stampare il contenuto del popup cliccare sul pulsante della stampante
 * Per chiudere il popup è sufficiente cliccare sul pulsante X.
 */
$(document).ready(function() {
    $('a.popupjq').click(function(e) {
        $('body').css('overflow-y', 'hidden');
        $('<div id="overlay"></div>')
        .css('top', $(document).scrollTop())
        .css('opacity', '0')
        .animate({'opacity': '0.75'}, 'fast')
        .appendTo('body');

        $('<div id="popupjq" class="popupjq-maxheight"></div>')
        .hide()
        .appendTo('body');

        $('#popupjq').load($(this).attr('href'), function() {
            posizionaPopup();
            
            $('#printDiv').css('cursor', 'pointer')
            .click(function() {
                printDiv();
                eliminaPopup();
                return false;
            });
            
            $('#deleteDiv').css('cursor', 'pointer')
            .click(function() {
                eliminaPopup();
                return false;
            });
        });

        e.preventDefault();
    });


});

function posizionaPopup() {
    var top = ($(window).height() - $('#popupjq').height()) / 2;
    var left = ($(window).width() - $('#popupjq').width()) / 2;
    var width = $('#popupjq').width()+16;
    popup = $('#popupjq').css({
        'top': top + $(document).scrollTop(),
        'left': left,
        'width': width
    }).fadeIn();
    try {
        popup.append("<p id='containerDiv' style='text-align:center'>\n\
<img id='printDiv' border='0' src='../immagini/stampa.png'>&nbsp;\n\
<img id='deleteDiv' border='0' src='../immagini/cancel.png'></p>");
		popup.jScrollPane();
    } catch(err) {
//        alert(err.message);
    }
}

function eliminaPopup() {
    $('#overlay, #popupjq').fadeOut('fast', function() {
        $(this).remove();
        $('body').css('overflow-y', 'auto');
    });
}

function printDiv() {
    $("p").remove("#containerDiv");
    var divContents = $("#popupjq").html();
    var printWindow = window.open('', 'printDiv');
    printWindow.document.write(divContents);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}
