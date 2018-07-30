import InfiniteScroll from 'infinite-scroll';

var todaysDateOptions = { weekday: 'long', month: 'long', day: 'numeric' };
var todaysDate = new Date();
todaysDate = todaysDate.toLocaleDateString('lt-LT', todaysDateOptions);
$( ".subtitle" ).append( "<p>"+ todaysDate +"</p>");

$('a[href*="#"]')

    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function(event) {
        // On-page links
        if (
            location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
            &&
            location.hostname == this.hostname
        ) {
            // Figure out element to scroll to
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            // Does a scroll target exist?
            if (target.length) {
                // Only prevent default if animation is actually gonna happen
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top-50
                }, 1000, function() {
                    // Callback after animation
                    // Must change focus!
                    var $target = $(target);
                    // $target.focus();
                    if ($target.is(":focus")) { // Checking if the target was focused
                        return false;
                    } else {
                        $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
                        // $target.focus(); // Set focus again
                    };
                });
            }
        }
    });

$(document).ready(function () {
    // var data = JSON.parse( response );
    var maxloadcount = 2;
    // console.log(data);
    // json('/post/infiniteScrollJSON');
    // var maxcount = JSON.parse('/post/infiniteScrollJSON');

    $.getJSON(
        '/post/infiniteScrollJSON',
        function(data){
            maxloadcount = data["maxLoadCount"];
        });

    var elem = document.querySelector('.container');
    var infScroll = new InfiniteScroll( elem, {
        // options
        path: function() {
            var pageNumber = ( this.loadCount + 1 );
            if (this.loadCount == maxloadcount){
                return false;
            }
            return '/post/infiniteScrollJSON?page=' + pageNumber;
        },
        history: false,
        responseType: 'text',
        status: '.page-load-status',
        checkLastPage: true,

    });
    infScroll.on( 'last', function( response, path ) {
        $( ".loader-ellips").hide();
        $( ".last-page" ).show();

    });

    infScroll.on( 'load', function(response ) {
        // parse JSON

        var data = JSON.parse( response );
        // console.log(data);
        var post = 0;
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

        for (post=0;post<data['pages'].length;post++){
            $( ".container" ).append( '<img src="'+ data['pages'][post].coverImage +'">');
            $( ".container" ).append( "<div class='post-title' style='font-size: 4vh'>"+data['pages'][post].title +"</div>");

            var date = new Date(data['pages'][post].publishedDate.date);

            date = date.toLocaleDateString('lt-LT', options);




            $( ".container" ).append( "<div class='post-date'>"+ date +"</div>");
            $( ".container" ).append( "<div class='post-shortcontent'>"+data['pages'][post].shortContent +"</div>");
            $( ".container" ).append( '<div class="post-readMore"><a href="/post/'+data['pages'][post].id+'" >skaityti</a></div>');
            $( ".container" ).append( "<hr>");
        }
    });

    infScroll.loadNextPage();
});



