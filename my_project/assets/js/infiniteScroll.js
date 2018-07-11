import InfiniteScroll from 'infinite-scroll';

console.log("infinite scroll js works?");

$(document).ready(function () {

    var elem = document.querySelector('.container');
    var infScroll = new InfiniteScroll( elem, {
        // options
        path: function() {
            var pageNumber = ( this.loadCount + 1 );
            return '/post/infiniteScrollJSON?page=' + pageNumber;
        },

        history: false,
        responseType: 'text',
    });

    infScroll.on( 'load', function(response ) {
        // parse JSON

        var data = JSON.parse( response );
        // console.log(data);
        // do something with JSON...
        // console.log(data[0].id);
        var post = 0;
        for (post=0;post<data.length;post++){
            // console.log(data[post].id);


            $( ".container" ).append( '<img src="'+ data[post].coverImage +'">');
            $( ".container" ).append( "<div style='font-size: xx-large'> Post Title: "+data[post].title +"</div>");
            $( ".container" ).append( "<div> Published Date: "+data[post].publishedDate.date +"</div>");
            $( ".container" ).append( "<div> Short Content: "+data[post].shortContent +"</div>");
            $( ".container" ).append( '<a href="/post/'+data[post].id+'">read more</a>');
            $( ".container" ).append( "<hr>");
        }
    });
    infScroll.loadNextPage();
});



