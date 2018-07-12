import InfiniteScroll from 'infinite-scroll';

console.log("infinite scroll js works?");


$(document).ready(function () {
    // var data = JSON.parse( response );
    var maxloadcount = 1;
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
        console.log("last page reached");
    });

    infScroll.on( 'load', function(response ) {
        // parse JSON

        var data = JSON.parse( response );
        // console.log(data);
        var post = 0;
        for (post=0;post<data['pages'].length;post++){
            $( ".container" ).append( '<img src="'+ data['pages'][post].coverImage +'">');
            $( ".container" ).append( "<div style='font-size: xx-large'> Post Title: "+data['pages'][post].title +"</div>");
            $( ".container" ).append( "<div> Published Date: "+data['pages'][post].publishedDate.date +"</div>");
            $( ".container" ).append( "<div> Short Content: "+data['pages'][post].shortContent +"</div>");
            $( ".container" ).append( '<a href="/post/'+data['pages'][post].id+'">read more</a>');
            $( ".container" ).append( "<hr>");
        }
    });

    infScroll.loadNextPage();
});



