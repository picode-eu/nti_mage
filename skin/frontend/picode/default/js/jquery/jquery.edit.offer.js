jQuery(document).ready(function()
{
    // upload product images
    var id;
    jQuery(document).on('click', 'span.button', function(){
    	jQuery('#image-upload').find('input[type="file"]').val('');
        id = jQuery(this).attr('id');
        jQuery('#input_' + id).click();
    });

    jQuery(document).on('change', '#image-upload', function() {
    	var imageUrl = getSkinImagesUrl('loader.gif');
        jQuery('#orig_' + id).hide();
        jQuery('#new_' + id).show().html('<img class="logo-upload-loader" src="' + imageUrl + '" />');
        jQuery('#' + id).removeClass('new-image').addClass('change-image').text('Modifica');
        jQuery('#' + id).closest('.image-holder').find('.delete-image').show();
        //console.log(target);

        jQuery("#image-upload").ajaxForm({
           target: '#new_' + id
        }).submit();

    });

    // delete existing/uploaded image
    jQuery(document).on('click', 'span.delete-image', function(){
        // console.log('clicked');
        var noImageSrc = getMediaUrl() + 'catalog/product/placeholder/default/image.jpg';
        var parent = jQuery(this).closest('.image-holder');
        if (parent.find('input.delete-image-input').length <= 0) {
            parent.append('<input class="delete-image-input" type="hidden" name="delete_orig_image[]" value="' + parent.find('span.change-image').attr('id') + '" />');
        }
        parent.find('img.product-image').attr('src', noImageSrc);
        parent.find('span.button').text('Selecteaza');
    });

    // check/uncheck hidden checkbox on personalized zones change
    var checked;
    jQuery(document).on('click', 'input[type="checkbox"]', function(){
    	var group = jQuery(this).closest('.input-box');
        var hiddenCheckbox = jQuery('#' + jQuery(this).attr('groupid') + '_hidden');;
        //var hiddenCheckbox = group.closest('.field').find('#' + hiddenName);
        //var hiddenCheckbox = jQuery('#' + hiddenName);
        checked = false;

        group.find('input[type="checkbox"]').each(function(e){
            //console.log(jQuery(this).attr('id'));
            if (jQuery(this).is(':checked')) {
                //console.log(jQuery(this).attr('id'));
                checked = true;
            }
        });

        if (checked && !hiddenCheckbox.is(':checked')) {
            //console.log('hidden checked');
            hiddenCheckbox.prop('checked', true);
        } else if (!checked && hiddenCheckbox.is(':checked')) {
            //console.log('hidden un-checked');
            hiddenCheckbox.prop('checked', false);
        }

        // if (!hiddenCheckbox.is(':checked')) {
            // console.log('hidden is un-cehcked');
        // } else {
            // console.log('hidden is checked');
        // }
    });

    // check/uncheck hidden checkbox on window load
    var isPersZones = getPersonalizedZones();
    //console.log(isPersZones.length);

    if (isPersZones.length) {
        //console.log('hidden is checked');
        jQuery('#ofg_zona_personalizata_hidden').prop('checked', true);
    } else {
        //console.log('hidden is un-cehcked');
        jQuery('#ofg_zona_personalizata_hidden').prop('checked', false);
    }

    // validate and submit the form
    jQuery(document).on('click', '.next-step', function(ev){

        var form = jQuery(this).closest('.oferta-form');
        var validForm = new VarienForm(form.attr('id'), false);
        var validation = validForm.validator.validate();

        if (validation) {
            // start submiting the form
            jQuery(form).submit(function(e)
            {
                var postData = form.serializeArray();
                var formUrl = form.attr('action');
                var method = form.attr('method');
                var currentStep = form.closest('.step-wrapper');

                // show loading image
                form.find('.please-wait').show();

                jQuery.ajax(
                {
                    url : formUrl,
                    type: method,
                    data : postData,

                    success:function(data, textStatus, jqXHR)
                    {
                        if (textStatus == 'success' && data != 'error') {
                            //console.log('success');
                            if (data != 'last_step') {
	                            currentStep.find('.form-list').slideUp(function(){
	                                // hide loading image
	                                form.find('.please-wait').hide();
	                                // scroll to active step
	                                jQuery('html, body').animate({scrollTop:form.closest('.step-wrapper').position().top + 170}, 'slow');
	                                form.closest('.step-wrapper').focus();
	                                // slide down next step
	                                jQuery('#' + data).slideDown('slow').closest('.step-wrapper').addClass('visible');
	                                // clear error message and red borders
	                                form.find('.form-error').hide().text('');
	                                if (jQuery('#ofg_zona_personalizata_hidden').hasClass('validation-passed')) {
	                                    jQuery('#group').css('border', '1px solid silver');
	                                } else {
	                                    jQuery('#group').css('border', '1px solid #ff0000');
	                                }
	                            });
	                        } else {
                                // is last step
                                window.location.replace(getSaveFormAction());
                            }

                        } else if (data == 'error') {
                        	// alert an error message
                        	form.find('.please-wait').hide();
                        	form.find('.form-error').show().text('Exista campuri obligatorii necompletate.');
                        	jQuery('html, body').animate({scrollTop:form.closest('.step-wrapper').position().top + 170}, 'slow');
                            form.closest('.step-wrapper').focus();
                        }
                    },

                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        //if fails
                        form.find('.form-error').text('Erorare la salvarea datelor. incercati mai tarziu.');
                        console.log(errorThrown);
                    }
                });

                e.preventDefault(); //STOP default action
            });
        } else {
            if (jQuery('#ofg_zona_personalizata_hidden').hasClass('validation-passed')) {
                jQuery('#group').css('border', '1px solid silver');
            } else {
                jQuery('#group').css('border', '1px solid #ff0000');
            }
            ev.preventDefault(); //STOP default action
        }

    });

    // go back one step action
    jQuery(document).on('click', '.back-one-step', function(){
        var currentStep = jQuery(this).closest('.step-wrapper');
        //jQuery('html, body').animate({ scrollTop: 180 }, 'slow');

        currentStep.find('.form-list').slideUp('slow', function(){
            var prevStep = currentStep.prev('.step-wrapper');

            if (prevStep.hasClass('hidden')) {
                beforePrevStep = prevStep.prev('.step-wrapper');
                jQuery('html, body').animate({scrollTop:beforePrevStep.position().top + 170}, 'slow', function(){
                    beforePrevStep.focus();
                    beforePrevStep.find('.form-list').slideDown();
                });

            } else {
                jQuery('html, body').animate({scrollTop:prevStep.position().top + 170}, 'slow', function(){
                    prevStep.focus();
                    prevStep.find('.form-list').slideDown();
                });
            }

        });
    });

    // show/hide foto-video sections
    jQuery(document).on('change', 'select#ofg_tip_oferta', function(){
    	//alert('changed');
    	// var formUrl = getBaseFormUrl();
        var fotoWrapper = jQuery('#detalii-foto-wrapper');
        var videoWrapper = jQuery('#detalii-video-wrapper');
        var adjustsNextStep = jQuery('#sedinte_foto_video_form').find('input[name="next_step"]');
        var sedinteWrapper = jQuery('#sedinte-foto-video-wrapper').find('.legend');

        if (jQuery(this).val() == '1') { // foto
            jQuery('#ofg_nr_fotografi').prop('disabled', false).closest('.field').removeClass('hidden').show();
            jQuery('#ofg_nr_cameramani').prop('disabled', true).closest('.field').addClass('hidden').hide();
            //fotoWrapper.find('form').attr('action', formUrl + 'saveoferta/');
            fotoWrapper.find('input[name="next_step"]').val('');
            fotoWrapper.find('form button').attr('title', 'Salveaza oferta').text('Salveaza oferta');
            fotoWrapper.removeClass('hidden');
            videoWrapper.addClass('hidden');
            adjustsNextStep.val('detalii-foto');
            sedinteWrapper.text('Sedinte FOTO programate');
        } else if (jQuery(this).val() == '2') { // video
            jQuery('#ofg_nr_fotografi').prop('disabled', true).closest('.field').addClass('hidden').hide();
            jQuery('#ofg_nr_cameramani').prop('disabled', false).closest('.field').removeClass('hidden').show();
            fotoWrapper.addClass('hidden');
            //fotoWrapper.find('form').attr('action', formUrl + 'ajaxform/');
            fotoWrapper.find('input[name="next_step"]').val('detalii-video');
            fotoWrapper.find('form button').attr('title', 'Pasul urmator').text('Pasul urmator');
            videoWrapper.removeClass('hidden');
            adjustsNextStep.val('detalii-video');
            sedinteWrapper.text('Sedinte VIDEO programate');
        } else if (jQuery(this).val() == '3') { // foto-video
            jQuery('#ofg_nr_fotografi').prop('disabled', false).closest('.field').removeClass('hidden').show();
            jQuery('#ofg_nr_cameramani').prop('disabled', false).closest('.field').removeClass('hidden').show();
            //fotoWrapper.find('form').attr('action', formUrl + 'ajaxform/');
            fotoWrapper.find('input[name="next_step"]').val('detalii-video');
            fotoWrapper.find('form button').attr('title', 'Pasul urmator').text('Pasul urmator');
            fotoWrapper.removeClass('hidden');
            videoWrapper.removeClass('hidden');
            adjustsNextStep.val('detalii-foto');
            sedinteWrapper.text('Sedinte FOTO-VIDEO programate');
        } else {
            jQuery('#ofg_nr_fotografi').prop('disabled', true).closest('.field').addClass('hidden').hide();
            jQuery('#ofg_nr_cameramani').prop('disabled', true).closest('.field').addClass('hidden').hide();
            fotoWrapper.addClass('hidden');
            videoWrapper.addClass('hidden');
            //fotoWrapper.find('form').attr('action', formUrl + 'ajaxform/');
            fotoWrapper.find('input[name="next_step"]').val('detalii-video');
            fotoWrapper.find('form button').attr('title', 'Pasul urmator').text('Pasul urmator');
            adjustsNextStep.val('');
            sedinteWrapper.text('Sedinte programate');
        }
    });

});

function updateNextField(input, update, add_1, add_2)
{
    var clicked = jQuery(input);
    //console.log(clicked.attr('id'));
    var update = jQuery('#' + update);
    var add_1 = jQuery('#' + add_1);
    var add_2 = jQuery('#' + add_2);

    switch (clicked.attr('id'))
    {
        // custom select or yes/no => input type text
        case 'oferta_speciala':
        case 'ofg_restaurant_panala':
        case 'ofv_format_filmare':
        case 'ofv_videoclip':
            var parent = update.closest('.field');

            if (clicked.val() == 'alt-moment' || clicked.val() == 'alt-format' || clicked.val() == '1') {
                update.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                parent.find('label').addClass('required');
                parent.find('em').text('*');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.field');
                    add_1.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                    addParent.find('label').addClass('required');
                    addParent.find('em').text('*');
                }
                // second additional update
                if (add_2.length) {
                    var addParent = add_2.closest('.field');
                    add_2.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                    addParent.find('label').addClass('required');
                    addParent.find('em').text('*');
                }

            } else {
                update.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                parent.find('label').removeClass('required');
                parent.find('em').text('');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.field');
                    add_1.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                    addParent.find('label').removeClass('required');
                    addParent.find('em').text('');
                }
                // second additional update
                if (add_2.length) {
                    var addParent = add_2.closest('.field');
                    add_2.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                    addParent.find('label').removeClass('required');
                    addParent.find('em').text('');
                }
            }
            break;

        // yes/no select => custom select + input type text
        case 'off_dvd':
        case 'ofv_montaj_dvd':
        case 'ofv_montaj_blu_ray':
        case 'ofv_montaj_film':
        case 'ofv_videoclip':
            var parent = update.closest('.field');
            update.find('option').removeAttr('selected');

            if (clicked.val() == '1') {
                update.val('0');
                update.find('option[value="0"]').attr('selected', true);
                update.removeAttr('disabled').removeClass('disabled').addClass('validate-select')
                parent.find('label').addClass('required');
                parent.find('em').text('*');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.field');
                    add_1.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                    addParent.find('label').addClass('required');
                    addParent.find('em').text('*');
                }
            } else {
                update.val('');
                update.attr('disabled', 'disabled').addClass('disabled').removeClass('validate-select')
                parent.find('label').removeClass('required');
                parent.find('em').text('');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.field');
                    add_1.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                    addParent.find('label').removeClass('required');
                    addParent.find('em').text('');
                }
            }
            break;

        // yes/no select => input type textarea
        case 'off_slide_show':
            var parent = update.closest('.wide');

            if (clicked.val() == '1') {
                update.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                parent.find('label').addClass('required');
                parent.find('em').text('*');
            } else {
                update.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                parent.find('label').removeClass('required');
                parent.find('em').text('');
            }
            break;

        // yes/no select => custom select + input type textarea
        case 'off_album_clasic':
        case 'off_album_carte':
            var parent = update.closest('.wide');
            update.find('option').removeAttr('selected');

            if (clicked.val() == '1') {
                update.val('0');
                update.find('option[value="0"]').attr('selected', true);
                update.removeAttr('disabled').removeClass('disabled').addClass('validate-select')
                parent.find('label').addClass('required');
                parent.find('em').text('*');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.wide');
                    add_1.removeAttr('disabled').removeClass('disabled').addClass('required-entry').val('');
                    addParent.find('label').addClass('required');
                    addParent.find('em').text('*');
                }
            } else {
                update.val('');
                update.attr('disabled', 'disabled').addClass('disabled').removeClass('validate-select')
                parent.find('label').removeClass('required');
                parent.find('em').text('');
                // additional updates
                if (add_1.length) {
                    var addParent = add_1.closest('.wide');
                    add_1.attr('disabled', 'disabled').addClass('disabled').removeClass('required-entry').val('');
                    addParent.find('label').removeClass('required');
                    addParent.find('em').text('');
                }
            }

            break;
    }
}

function autoCheck(group, hiddenCheckbox)
{
    group.find('input[type="checkbox"]').each(function(){
        //console.log(jQuery(this).attr('id'));
        if (jQuery(this).is(':checked')) {
            hiddenCheckbox.attr('checked', 'checked');
        } else {
            hiddenCheckbox.removeAttr('checked');
        }
    });
}

(function($){
    $.mlp = {x:0,y:0}; // Mouse Last Position
    function documentHandler(){
        var $current = this === document ? $(this) : $(this).contents();
        $current.mousemove(function(e){jQuery.mlp = {x:e.pageX,y:e.pageY}});
        //$current.find("iframe").load(documentHandler);
    }

    $(documentHandler);

    $.fn.ismouseover = function(overThis) {
        var result = false;
        this.eq(0).each(function() {
                // var $current = $(this).is("iframe") ? $(this).contents().find("body") : $(this);
                var $current = $(this);
                var offset = $current.offset();
                result =    offset.left<=$.mlp.x && offset.left + $current.outerWidth() > $.mlp.x &&
                            offset.top<=$.mlp.y && offset.top + $current.outerHeight() > $.mlp.y;
        });
        return result;
    };
})(jQuery);