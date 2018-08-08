function browseImages() {
   console.log(window.location.host);

   var imagesJsonURL =  "http://"+window.location.host+"/post/browseimages";
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

           variable = ($(this).attr('id'));


            if (variable == "logosmallnavbar"){

            }else {

                variable = ($(this).parent().attr('class'));
                overlay();

                if(variable == 'images-thumbnails'){
                    $(window.parent.document).find('#post_coverImage')["0"].value = $(this).attr('src');
                    // console.log($(window.parent.document).find('#empty-post-cover-image').attr('src'));
                    console.log($(this).attr('src'));
                    document.getElementById('post-cover-image').src = $(this).attr('src');

                    document.body.scrollTop = 0; // For Safari
                    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera}
                    // document.post.submit();
                }
            }


        });
    });




}


browseImages();

// $('.images-thumbnails img').click(function()
// {
//     alert($(this).attr('src'));
// });

