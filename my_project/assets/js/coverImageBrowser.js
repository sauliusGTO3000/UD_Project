function browseImages() {
   console.log("lets make it work");

   var imagesJsonURL =  "http://127.0.0.1:8000/post/browseImages";
   var imagesJSON=[];
   var imageURL;


    $.getJSON(imagesJsonURL, function (list) {
        $.each(list, function (_idx, item) {
            if (typeof(item.folder) === 'undefined') {
                item.folder = 'Images';
            }
            if (typeof(item.thumb) === 'undefined') {
                imageURL = "http://127.0.0.1:8000" + item.image;
                console.log(imageURL);
                $( ".images-thumbnails" ).append( '<img src='+imageURL+'>' );
            }
        });
        $( "body img" ).on( "click", function() {
            // alert($(this).attr('src'));
            $(window.parent.document).find('#post_coverImage')["0"].value=$(this).attr('src');
            overlay();
        });
    });




}


browseImages();

// $('.images-thumbnails img').click(function()
// {
//     alert($(this).attr('src'));
// });

