import 'select2';



var parallax_height = $('.logoSection').css('height');
console.log(parallax_height);
if (parallax_height == null){
    document.getElementById("sidebar-section").classList.remove('fadeInlong');

    // $('.sidebar-section').classList.remove('fadeInlong');
    $('.sidebar-section').css('display','block');
}else{$(window).scroll(function() {
    parallax_height= parseInt(parallax_height)
    console.log(parallax_height);
    var windscroll = $(window).scrollTop();

    if (windscroll >= parallax_height-150) {
        $('.sidebar-section').css('display','block');
        console.log("scrolled");

    } else {

        $('.sidebar-section').css('display','none');
    }

})}




;


// In your Javascript (external .js resource or <script> tag)
$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        tags:true,

        });
    $('.js-example-basic-multiple').on('select2:selecting', function (e) {
        var data = ($('.js-example-basic-multiple').find("option[value='" + e.params.args.data.id+ "']"));
        var value = data[0].value;
        if (isNaN(parseInt(value)) ){
            console.log("this is new");
            e.preventDefault();
            $.ajax({
                url:"/hashtag/newhashtag/"+e.params.args.data.text,

                success: function (data) {
                    console.log(data);
                    e.params.args.data.id = data.id;
                    var newOption = new Option(e.params.args.data.text, e.params.args.data.id, true, true);
                    $('.js-example-basic-multiple').append(newOption).trigger('change');
                    console.log(e.params.args.data);
                }
            });
            $('.js-example-basic-multiple').select2({
                tags:true,

            });
            $(".js-example-basic-multiple").select2()
        }else {console.log("this is old");}

    });


});




