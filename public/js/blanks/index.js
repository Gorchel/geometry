jQuery(document).ready(function($) {
    $("#doc-download").click(function(event) {
        var name = decodeURI(window.location.pathname);
        var o = {
            filename: name.replace('/kp/', '')+'.doc'
        };
        $(document).googoose(o);
    });
});
