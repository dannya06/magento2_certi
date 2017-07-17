$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            skipCheckbox = $("#skip-" + curStepBtn);
            isValid = true;

        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }


        if ((skipCheckbox).prop('checked') == true) {
            isValid = true;
            nextStepWizard.removeAttr('disabled').trigger('click');
        } else {
            if (isValid) {
                var $this = $(this);
                $this.button('loading');
                var ajaxRequest = curStepBtn + '.php';
                var ajaxParams = {};

                switch (curStepBtn) {
                    case 'step-3':
                        ajaxParams.storeCode = $('#theme-activation-store-code').val();
                        break;
                    case 'step-4':
                        ajaxParams.storeCode = $('#demo-configuration-store-code').val();
                        ajaxParams.demoVersion = $('#demo-configuration-version').val();
                        break;
                    case 'step-5':
                        ajaxParams.storeCode = $('#theme-configuration-store-code').val();
                        ajaxParams.homePage = $('#theme-configuration-home-page').val();
                        ajaxParams.header = $('#theme-configuration-header').val();
                        ajaxParams.categoryColumns = $('#theme-configuration-category-columns').val();
                        ajaxParams.productVersion = $('#theme-configuration-product-page').val();
                        ajaxParams.footer = $('#theme-configuration-footer').val();
                        break;
                }

                $.ajax({
                    type: "POST",
                    url: ajaxRequest,
                    // contentType: "application/json",
                    dataType: 'json',
                    data: ajaxParams,
                    success: function(data) {
                        $this.button('reset');
                        if (!data.error) {
                            nextStepWizard.removeAttr('disabled').trigger('click');
                            $('.result-container').append("<p class='success'>" + curStepBtn.toUpperCase() + ": <br/> " + data.msg + "</p>");
                        } else {
                            $('.result-container').append("<p class='error'>" + curStepBtn.toUpperCase() + ": <br/> "  + data.msg + "</p>");
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $('.result-container').append("<p class='error'>" + curStepBtn.toUpperCase() + ": <br/> " + " Server request error: " + errorThrown + "</p>");
                        $this.button('reset');
                    }
                });
            }
        }

    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});
