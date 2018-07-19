import InfiniteScroll from 'infinite-scroll';




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
            $( ".container" ).append( "<div style='font-size: xx-large'> Post Title: "+data['pages'][post].title +"</div>");
            var date = new Date(data['pages'][post].publishedDate.date);
            date = date.toLocaleDateString('lt-LT', options);
            $( ".container" ).append( "<div>"+ date +"</div>");
            $( ".container" ).append( "<div>"+data['pages'][post].shortContent +"</div>");
            $( ".container" ).append( '<a href="/post/'+data['pages'][post].id+'">read more</a>');
            $( ".container" ).append( "<hr>");
        }
    });

    infScroll.loadNextPage();
});



