var Script = function () {

    $('#pills').bootstrapWizard({'tabClass': 'nav nav-pills', 'debug': false, onShow: function(tab, navigation, index) {
        console.log('onShow');
    }, onNext: function(tab, navigation, index) {
        console.log('onNext pills');
    }, onPrevious: function(tab, navigation, index) {
        console.log('onPrevious');
    }, onLast: function(tab, navigation, index) {
        console.log('onLast');
    }, onTabClick: function(tab, navigation, index) {
        console.log('onTabClick');
    }, onTabShow: function(tab, navigation, index) {
        console.log('onTabShow');
        var $total = navigation.find('li').length;
        var $current = index+1;
        var $percent = ($current/$total) * 100;
        $('#pills').find('.bar').css({width:$percent+'%'});
    }});

    $('#tabsleft').bootstrapWizard({'tabClass': 'nav nav-tabs', 'debug': false, onShow: function(tab, navigation, index) {
        console.log('onShow');
    }, onNext: function(tab, navigation, index) {
        console.log('onNext tableft');
    }, onPrevious: function(tab, navigation, index) {
        console.log('onPrevious');
    }, onLast: function(tab, navigation, index) {
        console.log('onLast');
    }, onTabClick: function(tab, navigation, index) {
        console.log('onTabClick');

    }, onTabShow: function(tab, navigation, index) {
        console.log('onTabShow');
        var $total = navigation.find('li').length;
        var $current = index+1;
        var $percent = ($current/$total) * 100;
        $('#tabsleft').find('.bar').css({width:$percent+'%'});

        // If it's the last tab then hide the last button and show the finish instead
        if($current >= $total) {
            console.log("i am current");
            $('#tabsleft').find('.pager .next').hide();
            
            $('#tabsleft').find('.pager .finish').show();
            $('#tabsleft').find('.pager .finish').removeClass('disabled');
        } else {
            console.log("i am not current");
            $('#tabsleft').find('.pager .next').show();
            $('#tabsleft').find('.pager .finish').hide();
        }

    }});


    $('#tabsleft .finish').click(function() {
        alert('Finished!, Starting over!');
        $('#tabsleft').find("a[href*='tabsleft-tab1']").trigger('click');
    });

}();