
var timer;

$(document).ready(function () {

    $(".result").on("click", function () {

        var id = $(this).attr("data-linkId");
        var url = $(this).attr("href");
    
        if (!id) {
            alert("data-linkId attr. was not found.");
        }

        increaseClick(id, url);
        return false;
    });


    //api for masonary boxes.

    var grid = $(".imgResults");

    grid.on("layoutComplete", function () {

        $(".itemInGrid img").css("visibility", "visible");
    })

    grid.masonry({
        itemSelector: ".itemInGrid",
        columnWidth: 200,
        gutter: 5,
        isInitLayout: false

    });

    //custom fancybox api to allow more text items for imgs. (links added)
    $("[data-fancybox]").fancybox({

        caption: function (instance, item) {
            var caption = $(this).data('caption') || '';
            var websiteURL = $(this).data('websiteurl') || '';

            if (item.type === 'image') {
                caption = (caption.length ? caption + '<br />' : '')
                    + '<a href="' + item.src + '">View image</a><br>'
                    + '<a href="' + websiteURL + '">Visit Page</a><br>';
            }

            return caption;
        },
        //incease image clicks 
        afterShow: function (instance, item) {


            increaseImgClick(item.src);
           
        }




    });
 

});

//load the images into the image tab.

function loadImg(src, className) {
    var img = $("<img>");

    img.on("load", function () {
        $("." + className + " a").append(img);

        clearTimeout(timer);

        timer = setTimeout(function () {
            $(".imgResults").masonry();

        }, 500);

       

    });

    img.on("error", function () {

        $("." + className).remove();

        $.post("ajax/brokenLinkSet.php", { src: src });

    })

    img.attr("src", src);
}

//function for increase popularity click for links
function increaseClick(linkId, url) {

    $.post("ajax/updateLinkCount.php", { linkId: linkId }).done(function(result) {

        if (result != "") {
            alert(result);
            return;
        }
        window.location.href = url;
    });
}

//function for increase popularity click for imgs

function increaseImgClick(imgURL) {

    $.post("ajax/updateImgCount.php", { imgURL: imgURL }).done(function (result) {

        if (result != "") {
            alert(result);
            return;
        }
       
    });
}