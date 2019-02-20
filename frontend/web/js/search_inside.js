$( function() {
    var baseurl=$('#uribase').val();
    var customRenderMenu = function(ul, items){
        var self = this;
        var categoryArr = [];

        function contain(item, array) {
            var contains = false;
            $.each(array, function (index, value) {
                if (item == value) {
                    contains = true;
                    return false;
                }
            });
            return contains;
        }

        $.each(items, function (index, item) {
            if (! contain(item.category, categoryArr)) {
                categoryArr.push(item.category);
            }
            console.log(categoryArr);
        });

        $.each(categoryArr, function (index, category) {
            ul.append("<h2>" + category + "</h2>");
            $.each(items, function (index, item) {
                if (item.category == category) {
                    self._renderItemData(ul, item);
                }
            });
        });
    };

    $("#search-legal-users").click(function() { $(this).autocomplete("search", ""); });

    $( "#search-legal-users" ).autocomplete({
        minLength: 0,
        source: function( request, response ){
            $.ajax({
                url: baseurl+'/hospital/get-search-list',
                dataType:   'json',
                method:     'POST',
                data: { term: request.term },
                success: function(data){
                    //console.log(data);
                    response( $.map( data, function( item )   {
                        return{
                            label: item.label,
                            value: item.id,
                            category: item.category,
                            slug:item.query,
                            filters:item.filters,
                            avator:item.avator,
                            city:item.city,
                            category_check:item.category_check
                        }
                    }));
                }
            });
        },
        create: function () {
            //access to jQuery Autocomplete widget differs depending
            //on jQuery UI version - you can also try .data('autocomplete')
            $(this).data('uiAutocomplete')._renderMenu = customRenderMenu;
        },
        focus: function( event, ui ) {
           // $( "#search-legal-users" ).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $( "#search-legal-users" ).val( ui.item.label);
            $('#main-js-preloader').show();
            $.ajax({
                url: baseurl+'/hospital/get-detailurl',
                dataType:   'json',
                method:     'POST',
                data: { id: ui.item.value,filter: ui.item.filters,slug: ui.item.slug,search_type:ui.item.category_check},
                success: function(response){
                    if(response.result == 'success'){
                        if(response.fullpath == 1){
                            url = baseurl+ response.path + ui.item.slug;
                            window.location.replace(url);
                        }
                        else{
                            $('#main-js-preloader').hide();
                            url = baseurl+ response.path + ui.item.slug;
                            window.location.replace(url);
                        }

                    }
                    else{
                        url = baseurl;
                        window.location.replace(url);
                    }
                }
            });
            return false;

        }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        if(item.label != ''){
            var simpletext = new RegExp(this.term,"ig");
            var yellowtext = "<span style='color:#ff0000'>" + this.term + "</span>";
            var re = new RegExp($.trim(this.term.toLowerCase()));
            var labelnew = item.label.replace(simpletext,yellowtext);
            if(item.category_check == 'Groups'){
                return $( "<li>" )
                    .append( "<a><span class=\"pull-left\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i>" + labelnew + "</span></a>" )
                    .appendTo( ul );
            }
            else if(item.category_check == 'Search'){
                return $( "<li>" )
                    .append( "<a><span class=\"pull-left\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i>" + labelnew + "</span></a>" )
                    .appendTo( ul );
            }
            else if(item.category_check == 'Category'){
                return $( "<li>" )
                    .append( "<a><span class=\"pull-left\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i>" + labelnew + "</span></a>" )
                    .appendTo( ul );
            }
            else if(item.category_check == 'SubCategory'){
                return $( "<li>" )
                    .append( "<a><span class=\"pull-left\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i>" + labelnew + "</span></a>" )
                    .appendTo( ul );
            }
            else{
                if(item.avator == null){
                    return $( "<li>" )
                        .append( "<a><i><img src=\"images/doctor-profile-image.jpg\" class=\"img-responsive\" alt=\"img\"/></i>" + labelnew + "<span class=\"town-text\">"+ item.city +"</span></a>" )
                        .appendTo( ul );
                }
                else{
                    return $( "<li>" )
                        .append( "<a><i><img src="+item.avator+" class=\"img-responsive\" alt=\"img\"/></i>" + labelnew + "<span class=\"town-text\">"+ item.city +"</span></a>" )
                        .appendTo( ul );

                }
            }
        }
        else{
            return $( "<li>" )
                .append( "<a><span class=\"pull-left\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i>" + item.filters + "</span></a>" )
                .appendTo( ul );
        }
    };

    $(".search_btn_click").click(function(){
        $('#main-js-preloader').show();
        var field_search= $('#search-legal-users').val();
        var location_search= $('#txtPlaces').val();
        $.ajax({
            url: baseurl+'/hospital/get-searchurl',
            dataType:   'json',
            method:     'POST',
            data: { field_search: field_search,location_search: location_search},
            success: function(response){
                if(response.result == 'success'){
                        url = baseurl+ response.path;
                        //window.location.replace(url);
                }
                else{
                    url = baseurl;
                    //window.location.replace(url);
                }
            }
        });
        return false;

    });

} );

$("a.see_more_sch").click(function(){
    $(".slick-active .clicktodisplay").css("display", "block");
});

$(".slick-next").click(function(){
    $(".slick-active .clicktodisplay").css("display", "none");
});

$('.movenext').click(function(){
    $(".calendar").slick('slickNext');
});

$('.get_current_location').click(function(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(savePosition, positionError, {timeout:10000});
    } else {
        //Geolocation is not supported by this browser
    }
});

$('select#sel_lan').on('change',function(){
    $('#main-js-preloader').show();
    var url=window.location.href;
    var val=this.value;
    if(val == ''){
        var newurl=removeQString('language');
    }
    else{
        var newurl=updateQueryStringParameter(url,'language',val);
    }
    window.location.href=newurl;
});

$('select#sel_avail').on('change',function(){
    $('#main-js-preloader').show();
    var url=window.location.href;
    var val=this.value;
    if(val == ''){
        var newurl=removeQString('availabilities');
    }
    else{
        var newurl=updateQueryStringParameter(url,'availabilities',val);
    }
    window.location.href=newurl;
});

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function removeQString(key) {
    var urlValue=document.location.href;

    //Get query string value
    var searchUrl=location.search;

    if(key!="") {
        oldValue = getParameterByName(key);
        removeVal=key+"="+oldValue;
        if(searchUrl.indexOf('?'+removeVal+'&')!= "-1") {
            urlValue=urlValue.replace('?'+removeVal+'&','?');
        }
        else if(searchUrl.indexOf('&'+removeVal+'&')!= "-1") {
            urlValue=urlValue.replace('&'+removeVal+'&','&');
        }
        else if(searchUrl.indexOf('?'+removeVal)!= "-1") {
            urlValue=urlValue.replace('?'+removeVal,'');
        }
        else if(searchUrl.indexOf('&'+removeVal)!= "-1") {
            urlValue=urlValue.replace('&'+removeVal,'');
        }
    }
    else {
        var searchUrl=location.search;
        urlValue=urlValue.replace(searchUrl,'');
    }
    return urlValue;
}

function positionError(error) {
    var errorCode = error.code;
    var message = error.message;
    alert(message);
}

function savePosition(position) {
    latitude=position.coords.latitude;
    longitude=position.coords.longitude;
    $.ajax({
        url: baseurl+'/search/set-location-cookie-navigation',
        dataType:   'json',
        method:     'POST',
        data: { lat: latitude,lng: longitude},
        success: function(response){
            if(response.type == 'success'){
                $('input#txtPlaces').val(response.city);
                console.log(response);
            }
            else{
            }
        }
    });
}

