let timer;
$(document).ready(function() {
    $(".result").on("click", function() {
        let id = $(this).attr("data-linkId");
        let url = $(this).attr("href");
        if(!id){
            console.log("data not found")
        }
        increaseLinkClicks(id, url);
        return false;
    })
    let grid = $(".imageResults");
    grid.on("layoutComplete", function() {
        $(".gridItem img").css("visibility", "visible")
    })
    grid.masonry({
        itemSelected: ".gridItem",
        columnWidth: 200,
        gutter: 5,
        initLayout: false
    });
    $("[data-fancybox]").fancybox({
        caption : function( instance, item ) {
            let caption = $(this).data('caption') || '';
            let siteUrl = $(this).data('siteUrl') || '';
            if ( item.type === 'image' ) {
                caption = (caption.length ? caption + '<br />' : '')
                    + '<a href="' + item.src + '">View image</a><br>'
                    + '<a href="' + siteUrl + '">Visit page</a>';
            }

            return caption;
        }
    });

    /* $(document).on('click', '.see-market-image', function(e){
        let id = $(this).attr('id').split('-')[1];
        console.log(e, id)
      });

      $(document).on('click', '.contact-product-owner', function(e){
        let id = $(this).attr('id').split('-')[1];
        console.log(e, id)
      }); */

})
function loadImage(src, className){
    let image = $("<img>");
    image.on("load", function(){
        $("." +  className + " a").append(image);
        clearTimeout(timer);
        timer = setTimeout(function() {
            $(".imageResults").masonry();
        }, 500);
    });
    image.on("error", function(){
        $("." +  className).remove();
        $.post("ajax/setBroken.php", {src: src});
    });
    image.attr("src", src);
}
function increaseLinkClicks(linkId, url){
    $.post("ajax/updateLinkCount.php", {linkId: linkId})
        .done(function(result) {
            if(result !== ""){
                alert(result)
                return;
            }
            window.location.href = url;
        })
}

function getProductPictures(productId) {
    $.post("ajax/productInfo.php", {productId: productId})
    .done(function(result) {
        if(result !== ""){
            let product = JSON.parse(result);
            $("#productDetailTitle").text(product.title);
            $("#productDetailPrice").text(product.price + " €");
            $("#productDetailDescription").html(product.description);
            $("#productDetailModal").modal('show');
            return;
        }
    })
}

function contactProductOwner(ownerId) {
    $.post("ajax/updateLinkCount.php", {ownerId: ownerId})
    .done(function(result) {
        if(result !== ""){
            alert(result)
            return;
        }
    })
}