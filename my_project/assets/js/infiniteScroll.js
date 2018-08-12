import InfiniteScroll from 'infinite-scroll';

var todaysDateOptions = { weekday: 'long', month: 'long', day: 'numeric' };
var todaysDate = new Date();

var month = todaysDate.getMonth();
var weekday = todaysDate.getUTCDay();
var monthday = todaysDate.getUTCDate();
console.log(monthday);
// var weekday = ({{ post.publishDate|date('N') }});
// var month = ({{ post.publishDate|date('m') }})-1;
// var year = ({{ post.publishDate|date('Y') }});


var months = ['sausio', 'vasario', 'kovo', 'balandžio', 'gegužės', "birželio","liepos", "rugpjūčio", "rugsėjo", "spalio", "lapkričio", "gruodžio"];
var weekdays = ["sekmadienis",'pirmadienis', "antradienis", "trečiadienis", "ketvirtadienis", "penktadienis", "šeštadienis" ];
todaysDate = months[month] + " "+monthday+" d., "+weekdays[weekday];
$( ".wrapper" ).append( "<p id='todays-date-line'>"+ todaysDate +"</p>");

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
            $( ".container" ).append( '<a href="/post/'+data['pages'][post].id+'"><img src="'+ data['pages'][post].coverImage +'"></a>');
            $( ".container" ).append( "<a href='/post/"+data['pages'][post].id+"'><div class='post-title' >"+data['pages'][post].title +"</div></a>");

            var date = (data['pages'][post].publishedDate.date);
            date = date.substr(0,date.indexOf(" "));
            var dateToConvert = date;
            dateToConvert = Date.parse(dateToConvert);
            dateToConvert = new Date(dateToConvert);
            console.log(dateToConvert);

            var month = dateToConvert.getMonth();
            var weekday = dateToConvert.getUTCDay();
            var monthday = dateToConvert.getUTCDate();

            var months = ['sausio', 'vasario', 'kovo', 'balandžio', 'gegužės', "birželio","liepos", "rugpjūčio", "rugsėjo", "spalio", "lapkričio", "gruodžio"];
            var weekdays = ["sekmadienis",'pirmadienis', "antradienis", "trečiadienis", "ketvirtadienis", "penktadienis", "šeštadienis" ];
            var postDate = months[month] + " "+monthday+" d., "+weekdays[weekday];

            console.log(dateToConvert);
            $( ".container" ).append( "<div class='post-date'>"+ postDate +"</div>");
            $( ".container" ).append( "<div class='post-shortcontent'>"+data['pages'][post].shortContent +"</div>");
            $( ".container" ).append( '<div class="post-readMore"><a href="/post/'+data['pages'][post].id+'" >skaityti</a></div>');
            $( ".container" ).append( "<hr>");




        }
    });

    infScroll.loadNextPage();
});



