$(document).ready(function () {
    /**
     * Toggle compileerror
     */
    $(".showcompileerror a, .hidecompileerror a").click(function() {
        $(this).closest("td").find(".showcompileerror , .hidecompileerror").toggle();
        var attempt = $(this).closest("tr").attr("data-for-attempt");
        $(this).closest("tr").next(".other").find(".compileerror").slideToggle();
        return false;
    });
    /**
     * Toggle input
     */
    $(".hideinput a, .showinput a").click(function() {
        $(this).closest(".test").find(".hideinput, .showinput").toggle();
        $(this).closest(".test").find(".input").slideToggle();
        return false;
    });
    /**
     * Show all input
     */
    $(".showallinput a").click(function() {
        $(this).closest("tr").find(".hideallinput, .showallinput").toggle();
        $(this).closest("tr").next(".other").find(".showinput").hide();
        $(this).closest("tr").next(".other").find(".hideinput").show();
        $(this).closest("tr").next(".other").find(".input").slideDown();
        return false;
    });
    /**
     * Hide all input
     */
    $(".hideallinput a").click(function() {
        $(this).closest("tr").find(".hideallinput, .showallinput").toggle();
        $(this).closest("tr").next(".other").find(".showinput").show();
        $(this).closest("tr").next(".other").find(".hideinput").hide();
        $(this).closest("tr").next(".other").find(".input").slideUp();
        return false;
    });
    /**
     * Hide tests
     */
    $(".hidetests a").click(function() {
        $(this).closest("tr").find(".hidetests, .showtests").toggle();
        $(this).closest("tr").find(".hideallinput, .showallinput").hide();
        $(this).closest("tr").next(".other").slideUp();
        return false;
    });
    /**
     * Show tests
     */
    $(".showtests a").click(function() {
        $(this).closest("tr").find(".hidetests, .showtests").toggle();
        $(this).closest("tr").find(".showallinput").show();
        $(this).closest("tr").next(".other").find(".showinput").show();
        $(this).closest("tr").next(".other").find(".hideinput").hide();
        $(this).closest("tr").next(".other").find(".input").hide();
        $(this).closest("tr").next(".other").slideDown();
        return false;
    });
    /**
     * Hide all info about attempts
     */
    $(".hideall a").click(function() {
        $(".hideall, .showall").toggle();
        $(".hidecompileerror:visible a").click();
        $(".hideallinput:visible a").click();
        $(".hidetests:visible a").click();
        return false;
    });
    /**
     * Show all info about attempts
     */
    $(".showall a").click(function() {
        $(".hideall, .showall").toggle();
        $(".showtests:visible a").click();
        $(".showallinput:visible a").click();
        $(".showcompileerror:visible a").click();
        return false;
    });

    /**
     * Ignore all attempts younger than current
     */
    $(".testresults .ok input").click(function() {
        var attempt = parseInt($(this).closest("tr").attr("data-attempt"));
        $(".testresults tr").each(function() {
            var thisattempt = parseInt($(this).attr("data-attempt"));
            if (thisattempt > attempt) {
                $(this).find(".ignor input").click();
            }
        });
    });

    /**
     * Highlight active cell
     */
    $(".testresults input[type=radio]").click(function() {
        $(this).closest("tr").find("input[type=radio]").not(this).closest("td").removeClass("chosen");
        $(this).closest("td").addClass("chosen");
    });

    $(".testresults input[type=radio][checked]").closest("td").addClass("chosen");
});