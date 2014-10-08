/**
 * Created by John on 9/24/14.
 */

if ($("#error").is(":empty")) {
    $("#error").hide();
}
else {
    $("#error").fadeIn();

}
$("form").submit(function (event) {

    event.preventDefault();
    $.ajax({
        url: "login",

        data: $("form").serialize(),

        type: "post",

        dataType: "json",

        success: function (data) {
            if (data.message == "ok") {
                $(location).attr("href", data.redirect);
                console.log(data);
            }
            else {
                $("#error").text(data.message).fadeIn();
            }
        }
    })
})
