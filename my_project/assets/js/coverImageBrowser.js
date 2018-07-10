function browseImages() {
   console.log(window.location.host);

   var imagesJsonURL =  "http://"+window.location.host+"/post/browseImages";
   var imagesJSON=[];
   var imageURL;


    $.getJSON(imagesJsonURL, function (list) {
        $.each(list, function (_idx, item) {
            if (typeof(item.folder) === 'undefined') {
                item.folder = 'Images';
            }
            if (typeof(item.thumb) === 'undefined') {
                imageURL = "http://" + window.location.host+item.image;

                $( ".images-thumbnails" ).append( '<img src='+imageURL+'>' );
            }
        });
        $( "body img" ).on( "click", function() {
            // alert($(this).attr('src'));
            $(window.parent.document).find('#post_coverImage')["0"].value=$(this).attr('src');
            overlay();
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        });
    });




}


browseImages();

// $('.images-thumbnails img').click(function()
// {
//     alert($(this).attr('src'));
// });

