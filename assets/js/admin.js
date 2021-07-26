let discount_from_date = $("#discount_from_date").val();
let discount_to_date = $("#discount_to_date").val();
// let p = new persianDate();

$("#discount_from_date").persianDatepicker({
    formatDate: "YYYY/0M/0D",
    selectedBefore: discount_from_date.length ? 1 : 0,
    selectedDate: discount_from_date.length ? discount_from_date.replace(/-/gi, "/") : null
    // startDate: "today",
    // endDate: p.now().addYear(10).toString("YYYY/MM/DD")
});

$("#discount_to_date").persianDatepicker({
    formatDate: "YYYY/0M/0D",
    selectedBefore: discount_to_date.length ? 1 : 0,
    selectedDate: discount_to_date.length ? discount_to_date.replace(/-/gi, "/") : null
    // startDate: "today",
    // endDate: p.now().addYear(10).toString("YYYY/MM/DD")
});

jQuery(document).ready(function ($) {

    $("#discount_product").change(function () {
        let data = {
            discount_product: $('#discount_product').val(),
            action: 'ma_discount_get_data'
        };

        $.post(ajaxurl, data, function (response) {
            let ma_discount_product_price = document.getElementById("ma_discount_product_price");
            let ma_discount_product_image = document.getElementById("ma_discount_product_image");
            if ($('#discount_product').val() !== "") {
                ma_discount_product_price.style.display = "table-row";
                ma_discount_product_image.style.display = "table-row";
            } else {
                ma_discount_product_price.style.display = "none";
                ma_discount_product_image.style.display = "none";
            }

            $('#ma_discount_display_amount').html(response.ma_discount_product_price);
            $('#ma_discount_display_image').html('<img src="' + response.ma_discount_product_image + '" alt="Select a product" width="350" height="450">');
        }, 'json');
    });

    $("#ma_discount_show_product_after_sell").change(function () {
        let ma_discount_show_product_after_sell = document.getElementById("ma_discount_show_product_after_sell");
        let ma_discount_show_product_after_sell_number_of_day = document.getElementById("ma_discount_show_product_after_sell_number_of_day");
        let ma_discount_show_number_of_day_after_sell = document.getElementById("ma_discount_show_number_of_day_after_sell");

        if (ma_discount_show_product_after_sell.checked === true) {
            ma_discount_show_product_after_sell_number_of_day.style.display = "contents";
            ma_discount_show_number_of_day_after_sell.setAttribute('required', true);
        } else {
            ma_discount_show_product_after_sell_number_of_day.style.display = "none";
            ma_discount_show_number_of_day_after_sell.setAttribute('required', false);
        }
    });
});
